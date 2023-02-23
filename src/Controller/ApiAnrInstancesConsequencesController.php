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
use Monarc\Core\Service\InstanceConsequenceService;
use Monarc\Core\Validator\InputValidator\InstanceConsequence\PatchConsequenceDataInputValidator;

class ApiAnrInstancesConsequencesController extends AbstractRestfulControllerRequestHandler
{
    use ControllerRequestResponseHandlerTrait;

    private InstanceConsequenceService $instanceConsequenceService;

    private PatchConsequenceDataInputValidator $patchConsequenceDataInputValidator;

    public function __construct(
        InstanceConsequenceService $instanceConsequenceService,
        PatchConsequenceDataInputValidator $patchConsequenceDataInputValidator
    ) {
        $this->instanceConsequenceService = $instanceConsequenceService;
        $this->patchConsequenceDataInputValidator = $patchConsequenceDataInputValidator;
    }

    public function patch($id, $data)
    {
        /** @var Anr $anr */
        $anr = $this->getRequest()->getAttribute('anr');

        $this->validatePostParams($this->patchConsequenceDataInputValidator, $data);

        $this->instanceConsequenceService
            ->patchConsequence($anr, $id, $this->patchConsequenceDataInputValidator->getValidData());

        return $this->getSuccessfulJsonResponse();
    }
}
