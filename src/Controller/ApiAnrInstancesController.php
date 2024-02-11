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
use Monarc\Core\Service\InstanceService;
use Monarc\Core\Validator\InputValidator\Instance\CreateInstanceDataInputValidator;
use Monarc\Core\Validator\InputValidator\Instance\PatchInstanceDataInputValidator;
use Monarc\Core\Validator\InputValidator\Instance\UpdateInstanceDataInputValidator;

class ApiAnrInstancesController extends AbstractRestfulControllerRequestHandler
{
    use ControllerRequestResponseHandlerTrait;

    private InstanceService $instanceService;

    private CreateInstanceDataInputValidator $createInstanceDataInputValidator;

    private UpdateInstanceDataInputValidator $updateInstanceDataInputValidator;

    private PatchInstanceDataInputValidator $patchInstanceDataInputValidator;

    public function __construct(
        InstanceService $instanceService,
        CreateInstanceDataInputValidator $createInstanceDataInputValidator,
        UpdateInstanceDataInputValidator $updateInstanceDataInputValidator,
        PatchInstanceDataInputValidator $patchInstanceDataInputValidator
    ) {
        $this->instanceService = $instanceService;
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

    /**
     * Instantiation of an object to the analysis.
     *
     * @param array $data
     */
    public function create($data)
    {
        /** @var Anr $anr */
        $anr = $this->getRequest()->getAttribute('anr');

        $this->validatePostParams($this->createInstanceDataInputValidator, $data);

        $instance = $this->instanceService
            ->instantiateObjectToAnr($anr, $this->createInstanceDataInputValidator->getValidData(), true);

        return $this->getSuccessfulJsonResponse(['id' => $instance->getId()]);
    }

    /**
     * Is called when instances consequences are set (edit impact).
     *
     * @param array $data
     */
    public function update($id, $data)
    {
        /** @var Anr $anr */
        $anr = $this->getRequest()->getAttribute('anr');

        $this->validatePostParams($this->updateInstanceDataInputValidator, $data);

        $this->instanceService->updateInstance($anr, (int)$id, $this->updateInstanceDataInputValidator->getValidData());

        return $this->getSuccessfulJsonResponse();
    }

    /**
     * Is called when we move (drag-n-drop) instance inside of analysis.
     */
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
        /** @var Anr $anr */
        $anr = $this->getRequest()->getAttribute('anr');

        $this->instanceService->delete($anr, (int)$id);

        return $this->getSuccessfulJsonResponse();
    }
}
