<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2022 SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Monarc\Core\Controller\Handler\AbstractRestfulControllerRequestHandler;
use Monarc\Core\Controller\Handler\ControllerRequestResponseHandlerTrait;
use Monarc\Core\Model\Entity\Anr;
use Monarc\Core\Service\SoaScaleCommentService;

class ApiSoaScaleCommentController extends AbstractRestfulControllerRequestHandler
{
    use ControllerRequestResponseHandlerTrait;

    private SoaScaleCommentService $soaScaleCommentService;

    public function __construct(SoaScaleCommentService $soaScaleCommentService)
    {
        $this->soaScaleCommentService = $soaScaleCommentService;
    }

    public function getList()
    {
        /** @var Anr $anr */
        $anr = $this->getRequest()->getAttribute('anr');
        $language = $this->params()->fromQuery("language");

        return $this->getPreparedJsonResponse([
            'data' => $this->soaScaleCommentService->getSoaScaleComments($anr, $language),
        ]);
    }

    public function update($id, $data)
    {
        $this->soaScaleCommentService->update((int)$id, $data);

        return $this->getSuccessfulJsonResponse();
    }

    public function patchList($data)
    {
        /** @var Anr $anr */
        $anr = $this->getRequest()->getAttribute('anr');
        $language = $this->params()->fromQuery("language");

        $this->soaScaleCommentService->createOrHideSoaScaleComment($anr, $data);

        return $this->getSuccessfulJsonResponse([
            'data' => $this->soaScaleCommentService->getSoaScaleComments($anr, $language),
        ]);
    }
}
