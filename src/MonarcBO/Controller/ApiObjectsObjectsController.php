<?php
/**
 * @link      https://github.com/CASES-LU for the canonical source repository
 * @copyright Copyright (c) Cases is a registered trademark of SECURITYMADEIN.LU
 * @license   MyCases is licensed under the GNU Affero GPL v3 - See license.txt for more information
 */

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

