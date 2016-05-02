<?php
namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractController;
use Zend\View\Model\JsonModel;

/**
 * Api Admin ROles Controller
 *
 * Class ApiAdminRolesController
 * @package MonarcBO\Controller
 */
class ApiAdminRolesController extends AbstractController
{
    /**
     * Get List
     *
     * @return JsonModel
     */
    public function getList()
    {
        /** @var UserService $service */
        $service = $this->getService();
        return new JsonModel(array('count' => $service->getFilteredCount(),
            'roles' => $service->getList()));
    }
}

