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
        if (!empty($data['email']) && empty($data['password'])) {
            try {
                $this->getService()->passwordForgotten($data['email']);
            } catch (\Exception $e) {
                // Ignore the exception: We don't want to leak any data
            }
        }

        //verify token
        if (!empty($data['token']) && empty($data['password'])) {
            $result =  $this->getService()->verifyToken($data['token']);

            return new JsonModel(array('status' => $result));
        }

        //change password not logged
        if (!empty($data['token']) && !empty($data['password']) && !empty($data['confirm'])){
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

