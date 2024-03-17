<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2023 Luxembourg House of Cybersecurity LHC.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Monarc\Core\Controller\Handler\ControllerRequestResponseHandlerTrait;
use Monarc\Core\Entity\UserSuperClass;
use RobThree\Auth\TwoFactorAuth;
use RobThree\Auth\Providers\Qr\EndroidQrCodeProvider;
use Monarc\Core\Service\ConnectedUserService;
use Monarc\Core\Service\ConfigService;
use Monarc\Core\Table\UserTable;
use Laminas\Mvc\Controller\AbstractRestfulController;

class ApiUserTwoFAController extends AbstractRestfulController
{
    use ControllerRequestResponseHandlerTrait;

    private UserSuperClass $connectedUser;

    private ConfigService $configService;

    private UserTable $userTable;

    private TwoFactorAuth $twoFactorAuth;

    public function __construct(
        ConfigService $configService,
        ConnectedUserService $connectedUserService,
        UserTable $userTable
    ) {
        $this->configService = $configService;
        $this->connectedUser = $connectedUserService->getConnectedUser();
        $this->userTable = $userTable;
        $this->twoFactorAuth = new TwoFactorAuth('MONARC', 6, 30, 'sha1', new EndroidQrCodeProvider());
    }

    /**
     * Generates a new secret key for the connected user.
     * Returns the secret key and the corresponding QRCOde.
     */
    public function get($id)
    {
        /* Create a new secret and generate a QRCode. */
        $label = 'MONARC BO';
        if ($this->configService->getInstanceName()) {
            $label .= ' ('. $this->configService->getInstanceName() .')';
        }
        $secret = $this->twoFactorAuth->createSecret();
        $qrcode = $this->twoFactorAuth->getQRCodeImageAsDataUri($label, $secret);

        return $this->getPreparedJsonResponse([
            'id' => $this->connectedUser->getId(),
            'secret' => $secret,
            'qrcode' => $qrcode,
        ]);
    }

    /**
     * Confirms the newly generated key with a token given by the user.
     *
     * @param string[] $data
     */
    public function create($data)
    {
        $isValid = $this->twoFactorAuth->verifyCode($data['secretKey'], $data['verificationCode']);

        if ($isValid) {
            $this->connectedUser->setSecretKey($data['secretKey']);
            $this->connectedUser->setTwoFactorAuthEnabled(true);
            $this->userTable->save($this->connectedUser);
        }

        return $this->getPreparedJsonResponse(['status' => $isValid]);
    }

    /**
     * Disables the 2FA for the connected user and deletes the secret key.
     */
    public function delete($id)
    {
        $this->connectedUser->setTwoFactorAuthEnabled(false);
        $this->connectedUser->setSecretKey('');
        $this->connectedUser->setRecoveryCodes(null);
        $this->userTable->save($this->connectedUser);

        $this->getResponse()->setStatusCode(204);

        return $this->getPreparedJsonResponse();
    }
}
