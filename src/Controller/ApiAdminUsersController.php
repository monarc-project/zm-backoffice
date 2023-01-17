<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2022  SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Monarc\Core\Controller\Handler\ControllerRequestResponseHandlerTrait;
use Monarc\Core\InputFormatter\User\GetUsersInputFormatter;
use Monarc\Core\Service\UserService;
use Laminas\Mvc\Controller\AbstractRestfulController;

class ApiAdminUsersController extends AbstractRestfulController
{
    use ControllerRequestResponseHandlerTrait;

    private UserService $userService;

    private GetUsersInputFormatter $getUsersInputFormatter;

    public function __construct(
        GetUsersInputFormatter $getUsersInputFormatter,
        UserService $userService
    ) {
        $this->getUsersInputFormatter = $getUsersInputFormatter;
        $this->userService = $userService;
    }

    public function getList()
    {
        $formattedParams = $this->getFormattedInputParams($this->getUsersInputFormatter);

        return $this->getPreparedJsonResponse([
            'count' => $this->userService->getCount($formattedParams),
            'users' => $this->userService->getList($formattedParams),
        ]);
    }

    public function get($id)
    {
        return $this->getPreparedJsonResponse($this->userService->getData((int)$id));
    }

    /*
     * TODO: implement the validators for create and patch the BO side similar to FO CreateUserInputValidator
     */
    public function create($data)
    {
        $this->userService->create($data);

        return $this->getPreparedJsonResponse(['status' => 'ok']);
    }

    public function update($id, $data)
    {
        $this->userService->update((int)$id, $data);

        return $this->getPreparedJsonResponse(['status' => 'ok']);
    }

    public function patch($id, $data)
    {
        $this->userService->patch((int)$id, $data);

        return $this->getPreparedJsonResponse(['status' => 'ok']);
    }

    public function delete($id)
    {
        $this->userService->delete((int)$id);

        return $this->getPreparedJsonResponse(['status' => 'ok']);
    }
}
