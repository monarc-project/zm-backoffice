<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2023 Luxembourg House of Cybersecurity LHC.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Monarc\Core\Controller\Handler\AbstractRestfulControllerRequestHandler;
use Monarc\Core\Controller\Handler\ControllerRequestResponseHandlerTrait;
use Monarc\Core\InputFormatter\Scale\GetScalesInputFormatter;
use Monarc\Core\Model\Entity\Anr;
use Monarc\Core\Service\ScaleService;

class ApiAnrScalesController extends AbstractRestfulControllerRequestHandler
{
    use ControllerRequestResponseHandlerTrait;

    private ScaleService $scaleService;

    private GetScalesInputFormatter $getScalesInputFormatter;

    public function __construct(ScaleService $scaleService, GetScalesInputFormatter $getScalesInputFormatter)
    {
        $this->scaleService = $scaleService;
        $this->getScalesInputFormatter = $getScalesInputFormatter;
    }

    public function getList()
    {
        $formattedParams = $this->getFormattedInputParams($this->getScalesInputFormatter);
        $scales = $this->scaleService->getList($formattedParams);

        return $this->getPreparedJsonResponse([
            'count' => \count($scales),
            'scales' => $scales
        ]);
    }

    public function update($id, $data)
    {
        /** @var Anr $anr */
        $anr = $this->getRequest()->getAttribute('anr');

        /** @var array $data */
        $this->scaleService->update($anr, (int)$id, $data);

        return $this->getSuccessfulJsonResponse();
    }
}
