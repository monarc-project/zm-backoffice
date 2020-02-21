<?php
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2019  SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Exception;
use Monarc\Core\Service\PasswordService;
use Laminas\Mvc\Controller\AbstractRestfulController;
use Laminas\View\Model\JsonModel;

/**
 * Api Admin Passwords Controller
 *
 * Class ApiAdminPasswordsController
 * @package Monarc\BackOffice\Controller
 */
class ApiAdminPasswordsController extends AbstractRestfulController
{
    /** @var PasswordService */
    private $passwordService;

    public function __construct(PasswordService $passwordService)
    {
        $this->passwordService = $passwordService;
    }

    /**
     * @inheritdoc
     */
    public function create($data)
    {
        //password forgotten
        if (!empty($data['email']) && empty($data['password'])) {
            try {
                $this->passwordService->passwordForgotten($data['email']);
            } catch (Exception $e) {
                // Ignore the exception: We don't want to leak any data
            }
        }

        //verify token
        if (!empty($data['token']) && empty($data['password'])) {
            $result = $this->passwordService->verifyToken($data['token']);

            return new JsonModel(array('status' => $result));
        }

        //change password not logged
        if (!empty($data['token']) && !empty($data['password']) && !empty($data['confirm'])) {
            if ($data['password'] !== $data['confirm']) {
                throw new Exception('Password must be the same', 422);
            }

            throw new Exception('Password must be the same', 422);
        }

        return new JsonModel(array('status' => 'ok'));
    }
}
