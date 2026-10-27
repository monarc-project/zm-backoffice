<?php declare(strict_types=1);

namespace Monarc\BackOffice\Scenario\Service;

use Monarc\BackOffice\Scenario\Validator\CatalogRequestValidator;
use Monarc\Core\Scenario\Table\CatalogTable;
use Ramsey\Uuid\Uuid;
use RuntimeException;

final class CatalogService
{
    public function __construct(
        private CatalogTable $catalogTable,
        private CatalogAuthorizationService $authorization
    ) {
    }

    public function list(string $kind, array $query): array
    {
        $this->authorization->userId();
        $page = max(1, min(10000, (int) ($query['page'] ?? 1)));
        $pageSize = max(1, min(100, (int) ($query['pageSize'] ?? 25)));
        $locale = $this->locale($query['locale'] ?? null);
        // The BackOffice library is an authoring workspace. Its default list
        // must include draft and review versions so an interrupted workflow
        // can be resumed after a browser refresh.
        $where = '1 = 1';
        $parameters = [];
        if (isset($query['lifecycle'])
            && in_array(
                $query['lifecycle'],
                ['draft', 'review', 'published', 'deprecated'],
                true
            )
        ) {
            $where = 'c.lifecycle = :lifecycle';
            $parameters['lifecycle'] = $query['lifecycle'];
        }
        if (isset($query['search']) && is_string($query['search']) && $query['search'] !== '') {
            $where .= ' AND EXISTS (SELECT 1 FROM ' . $this->translationTable($kind) . ' t '
                . 'WHERE t.' . $this->foreignKey($kind) . ' = c.id AND t.title LIKE :search)';
            $parameters['search'] = '%' . mb_substr($query['search'], 0, 100) . '%';
        }
        $pageResult = $this->catalogTable->list(
            $kind,
            $where,
            $parameters,
            $page,
            $pageSize
        );
        $pageResult['items'] = array_map(
            fn (array $item): array => $this->presentListedResource($kind, $item, $locale),
            $pageResult['items']
        );

        return $pageResult;
    }

    public function read(string $kind, string $uuid, string $locale, ?int $version = null): array
    {
        $this->authorization->userId();
        $resource = $this->current($kind, $uuid, $version);
        $translations = $this->catalogTable->findTranslations($kind, (int) $resource['id']);
        $resource['translations'] = array_map(
            fn (array $translation): array => $this->presentTranslation($translation),
            $translations
        );
        $resource['translation'] = $this->resolveTranslation($translations, $locale);
        if ($kind === 'template') {
            $resource['risk_scenario_models'] = $this->catalogTable->findTemplateRiskModels((int) $resource['id']);
        }

        return $this->presentResource($resource);
    }

    public function versions(string $kind, string $uuid, string $locale): array
    {
        $this->authorization->userId();
        $versions = $this->catalogTable->findVersions($kind, $uuid);
        if ($versions === []) {
            throw new RuntimeException('Catalog resource not found.');
        }
        $result = [];
        foreach ($versions as $version) {
            $translations = $this->catalogTable->findTranslations($kind, (int) $version['id']);
            $version['translation'] = $this->resolveTranslation($translations, $locale);
            if ($kind === 'template') {
                $version['risk_scenario_models'] = $this->catalogTable->findTemplateRiskModels((int) $version['id']);
            }
            $result[] = $this->presentResource($version);
        }
        if ($result === []) {
            throw new RuntimeException('Catalog resource not found.');
        }
        return $result;
    }

    public function createDraft(string $kind, array $data, string $correlationId): array
    {
        CatalogRequestValidator::write($data, $kind);
        $userId = $this->authorization->userId();
        $uuid = Uuid::uuid4()->toString();

        return $this->insertDraft($kind, $uuid, 1, $data, $userId, $correlationId);
    }

    public function updateDraft(string $kind, string $uuid, array $data, int $revision, string $correlationId): array
    {
        CatalogRequestValidator::write($data, $kind);
        $userId = $this->authorization->userId();
        $current = $this->current($kind, $uuid);
        if ((int) $current['revision'] !== $revision) {
            throw new RuntimeException('Scenario catalog revision conflict.');
        }
        if ($current['lifecycle'] !== 'draft') {
            // Immutable published/reviewed content is copied into the next draft version.
            // Model UUIDs are deliberately version-local, so a successor gets
            // fresh identities even when its visible structure is unchanged.
            $data = $this->successorDraftData($kind, $current, $data);
            if ($kind === 'template' && isset($data['riskScenarioModels'])) {
                $data['riskScenarioModels'] = array_map(
                    static function (array $model): array {
                        unset($model['stableUuid']);

                        return $model;
                    },
                    $data['riskScenarioModels']
                );
            }
            return $this->insertDraft(
                $kind,
                $uuid,
                (int) $current['version'] + 1,
                $data,
                $userId,
                $correlationId
            );
        }
        $this->catalogTable->transactional(function () use (
            $kind,
            $current,
            $data,
            $revision,
            $uuid,
            $userId,
            $correlationId
        ): void {
            if (!$this->catalogTable->updateDraft($kind, (int) $current['id'], $data, $revision)) {
                throw new RuntimeException('Scenario catalog revision conflict.');
            }
            $this->replaceTranslations($kind, (int) $current['id'], $data['translations']);
            $this->catalogTable->replaceRelations($kind, (int) $current['id'], $data);
            $this->audit($kind, $uuid, (int) $current['version'], 'updated', $userId, $correlationId);
        });

        return $this->read($kind, $uuid, array_key_first($data['translations']));
    }

