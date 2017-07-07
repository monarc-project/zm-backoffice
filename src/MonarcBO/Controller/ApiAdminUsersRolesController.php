<?php
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) Cases is a registered trademark of SECURITYMADEIN.LU
 * @license   MyCases is licensed under the GNU Affero GPL v3 - See license.txt for more information
 */

namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractController;
use Zend\View\Model\JsonModel;

/**
 * Api Admin Users Roles Controller
 *
 * Class ApiAdminUsersRolesController
 * @package MonarcBO\Controller
 */
class ApiAdminUsersRolesController extends AbstractController
{
    protected $name = 'roles';

    /**
     * @inheritdoc
     */
    public function getList() {

        $request = $this->getRequest();
        $token = $request->getHeader('token');

        $currentUserRoles = $this->getService()->getByUserToken($token);

        return new JsonModel(array(
            'count' => count($currentUserRoles),
            $this->name => $currentUserRoles
        ));
    }

    /**
     * @inheritdoc
     */
    public function get($id)
    {
        $userRoles = $this->getService()->getByUserId($id);

        return new JsonModel(array(
            'count' => count($userRoles),
            $this->name => $userRoles
        ));
    }

    /**
     * @inheritdoc
     */
    public function create($data)
    {
        return $this->methodNotAllowed();
    }

    /**
     * @inheritdoc
     */
    public function update($id, $data)
    {
        return $this->methodNotAllowed();
    }

    /**
     * @inheritdoc
     */
    public function patch($id, $data)
    {
        return $this->methodNotAllowed();
    }

    /**
     * @inheritdoc
     */
    public function delete($id)
    {
        return $this->methodNotAllowed();
    }

}

