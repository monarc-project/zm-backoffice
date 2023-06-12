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

class ApiAnrInstancesRisksOpController extends AbstractRestfulControllerRequestHandler
{
    use ControllerRequestResponseHandlerTrait;

    private InstanceRiskOpService $instanceRiskOpService;

    public function __construct(InstanceRiskOpService $instanceRiskOpService)
    {
        $this->instanceRiskOpService = $instanceRiskOpService;
    }

    public function update($id, $data)
    {
        /** @var Anr $anr */
        $anr = $this->getRequest()->getAttribute('anr');

        /** @var array $data */
        $instanceRiskOp = $this->instanceRiskOpService->update($anr, (int)$id, $data);

        return $this->getPreparedJsonResponse([
            'cacheBrutRisk' => $instanceRiskOp->getCacheBrutRisk(),
            'cacheNetRisk' => $instanceRiskOp->getCacheNetRisk(),
            'cacheTargetedRisk' => $instanceRiskOp->getCacheTargetedRisk(),
        ]);
    }

    public function patch($id, $data)
    {
        /** @var Anr $anr */
        $anr = $this->getRequest()->getAttribute('anr');

        $instanceRiskOp = $this->instanceRiskOpService->updateScaleValue($anr, (int)$id, $data);

        return $this->getPreparedJsonResponse([
            'cacheBrutRisk' => $instanceRiskOp->getCacheBrutRisk(),
            'cacheNetRisk' => $instanceRiskOp->getCacheNetRisk(),
            'cacheTargetedRisk' => $instanceRiskOp->getCacheTargetedRisk(),
        ]);
    }
}
