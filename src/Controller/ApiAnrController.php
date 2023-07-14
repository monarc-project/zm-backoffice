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
use Monarc\Core\Service\AnrService;
use Monarc\Core\Validator\InputValidator\Anr\PatchThresholdsDataInputValidator;

class ApiAnrController extends AbstractRestfulControllerRequestHandler
{
    use ControllerRequestResponseHandlerTrait;

    private PatchThresholdsDataInputValidator $patchThresholdsDataInputValidator;

    private AnrService $anrService;

    public function __construct(
        PatchThresholdsDataInputValidator $patchThresholdsDataInputValidator,
        AnrService $amvService
    ) {
        $this->patchThresholdsDataInputValidator = $patchThresholdsDataInputValidator;
        $this->anrService = $amvService;
    }

    public function patchList($data)
    {
        $this->validatePostParams($this->patchThresholdsDataInputValidator, $data);
        /** @var Anr|null $anr */
        $anr = $this->getRequest()->getAttribute('anr');

        $this->anrService
            ->patchAcceptanceThresholdValues($anr, $this->patchThresholdsDataInputValidator->getValidData());

        return $this->getSuccessfulJsonResponse();
    }
}
