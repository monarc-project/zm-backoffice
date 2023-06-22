<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2023 Luxembourg House of Cybersecurity LHC.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Monarc\Core\Controller\Handler\AbstractRestfulControllerRequestHandler;
use Monarc\Core\Controller\Handler\ControllerRequestResponseHandlerTrait;
use Monarc\Core\InputFormatter\ScaleImpactType\GetScaleImpactTypesInputFormatter;
use Monarc\Core\Model\Entity\Anr;
use Monarc\Core\Service\ScaleImpactTypeService;

class ApiAnrScalesTypesController extends AbstractRestfulControllerRequestHandler
{
    use ControllerRequestResponseHandlerTrait;

    private ScaleImpactTypeService $scaleImpactTypeService;

    private GetScaleImpactTypesInputFormatter $getScaleImpactTypesInputFormatter;

    public function __construct(
        ScaleImpactTypeService $scaleImpactTypeService,
        GetScaleImpactTypesInputFormatter $getScaleImpactTypesInputFormatter
    ) {
        $this->scaleImpactTypeService = $scaleImpactTypeService;
        $this->getScaleImpactTypesInputFormatter = $getScaleImpactTypesInputFormatter;
    }

    public function getList()
    {
        $formattedParams = $this->getFormattedInputParams($this->getScaleImpactTypesInputFormatter);

        $scaleImpactTypesList = $this->scaleImpactTypeService->getList($formattedParams);

        return $this->getPreparedJsonResponse([
            'count' => \count($scaleImpactTypesList),
            'types' => $scaleImpactTypesList,
        ]);
    }

    public function create($data)
    {
        /** @var Anr $anr */
        $anr = $this->getRequest()->getAttribute('anr');

        $scaleImpactType = $this->scaleImpactTypeService->create($anr, $data);

        return $this->getSuccessfulJsonResponse([
            'id' => $scaleImpactType->getId(),
        ]);
    }

    public function patch($id, $data)
    {
        /** @var Anr $anr */
        $anr = $this->getRequest()->getAttribute('anr');

        $this->scaleImpactTypeService->patch($anr, (int)$id, $data);

        return $this->getSuccessfulJsonResponse();
    }

    public function delete($id)
    {
        /** @var Anr $anr */
        $anr = $this->getRequest()->getAttribute('anr');

        $this->scaleImpactTypeService->delete($anr, (int)$id);

        return $this->getSuccessfulJsonResponse();
    }
}