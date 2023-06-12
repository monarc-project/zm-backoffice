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
use Monarc\Core\Validator\InputValidator\InstanceRisk\UpdateInstanceRiskDataInputValidator;

class ApiAnrInstancesRisksController extends AbstractRestfulControllerRequestHandler
{
    use ControllerRequestResponseHandlerTrait;

    private InstanceRiskService $instanceRiskService;

    private UpdateInstanceRiskDataInputValidator $updateInstanceRiskDataInputValidator;

    public function __construct(
        InstanceRiskService $instanceRiskService,
        UpdateInstanceRiskDataInputValidator $updateInstanceRiskDataInputValidator
    ) {
        $this->instanceRiskService = $instanceRiskService;
        $this->updateInstanceRiskDataInputValidator = $updateInstanceRiskDataInputValidator;
    }

    public function update($id, $data)
    {
        /** @var Anr $anr */
        $anr = $this->getRequest()->getAttribute('anr');
        /** @var array $data */
        $this->validatePostParams($this->updateInstanceRiskDataInputValidator, $data);

        /** @var array $data */
        $instanceRisk = $this->instanceRiskService
            ->update($anr, (int)$id, $this->updateInstanceRiskDataInputValidator->getValidData());

        return $this->getPreparedJsonResponse([
            'id' => $instanceRisk->getId(),
            'threatRate' => $instanceRisk->getThreatRate(),
            'vulnerabilityRate' => $instanceRisk->getVulnerabilityRate(),
            'reductionAmount' => $instanceRisk->getReductionAmount(),
            'riskConfidentiality' => $instanceRisk->getRiskConfidentiality(),
            'riskIntegrity' => $instanceRisk->getRiskIntegrity(),
            'riskAvailability' => $instanceRisk->getRiskAvailability(),
        ]);
    }
}
