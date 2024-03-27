<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2024 Luxembourg House of Cybersecurity LHC.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Laminas\Mvc\Controller\AbstractRestfulController;
use Monarc\Core\Controller\Handler\ControllerRequestResponseHandlerTrait;
use Monarc\Core\Service\MeasureMeasureService;

class ApiMeasureMeasureController extends AbstractRestfulController
{
    use ControllerRequestResponseHandlerTrait;

    public function __construct(private MeasureMeasureService $measureMeasureService)
    {
    }

    public function getList()
    {
        /* Fetches all the measures links. */
        $measuresLinksData = $this->measureMeasureService->getList();

        return $this->getPreparedJsonResponse([
            'count' => \count($measuresLinksData),
            'measuresLinks' => $measuresLinksData,
        ]);
    }

    public function create($data)
    {
        if ($this->isBatchData($data)) {
            $this->measureMeasureService->createList($data);
        } else {
            $this->measureMeasureService->create($data);
        }

        return $this->getSuccessfulJsonResponse();
    }

    public function deleteList($data)
    {
        $masterMeasureUuid = $this->params()->fromQuery('masterMeasureUuid');
        $linkedMeasureUuid = $this->params()->fromQuery('linkedMeasureUuid');
        $this->measureMeasureService->delete($masterMeasureUuid, $linkedMeasureUuid);

        return $this->getSuccessfulJsonResponse();
    }
}
