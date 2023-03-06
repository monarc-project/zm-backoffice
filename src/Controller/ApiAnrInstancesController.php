<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2023 Luxembourg House of Cybersecurity LHC.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Monarc\Core\Controller\Handler\AbstractRestfulControllerRequestHandler;
use Monarc\Core\Controller\Handler\ControllerRequestResponseHandlerTrait;
use Monarc\Core\Model\Entity\Anr;
use Monarc\Core\Service\InstanceExportService;
use Monarc\Core\Service\InstanceService;
use Monarc\Core\Validator\InputValidator\Instance\CreateInstanceDataInputValidator;
use Monarc\Core\Validator\InputValidator\Instance\PatchInstanceDataInputValidator;
use Monarc\Core\Validator\InputValidator\Instance\UpdateInstanceDataInputValidator;

class ApiAnrInstancesController extends AbstractRestfulControllerRequestHandler
{
    use ControllerRequestResponseHandlerTrait;

    private InstanceService $instanceService;

    private InstanceExportService $instanceExportService;

    private CreateInstanceDataInputValidator $createInstanceDataInputValidator;

    private UpdateInstanceDataInputValidator $updateInstanceDataInputValidator;

    private PatchInstanceDataInputValidator $patchInstanceDataInputValidator;

    public function __construct(
        InstanceService $instanceService,
        InstanceExportService $instanceExportService,
        CreateInstanceDataInputValidator $createInstanceDataInputValidator,
        UpdateInstanceDataInputValidator $updateInstanceDataInputValidator,
        PatchInstanceDataInputValidator $patchInstanceDataInputValidator
    ) {
        $this->instanceService = $instanceService;
        $this->instanceExportService = $instanceExportService;
        $this->createInstanceDataInputValidator = $createInstanceDataInputValidator;
        $this->updateInstanceDataInputValidator = $updateInstanceDataInputValidator;
        $this->patchInstanceDataInputValidator = $patchInstanceDataInputValidator;
    }

    public function getList()
    {
        /** @var Anr $anr */
        $anr = $this->getRequest()->getAttribute('anr');

        $instances = $this->instanceService->getInstancesData($anr);

        return $this->getPreparedJsonResponse([
            'instances' => $instances,
        ]);
    }

    public function get($id)
    {
        /** @var Anr $anr */
        $anr = $this->getRequest()->getAttribute('anr');

        $instanceData = $this->instanceService->getInstanceData($anr, (int)$id);

        return $this->getPreparedJsonResponse($instanceData);
    }

    public function create($data)
    {
        /** @var Anr $anr */
        $anr = $this->getRequest()->getAttribute('anr');

        $this->validatePostParams($this->createInstanceDataInputValidator, $data);

        $instance = $this->instanceService
            ->instantiateObjectToAnr($anr, $this->createInstanceDataInputValidator->getValidData(), true);

        return $this->getSuccessfulJsonResponse(['id' => $instance->getId()]);
    }

    public function update($id, $data)
    {
        /** @var Anr $anr */
        $anr = $this->getRequest()->getAttribute('anr');

        $this->validatePostParams($this->updateInstanceDataInputValidator, $data);

        $this->instanceService->updateInstance($anr, (int)$id, $this->updateInstanceDataInputValidator->getValidData());

        return $this->getSuccessfulJsonResponse();
    }

    public function patch($id, $data)
    {
        /** @var Anr $anr */
        $anr = $this->getRequest()->getAttribute('anr');

        $this->validatePostParams($this->patchInstanceDataInputValidator, $data);

        $this->instanceService->patchInstance($anr, (int)$id, $this->patchInstanceDataInputValidator->getValidData());

        return $this->getSuccessfulJsonResponse();
    }

    public function delete($id)
    {
        $this->instanceService->delete((int)$id);

        return $this->getSuccessfulJsonResponse();
    }

    /**
     * TODO: use the InstanceExportService
     * TODO: the export with password and evals doesn't work as far as we need to get [POST] data, but request is [GET]
     *
     * Exports an instance in our own custom encrypted format and downloads it to the client browser
     * @return \Laminas\Stdlib\ResponseInterface The file attachment response
     */
    public function exportAction()
    {
        $id = $this->params()->fromRoute('id');
        $data['id'] = $id;

        if (empty($data['password'])) {
            $contentType = 'application/json; charset=utf-8';
            $extension = '.json';
        } else {
            $contentType = 'text/plain; charset=utf-8';
            $extension = '.bin';
        }

        $exportData = $this->instanceExportService->export($data);
        $this->getResponse()
            ->setContent($exportData);

        $this->getResponse()
            ->getHeaders()
            ->clearHeaders()
            ->addHeaderLine('Content-Type', $contentType)
            ->addHeaderLine('Content-Length', \strlen($exportData))
            ->addHeaderLine('Content-Disposition', 'attachment; filename="' .
                (empty($data['filename']) ? $data['id'] : $data['filename']) . $extension . '"');

        return $this->getResponse();
    }
}
