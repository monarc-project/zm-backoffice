<?php

namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractController;
use Zend\Crypt\Password\Bcrypt;
use Zend\View\Model\JsonModel;

class ApiModelsController extends AbstractController
{
    public function create($data)
    {
        $service = $this->getService();
        $service->create($data);

        return new JsonModel(array('status' => 'ok'));
    }

    public function delete($id)
    {
        $service = $this->getService();
        $service->delete($id);

        return new JsonModel(array('status' => 'ok'));
    }

    public function update($id, $data)
    {
        //TODO
    }

}

