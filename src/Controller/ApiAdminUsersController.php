<?php
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2019  SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Monarc\Core\Table\UserTable;
use Monarc\Core\Service\UserService;
use Throwable;
use Laminas\Mvc\Controller\AbstractRestfulController;
use Laminas\View\Model\JsonModel;

/**
 * Class ApiAdminUsersController
 * @package Monarc\BackOffice\Controller
 */
class ApiAdminUsersController extends AbstractRestfulController
{
    private const DEFAULT_LIMIT = 25;

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
        $searchString = $this->params()->fromQuery('filter', '');
        $status = $this->params()->fromQuery('status', 1);
        $filter = $status === 'all' ? null : ['status' => (int)$status];
        $page = $this->params()->fromQuery('page', 1);
        $limit = $this->params()->fromQuery('limit', static::DEFAULT_LIMIT);
        $order = $this->params()->fromQuery('order', '');

        $users = $this->userService->getUsersList($searchString, $filter, $order);

        return new JsonModel(array(
            'count' => \count($users),
            'users' => \array_slice($users, $page - 1, $limit),
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
            'role' => $user->getRolesArray(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function create($data)
    {
        $this->userService->create($data);

        return new JsonModel(array('status' => 'ok'));
    }

    /**
     * @inheritdoc
     */
    public function update($id, $data)
    {
        // TODO: add a request data filter.

        $this->userService->update($id, $data);

        return new JsonModel(array('status' => 'ok'));
    }

    public function patch($id, $data)
    {
        // TODO: add a request data filter.

        $this->userService->patch($id, $data);

        return new JsonModel(array('status' => 'ok'));
    }

    public function delete($id)
    {
        try {
            $this->userService->delete($id);
        } catch (Throwable $e) {
            return new JsonModel(array('status' => 'ko'));
        }

        return new JsonModel(array('status' => 'ok'));
    }
}
