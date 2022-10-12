<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2022 SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Monarc\Core\Controller\Handler\AbstractRestfulControllerRequestHandler;
use Monarc\Core\Controller\Handler\ControllerRequestResponseHandlerTrait;
use Monarc\Core\Exception\Exception;
use Monarc\Core\Model\Entity\Anr;
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
        /** @var Anr|null $anr */
        $anr = $this->getRequest()->getAttribute('anr');

        $libraryCategories = $this->objectService->getLibraryTreeStructure($anr);

        return $this->getPreparedJsonResponse([
            'categories' => $libraryCategories,
        ]);
    }

    public function create($data)
    {
        /** @var Anr|null $anr */
        $anr = $this->getRequest()->getAttribute('anr');

        if (!isset($data['objectId'], $data['categoryId'])) {
            throw new Exception('One of "objectId" or "categoryId" parameter is mandatory.');
        }

        $object = null;
        if (isset($data['objectId'])) {
            $object = $this->objectService->attachObjectToAnr($data['objectId'], $anr);
        } else {
            $this->objectService->attachCategoryObjectsToAnr((int)$data['categoryId'], $anr);
        }

        return $this->getSuccessfulJsonResponse([
            'id' => $object !== null ? $object->getUuid() : $data['categoryId'],
        ]);
    }

    public function delete($id)
    {
        /** @var Anr|null $anr */
        $anr = $this->getRequest()->getAttribute('anr');

        $this->objectService->detachObjectFromAnr($id, $anr);

        return $this->getSuccessfulJsonResponse();
    }
}
