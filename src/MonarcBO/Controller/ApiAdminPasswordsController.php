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
 * Api Admin Passwords Controller
 *
 * Class ApiAdminPasswordsController
 * @package MonarcBO\Controller
 */
class ApiAdminPasswordsController extends AbstractController
{
    /**
     * @inheritdoc
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

    /**
     * @inheritdoc
     */
    public function getList()
    {
        return $this->methodNotAllowed();
    }

    /**
     * @inheritdoc
     */
    public function get($id)
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
    public function patch($token, $data)
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

