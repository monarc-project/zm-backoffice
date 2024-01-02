<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2023 Luxembourg House of Cybersecurity LHC.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Monarc\Core\Controller\Handler\AbstractRestfulControllerRequestHandler;
use Monarc\Core\Controller\Handler\ControllerRequestResponseHandlerTrait;
use Monarc\Core\InputFormatter\Object\GetObjectInputFormatter;
use Monarc\Core\InputFormatter\Object\GetObjectsInputFormatter;
use Monarc\Core\Model\Entity\Anr;
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
            'objects' => $this->objectService->getList($formattedInputParams),
        ]);
    }

    /**
     * @param string $id
     */
    public function get($id)
    {
        $formattedInputParams = $this->getFormattedInputParams($this->getObjectInputFormatter);

        return $this->getPreparedJsonResponse($this->objectService->getObjectData($id, $formattedInputParams));
    }

    /**
     * @param array $data
     */
    public function create($data)
    {
        $isBatchData = $this->isBatchData($data);
        $this->validatePostParams($this->postObjectDataInputValidator, $data, $isBatchData);

        $objectsUuids = [];
        $validatedData = $isBatchData
            ? $this->postObjectDataInputValidator->getValidDataSets()
            : [$this->postObjectDataInputValidator->getValidData()];
        foreach ($validatedData as $validatedDataRow) {
            $objectsUuids[] = $this->objectService->create($validatedDataRow)->getUuid();
        }

        return $this->getSuccessfulJsonResponse([
            'id' => \count($objectsUuids) === 1 ? current($objectsUuids) : $objectsUuids,
        ]);
    }

    /**
     * @param string $id
     * @param array $data
     */
    public function update($id, $data)
    {
        $this->validatePostParams($this->postObjectDataInputValidator, $data);

        $this->objectService->update($id, $this->postObjectDataInputValidator->getValidData());

        return $this->getSuccessfulJsonResponse();
    }

    /**
     * @param string $id
     */
    public function delete($id)
    {
        $this->objectService->delete($id);

        return $this->getSuccessfulJsonResponse();
    }

    public function parentsAction()
    {
        /** @var Anr|null $anr */
        $anr = $this->getRequest()->getAttribute('anr');
        $objectUuid = $this->params()->fromRoute('id');

        return $this->getPreparedJsonResponse($this->objectService->getParentsInAnr($anr, $objectUuid));
    }
}
