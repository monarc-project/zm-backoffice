<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2023 Luxembourg House of Cybersecurity LHC.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Monarc\Core\Controller\Handler\AbstractRestfulControllerRequestHandler;
use Monarc\Core\Controller\Handler\ControllerRequestResponseHandlerTrait;
use Monarc\Core\Entity\Anr;
use Monarc\Core\Service\AnrInstanceMetadataFieldService;

class ApiAnrInstancesMetadataFieldsController extends AbstractRestfulControllerRequestHandler
{
    use ControllerRequestResponseHandlerTrait;

    private AnrInstanceMetadataFieldService $anrInstanceMetadataFieldService;

    public function __construct(AnrInstanceMetadataFieldService $anrInstanceMetadataFieldService)
    {
        $this->anrInstanceMetadataFieldService = $anrInstanceMetadataFieldService;
    }

    public function getList()
    {
        /** @var Anr $anr */
        $anr = $this->getRequest()->getAttribute('anr');
        $language = $this->params()->fromQuery("language");

        return $this->getPreparedJsonResponse([
            'data' => $this->anrInstanceMetadataFieldService->getList($anr, $language),
        ]);
    }

    public function get($id)
    {
        /** @var Anr $anr */
        $anr = $this->getRequest()->getAttribute('anr');
        $language = $this->params()->fromQuery("language");

        return $this->getPreparedJsonResponse([
            'data' => $this->anrInstanceMetadataFieldService->getAnrInstanceMetadataField($anr, (int)$id, $language),
        ]);
    }

    /**
     * @param array $data
     */
    public function create($data)
    {
        /** @var Anr $anr */
        $anr = $this->getRequest()->getAttribute('anr');

        return $this->getSuccessfulJsonResponse([
            'id' => $this->anrInstanceMetadataFieldService->create($anr, $data)->getId(),
        ]);
    }

    public function delete($id)
    {
        /** @var Anr $anr */
        $anr = $this->getRequest()->getAttribute('anr');

        $this->anrInstanceMetadataFieldService->delete($anr, (int)$id);

        return $this->getSuccessfulJsonResponse();
    }

    /**
     * @param array $data
     */
    public function update($id, $data)
    {
        /** @var Anr $anr */
        $anr = $this->getRequest()->getAttribute('anr');

        $this->anrInstanceMetadataFieldService->update($anr, (int)$id, $data);

        return $this->getSuccessfulJsonResponse();
    }
}
