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
use Monarc\Core\Service\InstanceRiskOpService;

class ApiAnrRisksOpController extends AbstractRestfulControllerRequestHandler
{
    use ControllerRequestResponseHandlerTrait;

    private InstanceRiskOpService $instanceRiskOpService;

    public function __construct(InstanceRiskOpService $instanceRiskOpService)
    {
        $this->instanceRiskOpService = $instanceRiskOpService;
    }

    /**
     * @param int $id Instance ID.
     */
    public function get($id)
    {
        /** @var Anr $anr */
        $anr = $this->getRequest()->getAttribute('anr');
        $params = $this->parseParams();

        $risks = $this->instanceRiskOpService->getOperationalRisks($anr, (int)$id, $params);

        return $this->getPreparedJsonResponse([
            'count' => \count($risks),
            'oprisks' => \array_slice($risks, ($params['page'] - 1) * $params['limit'], $params['limit']),
        ]);
    }

    public function getList()
    {
        /** @var Anr $anr */
        $anr = $this->getRequest()->getAttribute('anr');
        $params = $this->parseParams();

        $risks = $this->instanceRiskOpService->getOperationalRisks($anr, null, $params);

        return $this->getPreparedJsonResponse([
            'count' => \count($risks),
            'oprisks' => \array_slice($risks, ($params['page'] - 1) * $params['limit'], $params['limit']),
        ]);
    }

    protected function parseParams(): array
    {
        return [
            'keywords' => $this->params()->fromQuery("keywords"),
            'kindOfMeasure' => $this->params()->fromQuery("kindOfMeasure"),
            'order' => $this->params()->fromQuery("order", "maxRisk"),
            'order_direction' => $this->params()->fromQuery("order_direction", "desc"),
            'thresholds' => $this->params()->fromQuery("thresholds"),
            'page' => $this->params()->fromQuery("page", 1),
            'limit' => $this->params()->fromQuery("limit", 50),
        ];
    }
}
