<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2024 Luxembourg House of Cybersecurity LHC.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Laminas\Mvc\Controller\AbstractRestfulController;
use Monarc\Core\Controller\Handler\ControllerRequestResponseHandlerTrait;
use Monarc\Core\Entity\UserSuperClass;
use Monarc\Core\Service\ConnectedUserService;
use Monarc\Core\Service\UserProfileService;
use Monarc\Core\Validator\InputValidator\Profile\PatchProfileDataInputValidator;

class ApiUserProfileController extends AbstractRestfulController
{
    use ControllerRequestResponseHandlerTrait;

    private UserSuperClass $connectedUser;

    public function __construct(
        private PatchProfileDataInputValidator $patchProfileDataInputValidator,
        private UserProfileService $userProfileService,
        ConnectedUserService $connectedUserService
    ) {
        $this->connectedUser = $connectedUserService->getConnectedUser();
    }

    public function getList()
    {
        return $this->getPreparedJsonResponse([
            'id' => $this->connectedUser->getId(),
            'firstname' => $this->connectedUser->getFirstname(),
            'lastname' => $this->connectedUser->getLastname(),
            'email' => $this->connectedUser->getEmail(),
            'status' => $this->connectedUser->getStatus(),
            'role' => $this->connectedUser->getRolesArray(),
            'isTwoFactorAuthEnabled' => $this->connectedUser->isTwoFactorAuthEnabled(),
            'remainingRecoveryCodes' => \count($this->connectedUser->getRecoveryCodes()),
        ]);
    }

    public function patchList($data)
    {
        $this->validatePostParams(
            $this->patchProfileDataInputValidator->setExcludeFilter(['email' => $this->connectedUser->getEmail()]),
            $data
        );

        $this->userProfileService->updateMyData($data);

        return $this->getSuccessfulJsonResponse();
    }

    public function replaceList($data)
    {
        $this->userProfileService->updateMyData($data);

        return $this->getSuccessfulJsonResponse();
    }

    public function deleteList($data)
    {
        $this->userProfileService->deleteMe();

        $this->getResponse()->setStatusCode(204);

        return $this->getPreparedJsonResponse();
    }
}
