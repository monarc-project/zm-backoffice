<?php
namespace MonarcCore\Controller;

use Zend\View\Model\JsonModel;

class IndexController extends AbstractController
{
    public function getList()
    {
        //$recipes = $this->getEntityTable()->fetchAll();
        return new JsonModel($this->getService()->getList());
        return $this->methodNotAllowed();
    }
 
    public function get($id)
    {
        return new JsonModel($this->getService()->getEntity($id));
        return $this->methodNotAllowed();
    }
 
    public function create($data)
    {
        # code...
    }
 
    public function update($id, $data)
    {
        # code...
    }
 
    public function delete($id)
    {
        # code...
    }
}

