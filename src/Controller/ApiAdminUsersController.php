<?php
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2019  SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Monarc\Core\Model\Table\UserTable;
use Monarc\Core\Service\UserService;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

/**
 * Class ApiAdminUsersController
 * @package Monarc\BackOffice\Controller
 */
class ApiAdminUsersController extends AbstractRestfulController
{
    protected $name = 'users';

    /** @var UserService */
    private $userService;

    /** @var UserTable */
    private $userTable;

    public function __construct(UserService $userService, UserTable $userTable)
    {
        $this->userService = $userService;
        $this->userTable = $userTable;
    }

    /**
     * @inheritdoc
     */
    public function getList()
    {
        $page = $this->params()->fromQuery('page');
        $limit = $this->params()->fromQuery('limit');
        $order = $this->params()->fromQuery('order');
        $filter = $this->params()->fromQuery('filter');
        $status = $this->params()->fromQuery('status', 1);

        $filterAnd = $status === 'all' ? null : ['status' => (int)$status];

        $entities = $this->userService->getList($page, $limit, $order, $filter, $filterAnd);

        return new JsonModel(array(
            'count' => $this->userService->getFilteredCount($filter, $filterAnd),
            'users' => $entities
        ));
    }

    public function get($id)
    {
        $user = $this->userTable->findById($id);

        // TODO: use a normalizer instead.
        return new JsonModel([
            'id' => $user->getId(),
            'status' => $user->getStatus(),
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'email' => $user->getEmail(),
            'language' => $user->getLanguage(),
            'role' => $user->getRoles(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function create($data)
    {
        // Security: Don't allow changing role, password, status and history fields. To clean later.
        if (isset($data['salt'])) {
            unset($data['salt']);
        }
        if (isset($data['dateStart'])) {
            unset($data['dateStart']);
        }
        if (isset($data['dateEnd'])) {
            unset($data['dateEnd']);
        }

        $this->userService->create($data);

        return new JsonModel(array('status' => 'ok'));
    }

    /**
     * @inheritdoc
     */
    public function update($id, $data)
    {
        $data = $this->filterRequestData($data)

        $this->userService->update($id, $data);

        return new JsonModel(array('status' => 'ok'));
    }

    public function patch($id, $data)
    {
        $data = $this->filterRequestData($data);

        $this->userService->patch($id, $data);

        return new JsonModel(array('status' => 'ok'));
    }

    public function delete($id)
    {
        if ($this->userService->delete($id)) {
            return new JsonModel(array('status' => 'ok'));
        }

        return new JsonModel(array('status' => 'ko'));
    }

    /**
     * @param $data
     *
     * @return mixed
     */
    private function filterRequestData($data)
    {
        // TODO: replace with a Filter.
        // Security: Don't allow changing role, password, status and history fields. To clean later.
        if (isset($data['status'])) {
            unset($data['status']);
        }
        if (isset($data['id'])) {
            unset($data['id']);
        }
        if (isset($data['salt'])) {
            unset($data['salt']);
        }
        if (isset($data['updatedAt'])) {
            unset($data['updatedAt']);
        }
        if (isset($data['updater'])) {
            unset($data['updater']);
        }
        if (isset($data['createdAt'])) {
            unset($data['createdAt']);
        }
        if (isset($data['creator'])) {
            unset($data['creator']);
        }
        if (isset($data['dateStart'])) {
            unset($data['dateStart']);
        }
        if (isset($data['dateEnd'])) {
            unset($data['dateEnd']);
        }
        return $data;
}
}
