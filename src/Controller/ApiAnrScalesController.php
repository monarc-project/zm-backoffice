<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2024 Luxembourg House of Cybersecurity LHC.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Monarc\Core\Controller\Handler\AbstractRestfulControllerRequestHandler;
use Monarc\Core\Controller\Handler\ControllerRequestResponseHandlerTrait;
use Monarc\Core\Entity\Anr;
use Monarc\Core\Service\ScaleService;
use Monarc\Core\Validator\InputValidator\Scale\UpdateScalesDataInputValidator;

class ApiAnrScalesController extends AbstractRestfulControllerRequestHandler
{
    use ControllerRequestResponseHandlerTrait;

    public function __construct(
        private ScaleService $scaleService,
        private UpdateScalesDataInputValidator $updateScalesDataInputValidator
    ) {
    }

    public function getList()
    {
        /** @var Anr $anr */
        $anr = $this->getRequest()->getAttribute('anr');

        $scales = $this->scaleService->getList($anr);

        return $this->getPreparedJsonResponse([
            'count' => \count($scales),
            'scales' => $scales
        ]);
    }

    public function update($id, $data)
    {
        $this->validatePostParams($this->updateScalesDataInputValidator, $data);

        /** @var Anr $anr */
        $anr = $this->getRequest()->getAttribute('anr');
        $this->scaleService->update($anr, (int)$id, $this->updateScalesDataInputValidator->getValidData());

        return $this->getSuccessfulJsonResponse();
    }
}
