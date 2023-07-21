<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2023 Luxembourg House of Cybersecurity LHC.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Exception;
use Monarc\Core\Controller\Handler\ControllerRequestResponseHandlerTrait;
use Monarc\Core\Service\PasswordService;
use Laminas\Mvc\Controller\AbstractRestfulController;

class ApiAdminPasswordsController extends AbstractRestfulController
{
    use ControllerRequestResponseHandlerTrait;

    private PasswordService $passwordService;

    public function __construct(PasswordService $passwordService)
    {
        $this->passwordService = $passwordService;
    }

    /**
     * @param array $data
     */
    public function create($data)
    {
        /* Password forgotten. */
        if (!empty($data['email']) && empty($data['password'])) {
            try {
                $this->passwordService->passwordForgotten($data['email']);
            } catch (Exception $e) {
                throw new Exception('Password reset error occurred. Please try again later.', 422);
            }
        }

        /* Verify token. */
        if (!empty($data['token']) && empty($data['password'])) {
            return $this->getPreparedJsonResponse([
                'status' => $this->passwordService->verifyToken($data['token'])
            ]);
        }

        /* Change password, when user is not logged in. */
        if (!empty($data['token']) && !empty($data['password']) && !empty($data['confirm'])) {
            if ($data['password'] !== $data['confirm']) {
                throw new Exception('Password and its confirmation have to be equal.', 422);
            }

            $this->passwordService->newPasswordByToken($data['token'], $data['password']);
        }

        return $this->getSuccessfulJsonResponse();
    }
}
