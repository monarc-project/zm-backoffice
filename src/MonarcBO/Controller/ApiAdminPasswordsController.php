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
     * @throws \Exception
     */
    public function create($data)
    {
        //password forgotten
        if ((array_key_exists('email', $data)) && (!array_key_exists('password', $data))) {
            $this->getService()->passwordForgotten($data['email']);
        }

        //verify token
        if ((array_key_exists('token', $data)) && (!array_key_exists('password', $data))) {
            $result =  $this->getService()->verifyToken($data['token']);

            return new JsonModel(array('status' => $result));
        }

        //change password not logged
        if ((array_key_exists('token', $data)) && (array_key_exists('password', $data)) && (array_key_exists('confirm', $data))){
            if ($data['password'] == $data['confirm']) {
                $this->getService()->newPasswordByToken($data['token'], $data['password']);
            } else {
                throw  new \Exception('Password must be the same', 422);
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

    public function patch($token, $data)
    {
        return $this->methodNotAllowed();
    }

    public function delete($id)
    {
        return $this->methodNotAllowed();
    }
}

