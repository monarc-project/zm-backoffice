<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2023 Luxembourg House of Cybersecurity LHC.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Laminas\Mvc\Controller\AbstractRestfulController;
use Monarc\Core\Controller\Handler\ControllerRequestResponseHandlerTrait;
use Monarc\Core\Service\HistoricalService;

class ApiAdminHistoricalController extends AbstractRestfulController
{
    use ControllerRequestResponseHandlerTrait;

    private HistoricalService $historicalService;

    public function __construct(HistoricalService $historicalService)
    {
        $this->historicalService = $historicalService;
    }

    public function getList()
    {
        $page = $this->params()->fromQuery('page');
        $limit = $this->params()->fromQuery('limit');
        $order = $this->params()->fromQuery('order');
        $filter = $this->params()->fromQuery('filter');

        return $this->getPreparedJsonResponse([
            'count' => $this->historicalService->getFilteredCount($filter),
            'historical' => $this->historicalService->getList($page, $limit, $order, $filter)
        ]);
    }
}

