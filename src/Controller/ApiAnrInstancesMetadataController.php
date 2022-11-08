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
use Monarc\Core\Service\AnrInstanceMetadataService;

class ApiAnrInstancesMetadataController extends AbstractRestfulControllerRequestHandler
{
    use ControllerRequestResponseHandlerTrait;

    private AnrInstanceMetadataService $anrInstanceMetadataService;

    public function __construct(AnrInstanceMetadataService $anrInstanceMetadataService)
    {
        $this->anrInstanceMetadataService = $anrInstanceMetadataService;
    }

    public function getList()
    {
        /** @var Anr $anr */
        $anr = $this->getRequest()->getAttribute('anr');
        $language = $this->params()->fromQuery("language");

        return $this->getPreparedJsonResponse([
            'data' => $this->anrInstanceMetadataService->getList($anr, $language),
        ]);
    }

    public function get($id)
    {
        /** @var Anr $anr */
        $anr = $this->getRequest()->getAttribute('anr');
        $language = $this->params()->fromQuery("language");

        return $this->getPreparedJsonResponse([
            'data' => $this->anrInstanceMetadataService->getInstanceMetadata($anr, $id, $language),
        ]);
    }

    public function create($data)
    {
        /** @var Anr $anr */
        $anr = $this->getRequest()->getAttribute('anr');

        return $this->getSuccessfulJsonResponse([
            'id' => $this->anrInstanceMetadataService->create($anr, $data),
        ]);
    }

    public function delete($id)
    {
        $this->anrInstanceMetadataService->delete((int)$id);

        return $this->getSuccessfulJsonResponse();
    }

    public function update($id, $data)
    {
        $this->anrInstanceMetadataService->update((int)$id, $data);

        return $this->getSuccessfulJsonResponse();
    }
}
