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
    protected $name = 'roles';

    /**
     * Get List
     *
     * @return JsonModel
     */
    public function getList()
    {
        $service = $this->getService();
        return new JsonModel(array(
            'count' => $service->getFilteredCount(),
            $this->name => $service->getList()
        ));
    }

    public function get($id)
    {
        return $this->methodNotAllowed();
    }

    public function create($data)
    {
        return $this->methodNotAllowed();
    }

    public function update($id, $data)
    {
        return $this->methodNotAllowed();
    }

    public function patch($id, $data)
    {
        return $this->methodNotAllowed();
    }

    public function delete($id)
    {
        return $this->methodNotAllowed();
    }
}

