<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2026 Luxembourg House of Cybersecurity LHC.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Monarc\Core\Controller\Handler\AbstractRestfulControllerRequestHandler;
use Monarc\Core\InputFormatter\Historical\GetHistoricalsInputFormatter;
use Monarc\Core\Controller\Handler\ControllerRequestResponseHandlerTrait;
use Monarc\Core\Service\HistoricalService;

class ApiAdminHistoricalController extends AbstractRestfulControllerRequestHandler
{
    use ControllerRequestResponseHandlerTrait;

    public function __construct(
        private GetHistoricalsInputFormatter $getHistoricalsInputFormatter,
        private HistoricalService $historicalService
    ) {
    }

    public function getList()
    {
        $formattedParams = $this->getFormattedInputParams($this->getHistoricalsInputFormatter);

        return $this->getPreparedJsonResponse([
            'count' => $this->historicalService->getCount($formattedParams),
            'historical' => $this->historicalService->getList($formattedParams),
        ]);
    }
}
