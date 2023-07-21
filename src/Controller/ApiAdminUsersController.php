<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2023 Luxembourg House of Cybersecurity LHC.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Monarc\Core\Controller\Handler\ControllerRequestResponseHandlerTrait;
use Monarc\Core\InputFormatter\User\GetUsersInputFormatter;
use Monarc\Core\Service\UserService;
use Laminas\Mvc\Controller\AbstractRestfulController;
use Monarc\Core\Validator\InputValidator\User\PostUserDataInputValidator;

class ApiAdminUsersController extends AbstractRestfulController
{
    use ControllerRequestResponseHandlerTrait;

    private GetUsersInputFormatter $getUsersInputFormatter;

    private PostUserDataInputValidator $postUserDataInputValidator;

    private UserService $userService;

    public function __construct(
        GetUsersInputFormatter $getUsersInputFormatter,
        PostUserDataInputValidator $postUserDataInputValidator,
        UserService $userService
    ) {
        $this->getUsersInputFormatter = $getUsersInputFormatter;
        $this->postUserDataInputValidator = $postUserDataInputValidator;
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

    /**
     * @param array $data
     */
    public function create($data)
    {
        $this->validatePostParams($this->postUserDataInputValidator, $data);

        $this->userService->create($data);

        return $this->getSuccessfulJsonResponse();
    }

    /**
     * @param array $data
     */
    public function update($id, $data)
    {
        $this->validatePostParams($this->postUserDataInputValidator, $data);

        $this->userService->update((int)$id, $data);

        return $this->getSuccessfulJsonResponse();
    }

    public function patch($id, $data)
    {
        $this->userService->patch((int)$id, $data);

        return $this->getSuccessfulJsonResponse();
    }

    public function delete($id)
    {
        $this->userService->delete((int)$id);

        return $this->getSuccessfulJsonResponse();
    }
}
