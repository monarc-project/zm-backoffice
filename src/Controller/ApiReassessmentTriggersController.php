<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2026 Luxembourg House of Cybersecurity LHC.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Laminas\Mvc\Controller\AbstractRestfulController;
use Monarc\Core\Controller\Handler\ControllerRequestResponseHandlerTrait;
use Monarc\Core\Entity\ReassessmentTrigger;
use Monarc\Core\InputFormatter\ReassessmentTrigger\GetReassessmentTriggersInputFormatter;
use Monarc\Core\Service\ReassessmentTriggerService;
use Monarc\Core\Validator\InputValidator\ReassessmentTrigger\PatchReassessmentTriggerDataInputValidator;
use Monarc\Core\Validator\InputValidator\ReassessmentTrigger\PostReassessmentTriggerDataInputValidator;

class ApiReassessmentTriggersController extends AbstractRestfulController
{
    use ControllerRequestResponseHandlerTrait;

    public function __construct(
        private GetReassessmentTriggersInputFormatter $getReassessmentTriggersInputFormatter,
        private ReassessmentTriggerService $reassessmentTriggerService,
        private PostReassessmentTriggerDataInputValidator $postReassessmentTriggerDataInputValidator,
        private PatchReassessmentTriggerDataInputValidator $patchReassessmentTriggerDataInputValidator
    ) {
    }

    public function getList()
    {
        $formattedParams = $this->getFormattedInputParams($this->getReassessmentTriggersInputFormatter);
        $reassessmentTriggers = $this->reassessmentTriggerService->getList($formattedParams);

        return $this->getPreparedJsonResponse([
            'count' => $this->reassessmentTriggerService->getCount($formattedParams),
            'reassessmentTriggers' => array_map(
                fn (ReassessmentTrigger $reassessmentTrigger): array => $this->prepareReassessmentTriggerData(
                    $reassessmentTrigger,
                    false
                ),
                $reassessmentTriggers
            ),
        ]);
    }

    public function get($id)
    {
        return $this->getPreparedJsonResponse(
            $this->prepareReassessmentTriggerData($this->reassessmentTriggerService->get((int)$id), true)
        );
    }

    public function create($data)
    {
        $payload = $this->prepareReassessmentTriggerPayload($data);
        $this->validatePostParams($this->postReassessmentTriggerDataInputValidator, $payload);

        return $this->getSuccessfulJsonResponse($this->prepareReassessmentTriggerData(
            $this->reassessmentTriggerService->create($payload),
            true
        ));
    }

    public function update($id, $data)
    {
        $payload = $this->prepareReassessmentTriggerPayload($data);
        $this->validatePostParams($this->patchReassessmentTriggerDataInputValidator, $payload);

        return $this->getSuccessfulJsonResponse($this->prepareReassessmentTriggerData(
            $this->reassessmentTriggerService->update((int)$id, $payload),
            true
        ));
    }

    public function delete($id)
    {
        $this->reassessmentTriggerService->delete((int)$id);

        return $this->getSuccessfulJsonResponse();
    }

    private function prepareReassessmentTriggerData(
        ReassessmentTrigger $reassessmentTrigger,
        bool $includeTranslations
    ): array {
        $reassessmentTriggerData = [
            'id' => $reassessmentTrigger->getId(),
            'triggerType' => $this->reassessmentTriggerService->getDisplayTriggerType($reassessmentTrigger),
            'description' => $this->reassessmentTriggerService->getDisplayDescription($reassessmentTrigger),
            'isActive' => $reassessmentTrigger->isActive(),
            'position' => $reassessmentTrigger->getPosition(),
        ];

        if ($includeTranslations) {
            $reassessmentTriggerData['triggerTypes'] = $this->reassessmentTriggerService
                ->getTriggerTypes($reassessmentTrigger);
            $reassessmentTriggerData['descriptions'] = $this->reassessmentTriggerService
                ->getDescriptions($reassessmentTrigger);
        }

        return $reassessmentTriggerData;
    }

    private function prepareReassessmentTriggerPayload(array $sourceData): array
    {
        $payload = [];
        if (array_key_exists('id', $sourceData)) {
            $payload['id'] = (int)$sourceData['id'];
        }
        if (array_key_exists('triggerType', $sourceData)) {
            $payload['triggerType'] = trim((string)$sourceData['triggerType']);
        }
        if (array_key_exists('description', $sourceData)) {
            $payload['description'] = trim((string)$sourceData['description']);
        }
        if (array_key_exists('isActive', $sourceData)) {
            $payload['isActive'] = $sourceData['isActive'];
        }
        if (array_key_exists('position', $sourceData)) {
            $payload['position'] = $sourceData['position'];
        }

        $triggerTypes = $this->normalizeTranslations($sourceData['triggerTypes'] ?? null);
        if ($triggerTypes !== []) {
            $payload['triggerTypes'] = $triggerTypes;
            if (empty($payload['triggerType'])) {
                $payload['triggerType'] = (string)reset($triggerTypes);
            }
        }

        $descriptions = $this->normalizeTranslations($sourceData['descriptions'] ?? null);
        if ($descriptions !== []) {
            $payload['descriptions'] = $descriptions;
            if (empty($payload['description'])) {
                $payload['description'] = (string)reset($descriptions);
            }
        }

        return $payload;
    }

    /**
     * @return array<string, string>
     */
    private function normalizeTranslations(mixed $translations): array
    {
        if (!is_array($translations)) {
            return [];
        }

        $normalizedTranslations = [];
        foreach ($translations as $languageCode => $value) {
            $trimmedValue = trim((string)$value);
            if ($trimmedValue !== '') {
                $normalizedTranslations[(string)$languageCode] = $trimmedValue;
            }
        }

        return $normalizedTranslations;
    }
}
