<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2022  SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Monarc\Core\Controller\Handler\AbstractRestfulControllerRequestHandler;
use Monarc\Core\Controller\Handler\ControllerRequestResponseHandlerTrait;
use Monarc\Core\Service\ObjectObjectService;
use Monarc\Core\Validator\InputValidator\ObjectComposition\CreateDataInputValidator;
use Monarc\Core\Validator\InputValidator\ObjectComposition\MovePositionDataInputValidator;

class ApiObjectsObjectsController extends AbstractRestfulControllerRequestHandler
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

    public function create($data)
    {
        $this->validatePostParams($this->createDataInputValidator, $data);

        $objectComposition = $this->objectObjectService->create($this->createDataInputValidator->getValidData());

        return $this->getSuccessfulJsonResponse(['id' => $objectComposition->getId()]);
    }

    public function update($id, $data)
    {
        $this->validatePostParams($this->movePositionDataInputValidator, $data);

        $this->objectObjectService->shiftPositionInComposition(
            $id,
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
