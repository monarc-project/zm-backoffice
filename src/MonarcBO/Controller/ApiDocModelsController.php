<?php
namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractController;
use Zend\View\Model\JsonModel;

/**
 * Api Doc Models Controller
 *
 * Class ApiDocModelsController
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

    /**
     * Get
     *
     * @param mixed $id
     * @return JsonModel
     */
    public function get($id)
    {
        $entity = $this->getService()->getEntity($id);
        var_dump($entity);die();
        return new JsonModel($entity);
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

