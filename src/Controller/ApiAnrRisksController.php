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
use Monarc\Core\Service\InstanceRiskService;

class ApiAnrRisksController extends AbstractRestfulControllerRequestHandler
{
    use ControllerRequestResponseHandlerTrait;

    private InstanceRiskService $instanceRiskService;

    public function __construct(InstanceRiskService $instanceRiskService)
    {
        $this->instanceRiskService = $instanceRiskService;
    }

    public function get($id)
    {
        /** @var Anr $anr */
        $anr = $this->getRequest()->getAttribute('anr');
        $params = $this->prepareParams();

        $risks = $this->instanceRiskService->getInstanceRisks($anr, (int)$id, $params);

        return $this->getPreparedJsonResponse([
            'count' => \count($risks),
            'risks' => $params['limit'] > 0 ?
                \array_slice($risks, ($params['page'] - 1) * $params['limit'], $params['limit'])
                : $risks,
        ]);
    }

    public function getList()
    {
        /** @var Anr $anr */
        $anr = $this->getRequest()->getAttribute('anr');

        $params = $this->prepareParams();

        $risks = $this->instanceRiskService->getInstanceRisks($anr, null, $params);

        return $this->getPreparedJsonResponse([
            'count' => \count($risks),
            'risks' => $params['limit'] > 0 ?
                \array_slice($risks, ($params['page'] - 1) * $params['limit'], $params['limit'])
                : $risks,
        ]);
    }

    protected function prepareParams(): array
    {
        $params = $this->params();

        return [
            'keywords' => $params->fromQuery('keywords'),
            'kindOfMeasure' => $params->fromQuery('kindOfMeasure'),
            'order' => $params->fromQuery('order', 'maxRisk'),
            'order_direction' => $params->fromQuery('order_direction', 'desc'),
            'thresholds' => $params->fromQuery('thresholds'),
            'page' => $params->fromQuery('page', 1),
            'limit' => $params->fromQuery('limit', 50)
        ];
    }
}
