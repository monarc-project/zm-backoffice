<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2023 Luxembourg House of Cybersecurity LHC.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Monarc\Core\Controller\Handler\AbstractRestfulControllerRequestHandler;
use Monarc\Core\Controller\Handler\ControllerRequestResponseHandlerTrait;
use Monarc\Core\Service\OperationalRiskScaleCommentService;

class ApiOperationalRisksScalesCommentsController extends AbstractRestfulControllerRequestHandler
{
    use ControllerRequestResponseHandlerTrait;

    private OperationalRiskScaleCommentService $operationalRiskScaleCommentService;

    public function __construct(OperationalRiskScaleCommentService $operationalRiskScaleCommentService)
    {
        $this->operationalRiskScaleCommentService = $operationalRiskScaleCommentService;
    }

    /**
     * @param array $data
     */
    public function update($id, $data)
    {
        $this->operationalRiskScaleCommentService->update((int)$id, $data);

        return $this->getSuccessfulJsonResponse();
    }
}
