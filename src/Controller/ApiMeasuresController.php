<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2024 Luxembourg House of Cybersecurity LHC.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Laminas\Mvc\Controller\AbstractRestfulController;
use Monarc\Core\Controller\Handler\ControllerRequestResponseHandlerTrait;
use Monarc\Core\InputFormatter\Measure\GetMeasuresInputFormatter;
use Monarc\Core\Service\MeasureService;
use Monarc\Core\Validator\InputValidator\Measure\PostMeasureDataInputValidator;
use Monarc\Core\Validator\InputValidator\Measure\UpdateMeasureDataInputValidator;

class ApiMeasuresController extends AbstractRestfulController
{
    use ControllerRequestResponseHandlerTrait;

    public function __construct(
        private MeasureService $measureService,
        private GetMeasuresInputFormatter $getMeasuresInputFormatter,
        private PostMeasureDataInputValidator $postMeasureDataInputValidator,
        private UpdateMeasureDataInputValidator $updateMeasureDataInputValidator
    ) {
    }

    public function getList()
    {
        $formattedParams = $this->getFormattedInputParams($this->getMeasuresInputFormatter);

        return $this->getPreparedJsonResponse([
            'count' => $this->measureService->getCount($formattedParams),
            'measures' => $this->measureService->getList($formattedParams),
        ]);
    }

    /**
     * @param string $id
     */
    public function get($id)
    {
        return $this->getPreparedJsonResponse($this->measureService->getMeasureData($id));
    }

    public function create($data)
    {
        $isBatchData = $this->isBatchData($data);
        $this->validatePostParams($this->postMeasureDataInputValidator, $data, $isBatchData);

        if ($this->isBatchData($data)) {
            return $this->getSuccessfulJsonResponse([
                'id' => $this->measureService->createList($this->postMeasureDataInputValidator->getValidDataSets()),
            ]);
        }

        return $this->getSuccessfulJsonResponse([
            'id' => $this->measureService->create($this->postMeasureDataInputValidator->getValidData())->getUuid(),
        ]);
    }

    /**
     * @param string $id
     * @param array $data
     */
    public function update($id, $data)
    {
        $this->validatePostParams($this->updateMeasureDataInputValidator, $data);

        $this->measureService->update($id, $this->updateMeasureDataInputValidator->getValidData());

        return $this->getSuccessfulJsonResponse();
    }

    /**
     * @param string $id
     */
    public function delete($id)
    {
        $this->measureService->delete($id);

        return $this->getSuccessfulJsonResponse();
    }

    public function deleteList($data)
    {
        $this->measureService->deleteList($data);

        return $this->getSuccessfulJsonResponse();
    }
}
