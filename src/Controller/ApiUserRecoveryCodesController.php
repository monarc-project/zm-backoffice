<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2023 Luxembourg House of Cybersecurity LHC.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Monarc\Core\Controller\Handler\ControllerRequestResponseHandlerTrait;
use Monarc\Core\Exception\Exception;
use Monarc\Core\Model\Entity\UserSuperClass;
use Monarc\Core\Service\ConnectedUserService;
use Monarc\Core\Table\UserTable;
use Laminas\Mvc\Controller\AbstractRestfulController;

class ApiUserRecoveryCodesController extends AbstractRestfulController
{
    use ControllerRequestResponseHandlerTrait;

    private UserTable $userTable;

    private UserSuperClass $connectedUser;

    public function __construct(UserTable $userTable, ConnectedUserService $connectedUserService)
    {
        $this->userTable = $userTable;
        $this->connectedUser = $connectedUserService->getConnectedUser();
    }

    /**
     * Generates and returns 5 new recovery codes (20 chars for each code).
     */
    public function create($data)
    {
        if (!$this->connectedUser->isTwoFactorAuthEnabled()) {
            throw new Exception('Two factor authentication is not enabled', 412);
        }

        $recoveryCodes = [];
        for ($i = 1; $i <= 5; $i++) {
            $recoveryCodes[] = bin2hex(openssl_random_pseudo_bytes(10));
        }

        $this->connectedUser->createRecoveryCodes($recoveryCodes);

        $this->userTable->save($this->connectedUser);

        return $this->getSuccessfulJsonResponse([
            'recoveryCodes' => $recoveryCodes,
        ]);
    }
}
