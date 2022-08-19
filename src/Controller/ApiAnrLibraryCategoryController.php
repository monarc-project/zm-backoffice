<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2022 SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Monarc\Core\Controller\Handler\AbstractRestfulControllerRequestHandler;
use Monarc\Core\Controller\Handler\ControllerRequestResponseHandlerTrait;
use Monarc\Core\Service\ObjectCategoryService;

class ApiAnrLibraryCategoryController extends AbstractRestfulControllerRequestHandler
{
    use ControllerRequestResponseHandlerTrait;

    private ObjectCategoryService $objectCategoryService;

    public function __construct(ObjectCategoryService $objectCategoryService)
    {
        $this->objectCategoryService = $objectCategoryService;
    }

    public function patch($id, $data)
    {
        // TODO: attach an use the middleware's attribute anr => obj.
        $anrId = (int)$this->params()->fromRoute('anrid');

        $data['anr'] = $anrId;

        $this->objectCategoryService->patchLibraryCategory($id, $data);

        return $this->getSuccessfulJsonResponse();
    }
}
