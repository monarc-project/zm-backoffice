<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2026 Luxembourg House of Cybersecurity LHC.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Laminas\Mvc\Controller\AbstractRestfulController;
use Monarc\Core\Controller\Handler\ControllerRequestResponseHandlerTrait;
use Monarc\Core\Entity\RiskSource;
use Monarc\Core\InputFormatter\RiskSource\GetRiskSourcesInputFormatter;
use Monarc\Core\Service\RiskSourceService;
use Monarc\Core\Validator\InputValidator\RiskSource\PatchRiskSourceDataInputValidator;
use Monarc\Core\Validator\InputValidator\RiskSource\PostRiskSourceDataInputValidator;

class ApiRiskSourcesController extends AbstractRestfulController
{
    use ControllerRequestResponseHandlerTrait;

    public function __construct(
        private GetRiskSourcesInputFormatter $getRiskSourcesInputFormatter,
        private RiskSourceService $riskSourceService,
        private PostRiskSourceDataInputValidator $postRiskSourceDataInputValidator,
        private PatchRiskSourceDataInputValidator $patchRiskSourceDataInputValidator
    ) {
    }

    public function getList()
    {
        $formattedParams = $this->getFormattedInputParams($this->getRiskSourcesInputFormatter);
        $riskSources = $this->riskSourceService->getList($formattedParams);
        $displayLabelsByRiskSourceId = $this->riskSourceService->getDisplayLabelsByRiskSourceId($riskSources);

        return $this->getPreparedJsonResponse([
            'count' => $this->riskSourceService->getCount($formattedParams),
            'riskSources' => array_map(
                fn (RiskSource $riskSource): array => $this->prepareRiskSourceData(
                    $riskSource,
                    false,
                    $displayLabelsByRiskSourceId[$riskSource->getId()] ?? $riskSource->getLabel()
                ),
                $riskSources
            ),
        ]);
    }

    public function get($id)
    {
        return $this->getPreparedJsonResponse(
            $this->prepareRiskSourceData($this->riskSourceService->get((int)$id), true)
        );
    }

    public function create($data)
    {
        $this->validatePostParams($this->postRiskSourceDataInputValidator, $data);
        $riskSourceData = $this->prepareRiskSourcePayload($data, $this->postRiskSourceDataInputValidator->getValidData());

        return $this->getSuccessfulJsonResponse($this->prepareRiskSourceData(
            $this->riskSourceService->create($riskSourceData),
            true
        ));
    }

    public function update($id, $data)
    {
        $this->validatePostParams($this->patchRiskSourceDataInputValidator, $data);
        $riskSourceData = $this->prepareRiskSourcePayload($data, $this->patchRiskSourceDataInputValidator->getValidData());

        return $this->getSuccessfulJsonResponse($this->prepareRiskSourceData(
            $this->riskSourceService->update((int)$id, $riskSourceData),
            true
        ));
    }

    public function delete($id)
    {
        $this->riskSourceService->delete((int)$id);

        return $this->getSuccessfulJsonResponse();
    }

    private function prepareRiskSourceData(
        RiskSource $riskSource,
        bool $includeLabels = false,
        ?string $displayLabel = null
    ): array
    {
        $riskSourceData = [
            'id' => $riskSource->getId(),
            'label' => $displayLabel ?? $this->riskSourceService->getDisplayLabel($riskSource),
            'isDefault' => $riskSource->isDefault(),
            'isActive' => $riskSource->isActive(),
        ];

        if ($includeLabels) {
            $riskSourceData['labels'] = $this->riskSourceService->getLabels($riskSource);
        }

        return $riskSourceData;
    }

    private function prepareRiskSourcePayload(array $sourceData, array $validatedData): array
    {
        if (!isset($sourceData['labels']) || !is_array($sourceData['labels'])) {
            return $validatedData;
        }

        $labels = [];
        foreach ($sourceData['labels'] as $languageCode => $label) {
            $trimmedLabel = trim((string)$label);
            if ($trimmedLabel !== '') {
                $labels[(string)$languageCode] = $trimmedLabel;
            }
        }

        if ($labels !== []) {
            $validatedData['labels'] = $labels;
        }

        return $validatedData;
    }
}
