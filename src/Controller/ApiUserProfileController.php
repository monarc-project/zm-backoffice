<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2023  SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Monarc\Core\Controller\Handler\ControllerRequestResponseHandlerTrait;
use Monarc\Core\Model\Entity\UserSuperClass;
use Monarc\Core\Service\ConnectedUserService;
use Monarc\Core\Service\UserProfileService;
use Laminas\Mvc\Controller\AbstractRestfulController;
use Monarc\Core\Validator\InputValidator\Profile\PatchProfileDataInputValidator;

class ApiUserProfileController extends AbstractRestfulController
{
    use ControllerRequestResponseHandlerTrait;

    private UserProfileService $userProfileService;

    private UserSuperClass $connectedUser;
    private PatchProfileDataInputValidator $patchProfileDataInputValidator;

    public function __construct(
        PatchProfileDataInputValidator $patchProfileDataInputValidator,
        UserProfileService $userProfileService,
        ConnectedUserService $connectedUserService
    ) {
        $this->patchProfileDataInputValidator = $patchProfileDataInputValidator;
        $this->userProfileService = $userProfileService;
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
        $this->validatePostParams($this->patchProfileDataInputValidator, $data);

        $this->userProfileService->updateMyData($data);

        return $this->getSuccessfulJsonResponse();
    }

    public function replaceList($data)
    {
        $this->userProfileService->updateMyData($data);

        return $this->getSuccessfulJsonResponse();
    }

    public function deleteList($id)
    {
        $this->userProfileService->deleteMe();

        $this->getResponse()->setStatusCode(204);

        return $this->getPreparedJsonResponse();
    }
}
