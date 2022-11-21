<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2022 SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Monarc\Core\Controller\Handler\AbstractRestfulControllerRequestHandler;
use Monarc\Core\Controller\Handler\ControllerRequestResponseHandlerTrait;
use Monarc\Core\Model\Entity\Anr;
use Monarc\Core\Service\InstanceMetadataFieldService;

class ApiAnrInstancesMetadataFieldsController extends AbstractRestfulControllerRequestHandler
{
    use ControllerRequestResponseHandlerTrait;

    private InstanceMetadataFieldService $instanceMetadataFieldService;

    public function __construct(InstanceMetadataFieldService $instanceMetadataFieldService)
    {
        $this->instanceMetadataFieldService = $instanceMetadataFieldService;
    }

    public function getList()
    {
        /** @var Anr $anr */
        $anr = $this->getRequest()->getAttribute('anr');
        $language = $this->params()->fromQuery("language");

        return $this->getPreparedJsonResponse([
            'data' => $this->instanceMetadataFieldService->getList($anr, $language),
        ]);
    }

    public function get($id)
    {
        /** @var Anr $anr */
        $anr = $this->getRequest()->getAttribute('anr');
        $language = $this->params()->fromQuery("language");

        return $this->getPreparedJsonResponse([
            'data' => $this->instanceMetadataFieldService->getInstanceMetadataField($anr, (int)$id, $language),
        ]);
    }

    public function create($data)
    {
        /** @var Anr $anr */
        $anr = $this->getRequest()->getAttribute('anr');

        return $this->getSuccessfulJsonResponse([
            'id' => $this->instanceMetadataFieldService->create($anr, $data),
        ]);
    }

    public function delete($id)
    {
        $this->instanceMetadataFieldService->delete((int)$id);

        return $this->getSuccessfulJsonResponse();
    }

    public function update($id, $data)
    {
        $this->instanceMetadataFieldService->update((int)$id, $data);

        return $this->getSuccessfulJsonResponse();
    }
}
