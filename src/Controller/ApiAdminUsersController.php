<?php
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2019  SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

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

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
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
        if (count($this->dependencies)) {
            foreach ($entities as $key => $entity) {
                $this->formatDependencies($entities[$key], $this->dependencies);
            }
        }

        return new JsonModel(array(
            'count' => $this->userService->getFilteredCount($filter, $filterAnd),
            $this->name => $entities
        ));
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

        $this->userService->update($id, $data);

        return new JsonModel(array('status' => 'ok'));
    }

    public function delete($id)
    {
        if ($this->userService->delete($id)) {
            return new JsonModel(array('status' => 'ok'));
        }

        return new JsonModel(array('status' => 'ko'));
    }

    public function deleteList($data)
    {
        if ($this->userService->deleteList($data)) {
            return new JsonModel(array('status' => 'ok'));
        }

        return new JsonModel(array('status' => 'ko'));
    }
}
