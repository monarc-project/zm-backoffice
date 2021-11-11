<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2021  SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Monarc\Core\Exception;
use Monarc\Core\Service\UserRoleService;
use Laminas\Mvc\Controller\AbstractRestfulController;
use Laminas\View\Model\JsonModel;

/**
 * Api Admin Users Roles Controller
 *
 * Class ApiAdminUsersRolesController
 * @package Monarc\BackOffice\Controller
 */
class ApiAdminUsersRolesController extends AbstractRestfulController
{
    /** @var UserRoleService */
    private $userRoleService;

    public function __construct(UserRoleService $userRoleService)
    {
        $this->userRoleService = $userRoleService;
    }

    public function getList()
    {
        $token = $this->getRequest()->getHeader('token');
        if ($token === false) {
            throw new UserNotLoggedInException('The user token is not defined. Please login', 403);
        }

        $currentUserRoles = $this->userRoleService->getUserRolesByToken($token->getFieldValue());

        return new JsonModel([
            'count' => \count($currentUserRoles),
            'roles' => $currentUserRoles,
        ]);
    }

    public function get($id)
    {
        $userRoles = $this->userRoleService->getUserRolesByUserId((int)$id);

        return new JsonModel([
            'count' => \count($userRoles),
            'roles' => $userRoles
        ]);
    }
}
