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
use Monarc\Core\Service\InstanceRiskOpService;
use Monarc\Core\Validator\InputValidator\InstanceRiskOp\PatchInstanceRiskOpDataInputValidator;
use Monarc\Core\Validator\InputValidator\InstanceRiskOp\UpdateInstanceRiskOpDataInputValidator;

class ApiAnrInstancesRisksOpController extends AbstractRestfulControllerRequestHandler
{
    use ControllerRequestResponseHandlerTrait;

    private InstanceRiskOpService $instanceRiskOpService;
    private PatchInstanceRiskOpDataInputValidator $patchInstanceRiskOpDataInputValidator;
    private UpdateInstanceRiskOpDataInputValidator $updateInstanceRiskOpDataInputValidator;

    public function __construct(
        InstanceRiskOpService $instanceRiskOpService,
        UpdateInstanceRiskOpDataInputValidator $updateInstanceRiskOpDataInputValidator,
        PatchInstanceRiskOpDataInputValidator $patchInstanceRiskOpDataInputValidator
    ) {
        $this->instanceRiskOpService = $instanceRiskOpService;
        $this->updateInstanceRiskOpDataInputValidator = $updateInstanceRiskOpDataInputValidator;
        $this->patchInstanceRiskOpDataInputValidator = $patchInstanceRiskOpDataInputValidator;
    }

    /**
     * @param array $data
     */
    public function update($id, $data)
    {
        $this->validatePostParams($this->updateInstanceRiskOpDataInputValidator, $data);
        /** @var Anr $anr */
        $anr = $this->getRequest()->getAttribute('anr');

        /** @var array $data */
        $instanceRiskOp = $this->instanceRiskOpService->update(
            $anr,
            (int)$id,
            $this->updateInstanceRiskOpDataInputValidator->getValidData()
        );

        return $this->getPreparedJsonResponse([
            'cacheBrutRisk' => $instanceRiskOp->getCacheBrutRisk(),
            'cacheNetRisk' => $instanceRiskOp->getCacheNetRisk(),
            'cacheTargetedRisk' => $instanceRiskOp->getCacheTargetedRisk(),
        ]);
    }

    public function patch($id, $data)
    {
        $this->validatePostParams($this->patchInstanceRiskOpDataInputValidator, $data);
        /** @var Anr $anr */
        $anr = $this->getRequest()->getAttribute('anr');

        $instanceRiskOp = $this->instanceRiskOpService->updateScaleValue(
            $anr,
            (int)$id,
            $this->patchInstanceRiskOpDataInputValidator->getValidData()
        );

        return $this->getPreparedJsonResponse([
            'cacheBrutRisk' => $instanceRiskOp->getCacheBrutRisk(),
            'cacheNetRisk' => $instanceRiskOp->getCacheNetRisk(),
            'cacheTargetedRisk' => $instanceRiskOp->getCacheTargetedRisk(),
        ]);
    }
}
