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
        $where = 'c.lifecycle IN (\'published\', \'deprecated\')';
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
        $lifecycle = $action === 'review' ? 'review' : ($action === 'publish' ? 'published' : 'deprecated');
        $this->catalogTable->transactional(function () use (
            $kind,
            $current,
            $lifecycle,
            $userId,
            $revision,
            $uuid,
            $correlationId
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
            $this->audit($kind, $uuid, (int) $current['version'], $lifecycle, $userId, $correlationId);
        });

        return $this->read($kind, $uuid, 'en');
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

    /** @param array<string, mixed> $resource @return array<string, mixed> */
    private function presentListedResource(string $kind, array $resource, string $locale): array
    {
        $translations = $this->catalogTable->findTranslations($kind, (int) $resource['id']);
        $resource['translation'] = $this->resolveTranslation($translations, $locale);

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
        string $correlationId
    ): void {
        $this->catalogTable->audit(
            $kind,
            $uuid,
            $version,
            $action,
            $userId,
            $correlationId,
            ['action' => $action]
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

        return $result;
    }
}
