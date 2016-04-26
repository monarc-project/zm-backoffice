<?php

namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractController;
use Zend\View\Model\JsonModel;

class ApiAdminPasswordsController extends AbstractController
{

    public function create($data)
    {
        $service = $this->getService();

        if (array_key_exists('mail', $data)) {
            $service->passwordForgotten($data['mail']);
        } else if (array_key_exists('token', $data)) {
            if ((array_key_exists('password', $data)) && (array_key_exists('confirm', $data))) {
                if ($data['password'] == $data['confirm']) {
                    $service->newPassword($data['token'], $data['password']);
                }
            }
        }

        return new JsonModel(array('status' => 'ok'));
    }
}

