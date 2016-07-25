<?php

namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractController;
use MonarcCore\Service\ObjectObjectService;
use Zend\View\Model\JsonModel;

/**
 * Api Objects Components Controller
 *
 * Class ApiObjectsObjectsController
 * @package MonarcBO\Controller
 */
class ApiObjectsObjectsController extends AbstractController
{
    public function get($id)
    {
        return $this->methodNotAllowed();
    }

    public function getList()
    {
        return $this->methodNotAllowed();
    }

    public function update($id, $data)
    {
        // This works a little different that regular PUT calls - here we just expect a parameter "move" with the
        // value "up" or "down" to move the object. We can't edit any other field anyway.
        if (array_key_exists('move', $data) && in_array($data['move'], ['up', 'down'])) {
            /** @var ObjectObjectService $service */
            $service = $this->getService();
            $service->moveObject($id, $data['move']);
        }

        return new JsonModel(array("status" => "ok"));
    }

    public function patch($id, $data)
    {
        return $this->methodNotAllowed();
    }
}

