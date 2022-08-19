<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2022 SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Monarc\Core\Controller\Handler\AbstractRestfulControllerRequestHandler;
use Monarc\Core\Controller\Handler\ControllerRequestResponseHandlerTrait;
use Monarc\Core\Model\Entity\AnrSuperClass;
use Monarc\Core\Service\ObjectService;

class ApiAnrLibraryController extends AbstractRestfulControllerRequestHandler
{
    use ControllerRequestResponseHandlerTrait;

    private ObjectService $objectService;

    public function __construct(ObjectService $objectService)
    {
        $this->objectService = $objectService;
    }

    public function getList()
    {
        /** @var AnrSuperClass|null $anr */
        $anr = $this->getRequest()->getAttribute('anr');

        $libraryCategories = $this->objectService->getLibraryTreeStructure($anr);

        return $this->getPreparedJsonResponse([
            'categories' => $libraryCategories,
        ]);
    }

    public function create($data)
    {
        $anrId = $this->params()->fromRoute('anrid');

        // TODO: add to validate.
        if (!isset($data['objectId'])) {
            throw new \Monarc\Core\Exception\Exception('objectId is missing');
        }

        $id = $this->objectService->attachObjectToAnr($data['objectId'], $anrId);

        return $this->getSuccessfulJsonResponse([
            'id' => $id,
        ]);
    }

    public function delete($id)
    {
        $this->objectService->detachObjectFromAnr($id, (int)$this->params()->fromRoute('anrid'));

        return $this->getSuccessfulJsonResponse();
    }
}