    public function transition(string $kind, string $uuid, string $action, int $revision, string $correlationId): array
    {
        $reviewer = $action !== 'review';
        $userId = $this->authorization->userId($reviewer);
        $current = $this->current($kind, $uuid);
        if ((int) $current['revision'] !== $revision
            || !in_array(
                $current['lifecycle'],
                $action === 'review' ? ['draft'] : ($action === 'publish' ? ['review'] : ['published']),
                true
            )
        ) {
            throw new RuntimeException('Scenario catalog lifecycle conflict.');
        }
        if ($kind === 'template' && in_array($action, ['review', 'publish'], true)) {
            $this->catalogTable->assertTemplateEventLinksPublishable((int) $current['id']);
        }
        $lifecycle = $action === 'review' ? 'review' : ($action === 'publish' ? 'published' : 'deprecated');
        $warnings = $kind === 'template' && $action === 'publish'
            ? $this->catalogTable->templateModelEventWarnings((int) $current['id'])
            : [];
        $this->catalogTable->transactional(function () use (
            $kind,
            $current,
            $lifecycle,
            $userId,
            $revision,
            $uuid,
            $correlationId,
            $warnings
        ): void {
            if (!$this->catalogTable->transition(
                $kind,
                (int) $current['id'],
                $lifecycle,
                $userId,
                $revision
            )) {
                throw new RuntimeException('Scenario catalog lifecycle conflict.');
            }
            $this->audit(
                $kind,
                $uuid,
                (int) $current['version'],
                $lifecycle,
                $userId,
                $correlationId,
                $warnings === [] ? [] : ['modelsWithoutAvailableEvents' => $warnings]
            );
        });

        $result = $this->read($kind, $uuid, 'en');
        if ($warnings !== []) {
            $result['publicationWarnings'] = [
                'modelsWithoutAvailableEvents' => $warnings,
            ];
        }

        return $result;
    }

    private function insertDraft(
        string $kind,
        string $uuid,
        int $version,
        array $data,
        int $userId,
        string $correlationId
    ): array {
        $this->catalogTable->transactional(function () use (
            $kind,
            $uuid,
            $version,
            $data,
            $userId,
            $correlationId
        ): void {
            $id = $this->catalogTable->insertDraft($kind, $uuid, $version, $data, $userId);
            $this->replaceTranslations($kind, $id, $data['translations']);
            $this->catalogTable->replaceRelations($kind, $id, $data);
            $this->audit($kind, $uuid, $version, 'draft_created', $userId, $correlationId);
        });

        return $this->read($kind, $uuid, array_key_first($data['translations']));
    }

    private function replaceTranslations(string $kind, int $id, array $translations): void
    {
        $this->catalogTable->replaceTranslations($kind, $id, $translations);
    }

    /** @param array<string, mixed> $current @param array<string, mixed> $data @return array<string, mixed> */
    private function successorDraftData(string $kind, array $current, array $data): array
    {
        $id = (int) $current['id'];
        $data['semanticVersion'] ??= $current['semantic_version'];
        if ($kind === 'event') {
            $data['category'] ??= $current['category'];
            $data['guidance'] ??= $current['guidance'];
        } else {
            $data['assessmentGuidance'] ??= $current['assessment_guidance'];
        }

        $data['translations'] = array_replace(
            $this->writeTranslations($kind, $id),
            $data['translations']
        );
        $data['tags'] ??= $this->catalogTable->findTags($kind, $id);
        $data['references'] ??= $this->catalogTable->findReferences($kind, $id);
        $data['suggestions'] ??= $this->catalogTable->findSuggestions($kind, $id);
        if ($kind === 'template') {
            $data['eventRefs'] ??= $this->catalogTable->findTemplateEventReferences($id);
            $data['riskScenarioModels'] ??= array_map(
                static fn (array $model): array => [
                    'title' => $model['title'],
                    'description' => $model['description'],
                    'eventLinks' => array_map(
                        static fn (array $event): array => [
                            'stableUuid' => $event['stable_uuid'],
                            'version' => (int) $event['event_version'],
                            'guidance' => $event['guidance'],
                        ],
                        $model['events']
                    ),
                ],
                $this->catalogTable->findTemplateRiskModels($id)
            );
        }

        return $data;
    }

    /** @return array<string, array<string, mixed>> */
    private function writeTranslations(string $kind, int $id): array
    {
        $translations = [];
        foreach ($this->catalogTable->findTranslations($kind, $id) as $translation) {
            $translations[$translation['locale']] = [
                'title' => $translation['title'],
                'description' => $translation['description'],
                'riskSourceGuidance' => $translation['risk_source_guidance'] ?? null,
                'causeGuidance' => $translation['cause_guidance'] ?? null,
                'consequenceGuidance' => $translation['consequence_guidance'] ?? null,
                'subjectGuidance' => $translation['subject_guidance'] ?? null,
                'controlGuidance' => $translation['control_guidance'] ?? null,
                'monitoringGuidance' => $translation['monitoring_guidance'] ?? null,
            ];
        }

        return $translations;
    }

