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
     * @param int|null $id Instance ID.
     */
    public function get($id)
    {
        /** @var Anr $anr */
        $anr = $this->getRequest()->getAttribute('anr');
        $params = $this->parseParams();
        $id = $id === null ? null : (int)$id;

        $risks = $this->instanceRiskOpService->getOperationalRisks($anr, (int)$id, $params);

        return $this->getPreparedJsonResponse([
            'count' => \count($risks),
            'oprisks' => \array_slice($risks, ($params['page'] - 1) * $params['limit'], $params['limit']),
        ]);
    }

    public function getList()
    {
        return $this->get(null);
    }

    protected function parseParams(): array
    {
        $params = $this->params();

        return [
            'keywords' => $params->fromQuery('keywords'),
            'kindOfMeasure' => $params->fromQuery('kindOfMeasure'),
            'order' => $params->fromQuery('order', 'maxRisk'),
            'order_direction' => $params->fromQuery('order_direction', 'desc'),
            'thresholds' => $params->fromQuery('thresholds'),
            'page' => (int)$params->fromQuery('page', 1),
            'limit' => (int)$params->fromQuery('limit', 50),
        ];
    }
}
