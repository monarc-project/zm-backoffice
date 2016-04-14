<?php
namespace MonarcBO\Service;

use MonarcCore\Service\AbstractService;

class UserService extends AbstractService
{
    protected $userTable;
    protected $userEntity;

    public function getList()
    {
        return $this->get('userTable')->fetchAll();
    }

    public function getEntity($id)
    {
        return $this->get('userTable')->get($id);
    }
}