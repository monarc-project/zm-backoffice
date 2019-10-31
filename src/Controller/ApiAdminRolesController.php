<?php
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2019  SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Monarc\Core\Service\RoleService;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

/**
 * TODO: seems it's not used.
 *
 * Class ApiAdminRolesController
 * @package Monarc\BackOffice\Controller
 */
class ApiAdminRolesController extends AbstractRestfulController
{
    /** @var RoleService */
    private $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * @inheritdoc
     */
    public function getList()
    {
        return new JsonModel(array(
            'count' => $this->roleService->getFilteredCount(),
            'roles' => $this->roleService->getList()
        ));
    }
}
