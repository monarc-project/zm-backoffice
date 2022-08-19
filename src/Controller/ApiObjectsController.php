<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2022  SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Monarc\Core\Controller\Handler\AbstractRestfulControllerRequestHandler;
use Monarc\Core\Controller\Handler\ControllerRequestResponseHandlerTrait;
use Monarc\Core\InputFormatter\Object\GetObjectInputFormatter;
use Monarc\Core\InputFormatter\Object\GetObjectsInputFormatter;
use Monarc\Core\Model\Entity\AnrSuperClass;
use Monarc\Core\Service\ObjectService;
use Monarc\Core\Validator\InputValidator\Object\PostObjectDataInputValidator;

class ApiObjectsController extends AbstractRestfulControllerRequestHandler
{
    use ControllerRequestResponseHandlerTrait;

    private ObjectService $objectService;

    private GetObjectsInputFormatter $getObjectsInputFormatter;

    private GetObjectInputFormatter $getObjectInputFormatter;

    private PostObjectDataInputValidator $postObjectDataInputValidator;

    public function __construct(
        ObjectService $objectService,
        GetObjectsInputFormatter $getObjectsInputFormatter,
        GetObjectInputFormatter $getObjectInputFormatter,
        PostObjectDataInputValidator $postObjectDataInputValidator
    ) {
        $this->objectService = $objectService;
        $this->getObjectsInputFormatter = $getObjectsInputFormatter;
        $this->getObjectInputFormatter = $getObjectInputFormatter;
        $this->postObjectDataInputValidator = $postObjectDataInputValidator;
    }

    public function getList()
    {
        $formattedInputParams = $this->getFormattedInputParams($this->getObjectsInputFormatter);

        return $this->getPreparedJsonResponse([
            'count' => $this->objectService->getCount($formattedInputParams),
            'objects' => $this->objectService->getListSpecific($formattedInputParams),
        ]);
    }

    public function get($id)
    {
        $formattedInputParams = $this->getFormattedInputParams($this->getObjectInputFormatter);

        return $this->getPreparedJsonResponse($this->objectService->getObjectData($id, $formattedInputParams));
    }

    public function create($data)
    {
        $this->validatePostParams($this->postObjectDataInputValidator, $data);

        $object = $this->objectService->create($data);

        return $this->getPreparedJsonResponse([
            'status' => 'ok',
            'id' => $object->getUuid(),
        ]);
    }

    // TODO: update, patch, delete, deleteAll.

    public function getParents()
    {
        /** @var AnrSuperClass|null $anr */
        $anr = $this->getRequest()->getAttribute('anr');
        $objectUuid = $this->params()->fromRoute('id');

        return $this->getPreparedJsonResponse($this->objectService->getParentsInAnr($anr, $objectUuid));
    }
}
