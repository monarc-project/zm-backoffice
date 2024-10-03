<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2024 Luxembourg House of Cybersecurity LHC.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Laminas\Mvc\Controller\AbstractRestfulController;
use Monarc\Core\Controller\Handler\ControllerRequestResponseHandlerTrait;
use Monarc\Core\InputFormatter\RolfRisk\GetRolfRisksInputFormatter;
use Monarc\Core\Service\RolfRiskService;
use Monarc\Core\Validator\InputValidator\RolfRisk\PostRolfRiskDataInputValidator;

class ApiRolfRisksController extends AbstractRestfulController
{
    use ControllerRequestResponseHandlerTrait;

    public function __construct(
        private RolfRiskService $rolfRiskService,
        private GetRolfRisksInputFormatter $rolfRisksInputFormatter,
        private PostRolfRiskDataInputValidator $postRolfRiskDataInputValidator
    ) {
    }

    public function getList()
    {
        $formattedParams = $this->getFormattedInputParams($this->rolfRisksInputFormatter);

        return $this->getPreparedJsonResponse([
            'count' => $this->rolfRiskService->getCount($formattedParams),
            'risks' => $this->rolfRiskService->getList($formattedParams),
        ]);
    }

    public function get($id)
    {
        return $this->getPreparedJsonResponse($this->rolfRiskService->getRolfRiskData((int)$id));
    }

    /**
     * @param array $data
     */
    public function create($data)
    {
        $isBatchData = $this->isBatchData($data);
        $this->validatePostParams($this->postRolfRiskDataInputValidator, $data, $isBatchData);

        if ($this->isBatchData($data)) {
            return $this->getSuccessfulJsonResponse([
                'id' => $this->rolfRiskService->createList($this->postRolfRiskDataInputValidator->getValidDataSets()),
            ]);
        }

        return $this->getSuccessfulJsonResponse([
            'id' => $this->rolfRiskService->create($this->postRolfRiskDataInputValidator->getValidData())->getId(),
        ]);
    }

    /**
     * @param array $data
     */
    public function update($id, $data)
    {
        $this->validatePostParams($this->postRolfRiskDataInputValidator->setExcludeFilter(['id' => (int)$id]), $data);

        $this->rolfRiskService->update((int)$id, $this->postRolfRiskDataInputValidator->getValidData());

        return $this->getSuccessfulJsonResponse();
    }

    public function patchList($data)
    {
        $this->rolfRiskService->linkMeasuresToRisks($data['fromReferential'], $data['toReferential']);

        return $this->getSuccessfulJsonResponse();
    }

    public function delete($id)
    {
        $this->rolfRiskService->delete((int)$id);

        return $this->getSuccessfulJsonResponse();
    }

    public function deleteList($data)
    {
        $this->rolfRiskService->deleteList($data);

        return $this->getSuccessfulJsonResponse();
    }
}
