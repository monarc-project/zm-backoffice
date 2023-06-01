<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2023 Luxembourg House of Cybersecurity LHC.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Monarc\Core\Controller\Handler\AbstractRestfulControllerRequestHandler;
use Monarc\Core\Controller\Handler\ControllerRequestResponseHandlerTrait;
use Monarc\Core\Exception\Exception;
use Monarc\Core\Model\Entity\Anr;
use Monarc\Core\Service\OperationalRiskScaleService;

class ApiOperationalRisksScalesController extends AbstractRestfulControllerRequestHandler
{
    use ControllerRequestResponseHandlerTrait;

    private OperationalRiskScaleService $operationalRiskScaleService;

    public function __construct(OperationalRiskScaleService $operationalRiskScaleService)
    {
        $this->operationalRiskScaleService = $operationalRiskScaleService;
    }

    public function getList()
    {
        /** @var Anr $anr */
        $anr = $this->getRequest()->getAttribute('anr');
        $language = $this->params()->fromQuery('language');

        return $this->getPreparedJsonResponse([
            'data' => $this->operationalRiskScaleService->getOperationalRiskScales($anr, $language),
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
            'id' => $this->operationalRiskScaleService->createOperationalRiskScaleType($anr, $data),
        ]);
    }

    /**
     * @param array $data
     */
    public function deleteList($data)
    {
        $this->operationalRiskScaleService->deleteOperationalRiskScaleTypes($data);

        return $this->getSuccessfulJsonResponse();
    }

    /**
     * @param array $data
     */
    public function update($id, $data)
    {
        $this->operationalRiskScaleService->update((int)$id, $data);

        return $this->getSuccessfulJsonResponse();
    }

    public function patchList($data)
    {
        // TODO: add a validator for the limits...

        /** @var Anr $anr */
        $anr = $this->getRequest()->getAttribute('anr');

        if (isset($data['scaleValue'], $data['scaleIndex'])) {
            $this->operationalRiskScaleService->updateValueForAllScales($anr, $data);
        }

        if (isset($data['numberOfLevelForOperationalImpact'])) {
            $numberOfLevelForOperationalImpact = (int)$data['numberOfLevelForOperationalImpact'];

            if ($numberOfLevelForOperationalImpact > 20) {
                throw new Exception('Scales level must remain below 20 ', 412);
            }

            $this->operationalRiskScaleService->updateLevelsNumberOfOperationalRiskScale($anr, $data);
        }

        if (isset($data['probabilityMin'], $data['probabilityMax'])) {
            $probabilityMin = (int)$data['probabilityMin'];
            $probabilityMax = (int)$data['probabilityMax'];

            if ($probabilityMin > 20 || $probabilityMax > 20) {
                throw new Exception('Scales level must remain below 20 ', 412);
            }
            if ($probabilityMin >= $probabilityMax) {
                throw new Exception('Minimum cannot be greater than Maximum', 412);
            }

            $this->operationalRiskScaleService->updateMinMaxForOperationalRiskProbability($anr, $data);
        }

        return $this->getSuccessfulJsonResponse();
    }
}
