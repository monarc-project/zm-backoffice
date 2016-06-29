<?php

namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractController;
use Zend\View\Model\JsonModel;

class ApiAdminPasswordsController extends AbstractController
{
    /**
     * Create
     *
     * @param mixed $data
     * @return JsonModel
     */
    public function create($data)
    {
        $service = $this->getService();

        if ((array_key_exists('mail', $data)) && (!array_key_exists('password', $data))) {
            $service->passwordForgotten($data['mail']);
        } else if ((array_key_exists('mail', $data)) && (array_key_exists('password', $data)) && (array_key_exists('confirm', $data))){
            if ($data['password'] == $data['confirm']) {
                $service->newPassword($data['mail'], $data['password']);
            }
        }

        return new JsonModel(array('status' => 'ok'));
    }

    public function getList()
    {
        return $this->methodNotAllowed();
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

    public function delete($id)
    {
        return $this->methodNotAllowed();
    }
}

