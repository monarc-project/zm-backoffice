<?php
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2019  SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Monarc\Core\Controller\AbstractController;
use Monarc\Core\Service\ObjectObjectService;
use Laminas\View\Model\JsonModel;

/**
 * TODO: extend AbstractRestfulController and remove AbstractController.
 *
 * Class ApiObjectsObjectsController
 * @package Monarc\BackOffice\Controller
 */
class ApiObjectsObjectsController extends AbstractController
{
    public function __construct(ObjectObjectService $objectObjectService)
    {
        parent::__construct($objectObjectService);
    }

    /**
     * @inheritdoc
     */
    public function get($id)
    {
        return $this->methodNotAllowed();
    }

    /**
     * @inheritdoc
     */
    public function getList()
    {
        return $this->methodNotAllowed();
    }

    /**
     * @inheritdoc
     */
    public function update($id, $data)
    {
        // This works a little different that regular PUT calls - here we just expect a parameter "move" with the
        // value "up" or "down" to move the object. We can't edit any other field anyway.
        if (isset($data['move']) && in_array($data['move'], ['up', 'down'])) {
            /** @var ObjectObjectService $service */
            $service = $this->getService();
            $service->moveObject($id, $data['move']);
        }

        return new JsonModel(array("status" => "ok"));
    }

    /**
     * @inheritdoc
     */
    public function patch($id, $data)
    {
        return $this->methodNotAllowed();
    }
}
