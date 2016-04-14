<?php
namespace MonarcBO\Service;

use MonarcBO\Model\Entity\User;
use MonarcBO\Model\Table\UserTable;
use MonarcCore\Service\AbstractService;

class UserService extends AbstractService
{
    protected $userTable;
    protected $userEntity;

    public function getTotalCount()
    {
        /** @var UserTable $userTable */
        $userTable = $this->get('userTable');
        return $userTable->count();
    }

    public function getFilteredCount($page = 1, $limit = 25, $order = null, $filter = null) {
        /** @var UserTable $userTable */
        $userTable = $this->get('userTable');

        return $userTable->countFiltered($page, $limit, $this->parseFrontendOrder($order),
            $this->parseFrontendFilter($filter, array('firstname', 'lastname', 'email')));
    }

    public function getList($page = 1, $limit = 25, $order = null, $filter = null)
    {
        /** @var UserTable $userTable */
        $userTable = $this->get('userTable');

        return $userTable->fetchAllFiltered($page, $limit, $this->parseFrontendOrder($order),
            $this->parseFrontendFilter($filter, array('firstname', 'lastname', 'email')));
    }

    public function getEntity($id)
    {
        return $this->get('userTable')->get($id);
    }

    public function create($data)
    {
        /** @var UserTable $userTable */
        $userTable = $this->get('userTable');

        $entity = new User();
        $entity->exchangeArray($data);

        $userTable->save($entity);
    }
}