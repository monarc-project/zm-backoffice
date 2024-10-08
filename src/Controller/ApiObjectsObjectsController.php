<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2023 Luxembourg House of Cybersecurity LHC.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Laminas\Mvc\Controller\AbstractRestfulController;
use Monarc\Core\Controller\Handler\ControllerRequestResponseHandlerTrait;
use Monarc\Core\Service\ObjectObjectService;
use Monarc\Core\Validator\InputValidator\ObjectComposition\CreateDataInputValidator;
use Monarc\Core\Validator\InputValidator\ObjectComposition\MovePositionDataInputValidator;

class ApiObjectsObjectsController extends AbstractRestfulController
{
    use ControllerRequestResponseHandlerTrait;

    private ObjectObjectService $objectObjectService;

    private CreateDataInputValidator $createDataInputValidator;

    private MovePositionDataInputValidator $movePositionDataInputValidator;

    public function __construct(
        ObjectObjectService $objectObjectService,
        CreateDataInputValidator $createDataInputValidator,
        MovePositionDataInputValidator $movePositionDataInputValidator
    ) {
        $this->objectObjectService = $objectObjectService;
        $this->createDataInputValidator = $createDataInputValidator;
        $this->movePositionDataInputValidator = $movePositionDataInputValidator;
    }

    /**
     * @param array $data
     */
    public function create($data)
    {
        $this->validatePostParams($this->createDataInputValidator, $data);

        $objectComposition = $this->objectObjectService->create($this->createDataInputValidator->getValidData());

        return $this->getSuccessfulJsonResponse(['id' => $objectComposition->getId()]);
    }

    /**
     * @param array $data
     */
    public function update($id, $data)
    {
        $this->validatePostParams($this->movePositionDataInputValidator, $data);

        $this->objectObjectService->shiftPositionInComposition(
            (int)$id,
            $this->movePositionDataInputValidator->getValidData()
        );

        return $this->getSuccessfulJsonResponse();
    }

    public function delete($id)
    {
        $this->objectObjectService->delete((int)$id);

        return $this->getSuccessfulJsonResponse();
    }
}
