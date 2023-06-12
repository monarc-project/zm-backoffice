<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2023 Luxembourg House of Cybersecurity LHC.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Laminas\View\Model\JsonModel;
use Monarc\Core\Controller\Handler\AbstractRestfulControllerRequestHandler;
use Monarc\Core\Controller\Handler\ControllerRequestResponseHandlerTrait;
use Monarc\Core\InputFormatter\ScaleComment\GetScaleCommentsInputFormatter;
use Monarc\Core\Model\Entity\Anr;
use Monarc\Core\Service\ScaleCommentService;

class ApiAnrScalesCommentsController extends AbstractRestfulControllerRequestHandler
{
    use ControllerRequestResponseHandlerTrait;

    private ScaleCommentService $scaleCommentService;

    private GetScaleCommentsInputFormatter $getScaleCommentsInputFormatter;

    public function __construct(
        ScaleCommentService $scaleCommentService,
        GetScaleCommentsInputFormatter $getScaleCommentsInputFormatter
    ) {
        $this->scaleCommentService = $scaleCommentService;
        $this->getScaleCommentsInputFormatter = $getScaleCommentsInputFormatter;
    }

    public function getList()
    {
        $formattedParams = $this->getFormattedInputParams($this->getScaleCommentsInputFormatter);
        $formattedParams->setFilterValueFor('scale', (int)$this->params()->fromRoute('scaleId'));

        $comments = $this->scaleCommentService->getList($formattedParams);

        return new JsonModel(array(
            'count' => \count($comments),
            'comments' => $comments,
            // TODO: check if we need those fields:
            //'anr' => $anrId,
            //'scale' => $scale,
        ));
    }

    public function create($data)
    {
        /** @var Anr $anr */
        $anr = $this->getRequest()->getAttribute('anr');

        $scaleComment = $this->scaleCommentService->create($anr, $data);

        return $this->getSuccessfulJsonResponse(['id' => $scaleComment->getId()]);
    }

    public function update($id, $data)
    {
        /** @var Anr $anr */
        $anr = $this->getRequest()->getAttribute('anr');

        $scaleComment = $this->scaleCommentService->update($anr, (int)$id, $data);

        return $this->getSuccessfulJsonResponse(['id' => $scaleComment->getId()]);
    }
}
