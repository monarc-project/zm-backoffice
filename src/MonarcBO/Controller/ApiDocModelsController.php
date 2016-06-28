<?php
namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractController;
use Zend\View\Model\JsonModel;

/**
 * Api Admin Historicals Controller
 *
 * Class ApiAdminHistoricalsController
 * @package MonarcBO\Controller
 */
class ApiDocModelsController extends AbstractController
{
    protected $name = "docmodels";

    public function create($data)
    {
        $service = $this->getService();
        $file = $this->request->getFiles()->toArray();
        if(!empty($file['file'])){
            $data['path'] = $file['file'];
        }
        $service->create($data);

        return new JsonModel(array('status' => 'ok'));
    }

    public function get($id)
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
}

