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
        $this->validatePostParams($this->postReassessmentTriggerDataInputValidator, $data);

        return $this->getSuccessfulJsonResponse($this->prepareReassessmentTriggerData(
            $this->reassessmentTriggerService->create(
                $this->postReassessmentTriggerDataInputValidator->getValidData()
            ),
            true
        ));
    }

    public function update($id, $data)
    {
        $this->validatePostParams($this->patchReassessmentTriggerDataInputValidator, $data);

        return $this->getSuccessfulJsonResponse($this->prepareReassessmentTriggerData(
            $this->reassessmentTriggerService->update(
                (int)$id,
                $this->patchReassessmentTriggerDataInputValidator->getValidData()
            ),
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
        bool $includeTranslations = false
    ): array {
        $reassessmentTriggerData = [
            'id' => $reassessmentTrigger->getId(),
            'triggerType' => $this->reassessmentTriggerService->getDisplayTriggerType($reassessmentTrigger),
            'description' => $this->reassessmentTriggerService->getDisplayDescription($reassessmentTrigger),
            'monitoringApproach' => $this->reassessmentTriggerService->getDisplayMonitoringApproach(
                $reassessmentTrigger
            ),
            'isActive' => $reassessmentTrigger->isActive(),
            'position' => $reassessmentTrigger->getPosition(),
        ];

        if ($includeTranslations) {
            $reassessmentTriggerData['triggerTypes'] = $this->reassessmentTriggerService
                ->getTriggerTypes($reassessmentTrigger);
            $reassessmentTriggerData['descriptions'] = $this->reassessmentTriggerService
                ->getDescriptions($reassessmentTrigger);
            $reassessmentTriggerData['monitoringApproaches'] = $this->reassessmentTriggerService
                ->getMonitoringApproaches($reassessmentTrigger);
        }

        return $reassessmentTriggerData;
    }
}
