<?php

namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractController;
use Zend\View\Model\JsonModel;

class ApiAdminModelsController extends AbstractController
{
    public function create($data)
    {
        $service = $this->getService();
        $result = $service->create($data);

        if ($result) {
            return new JsonModel(array('status' => 'ok'));
        } else {
            return $this->getResponse()->setStatusCode(422);
        }
    }

    public function delete($id)
    {
        $service = $this->getService();
        $service->delete($id);

        return new JsonModel(array('status' => 'ok'));
    }

    public function update($id, $data)
    {
        var_dump($id);
        var_dump($data);
        die;
    }

}

