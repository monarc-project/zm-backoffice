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

class ApiUserPasswordController extends AbstractRestfulController
{
    use ControllerRequestResponseHandlerTrait;

    private PasswordService $passwordService;

    public function __construct(PasswordService $passwordService)
    {
        $this->passwordService = $passwordService;
    }

    /**
     * @param string[] $data
     */
    public function update($id, $data)
    {
        if ($data['new'] !== $data['confirm']) {
            throw new Exception('Passwords must be the same', 422);
        }

        $this->passwordService->changePassword((int)$id, $data['old'], $data['new']);

        return $this->getSuccessfulJsonResponse();
    }
}
