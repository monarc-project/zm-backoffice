<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2022  SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Laminas\Mvc\Controller\AbstractRestfulController;
use Monarc\BackOffice\Service\ServerService;
use Monarc\BackOffice\Validator\InputValidator\Server\PostServerDataInputValidator;
use Monarc\Core\Controller\Handler\ControllerRequestResponseHandlerTrait;
use Monarc\Core\InputFormatter\Server\GetServersInputFormatter;
use Monarc\Core\Model\Entity\User;
use Monarc\Core\Model\Entity\UserRole;
use Monarc\Core\Service\ConnectedUserService;

class ApiAdminServersController extends AbstractRestfulController
{
    use ControllerRequestResponseHandlerTrait;

    private ServerService $serverService;

    private GetServersInputFormatter $getServersInputFormatter;

    private PostServerDataInputValidator $postServerDataInputValidator;

    private User $connectedUser;

    public function __construct(
        ServerService $serverService,
        GetServersInputFormatter $getServersInputFormatter,
        PostServerDataInputValidator $postServerDataInputValidator,
        ConnectedUserService $connectedUserService
    ) {
        $this->serverService = $serverService;
        $this->getServersInputFormatter = $getServersInputFormatter;
        $this->postServerDataInputValidator = $postServerDataInputValidator;
        $this->connectedUser = $connectedUserService->getConnectedUser();
    }

    public function getList()
    {
        $formattedInputParams = $this->getFormattedInputParams($this->getServersInputFormatter);

        return $this->getPreparedJsonResponse([
            'count' => $this->serverService->getFilteredCount($formattedInputParams),
            'servers' => $this->serverService->getList($formattedInputParams),
        ]);
    }

    public function get($id)
    {
        return $this->getPreparedJsonResponse($this->serverService->getServerData((int)$id));
    }

    public function create($data)
    {
        /* Only "sysadmin" can access the endpoint. */
        if (!$this->connectedUser->hasRole(UserRole::SYS_ADMIN)) {
            return $this->getResponse()->setStatusCode(403);
        }

        $this->validatePostParams($this->postServerDataInputValidator, $data);

        $server = $this->serverService->create($this->postServerDataInputValidator->getValidData());

        return $this->getSuccessfulJsonResponse(['id' => $server->getId()]);
    }

    public function update($id, $data)
    {
        /* Only "sysadmin" can access the endpoint. */
        if (!$this->connectedUser->hasRole(UserRole::SYS_ADMIN)) {
            return $this->getResponse()->setStatusCode(403);
        }

        $this->validatePostParams($this->postServerDataInputValidator, $data);

        $this->serverService->update((int)$id, $this->postServerDataInputValidator->getValidData());

        return $this->getSuccessfulJsonResponse();
    }

    public function delete($id)
    {
        /* Only "sysadmin" can access the endpoint. */
        if (!$this->connectedUser->hasRole(UserRole::SYS_ADMIN)) {
            return $this->getResponse()->setStatusCode(403);
        }

        $this->serverService->delete((int)$id);

        return $this->getSuccessfulJsonResponse();
    }
}