    /** @param array<string, mixed> $resource @return array<string, mixed> */
    private function presentListedResource(string $kind, array $resource, string $locale): array
    {
        $translations = $this->catalogTable->findTranslations($kind, (int) $resource['id']);
        $resource['translations'] = array_map(
            fn (array $translation): array => $this->presentTranslation($translation),
            $translations
        );
        $resource['translation'] = $this->resolveTranslation($translations, $locale);
        if ($kind === 'template') {
            $resource['risk_scenario_models'] = $this->catalogTable->findTemplateRiskModels((int) $resource['id']);
        }

        return $this->presentResource($resource);
    }

    private function locale(mixed $value): string
    {
        if (is_string($value) && preg_match('/^[a-z]{2,3}(?:-[A-Z]{2})?$/', $value)) {
            return $value;
        }

        return 'en';
    }

    private function audit(
        string $kind,
        string $uuid,
        int $version,
        string $action,
        int $userId,
        string $correlationId,
        array $payload = []
    ): void {
        $this->catalogTable->audit(
            $kind,
            $uuid,
            $version,
            $action,
            $userId,
            $correlationId,
            ['action' => $action] + $payload
        );
    }

    /** @return array<string, mixed> */
    private function current(string $kind, string $uuid, ?int $version = null): array
    {
        $resource = $this->catalogTable->find($kind, $uuid, $version);
        if ($resource === null) {
            throw new RuntimeException('Catalog resource not found.');
        }

        return $resource;
    }

    private function translationTable(string $kind): string
    {
        return $kind === 'event' ? 'scenario_catalog_event_translations' : 'scenario_catalog_template_translations';
    }

    private function foreignKey(string $kind): string
    {
        return $kind === 'event' ? 'event_id' : 'template_id';
    }

    /** @param array<int, array<string, mixed>> $translations */
    private function resolveTranslation(array $translations, string $locale): array
    {
        foreach ($translations as $translation) {
            if ($translation['locale'] === $locale) {
                return $this->presentTranslation($translation);
            }
        }

        foreach ($translations as $translation) {
            if ($translation['locale'] === 'en') {
                return $this->presentTranslation($translation);
            }
        }

        return $this->presentTranslation($translations[0]);
    }

    /** @param array<string, mixed> $translation @return array<string, mixed> */
    private function presentTranslation(array $translation): array
    {
        return [
            'locale' => $translation['locale'],
            'title' => $translation['title'],
            'description' => $translation['description'],
            'riskSourceGuidance' => $translation['risk_source_guidance'] ?? null,
            'causeGuidance' => $translation['cause_guidance'] ?? null,
            'consequenceGuidance' => $translation['consequence_guidance'] ?? null,
            'subjectGuidance' => $translation['subject_guidance'] ?? null,
            'controlGuidance' => $translation['control_guidance'] ?? null,
            'monitoringGuidance' => $translation['monitoring_guidance'] ?? null,
        ];
    }

    /** @param array<string, mixed> $resource @return array<string, mixed> */
    private function presentResource(array $resource): array
    {
        $result = [
            'stableUuid' => $resource['stable_uuid'],
            'version' => (int) $resource['version'],
            'semanticVersion' => $resource['semantic_version'],
            'lifecycle' => $resource['lifecycle'],
            'revision' => (int) $resource['revision'],
            'provenance' => $resource['provenance'],
            'authorId' => (int) $resource['author_id'],
            'reviewerId' => $resource['reviewer_id'] === null ? null : (int) $resource['reviewer_id'],
            'publishedAt' => $resource['published_at'],
            'deprecatedAt' => $resource['deprecated_at'],
            'translation' => $resource['translation'] ?? null,
        ];
        if (isset($resource['category'])) {
            $result['category'] = $resource['category'];
            $result['guidance'] = $resource['guidance'];
        } else {
            $result['assessmentGuidance'] = $resource['assessment_guidance'];
        }
        if (isset($resource['translations'])) {
            $result['translations'] = $resource['translations'];
        }
        if (isset($resource['risk_scenario_models'])) {
            $result['riskScenarioModels'] = array_map(
                static fn (array $model): array => [
                    'stableUuid' => $model['stable_uuid'],
                    'title' => $model['title'],
                    'description' => $model['description'],
                    'position' => (int) $model['position'],
                    'revision' => (int) $model['revision'],
                    'eventLinks' => array_map(
                        static fn (array $event): array => [
                            'stableUuid' => $event['stable_uuid'],
                            'version' => (int) $event['event_version'],
                            'title' => $event['event_title_snapshot'],
                            'guidance' => $event['guidance'],
                            'position' => (int) $event['position'],
                        ],
                        $model['events']
                    ),
                ],
                $resource['risk_scenario_models']
            );
        }

        return $result;
    }
}
