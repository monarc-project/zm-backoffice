<?php

namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractController;
use Zend\View\Model\JsonModel;

class ApiAdminPasswordsController extends AbstractController
{

    public function create($data)
    {
        $service = $this->getService();

        //password forgotten
        if (array_key_exists('mail', $data)) {
            $service->passwordForgotten($data['mail']);
        }


        die;

        //return new JsonModel(array('status' => 'ok'));
    }
}

