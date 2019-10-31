<?php
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2019  SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Exception;
use Monarc\Core\Service\PasswordService;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

/**
 * Api User Password Controller
 *
 * Class ApiUserPasswordController
 * @package Monarc\BackOffice\Controller
 */
class ApiUserPasswordController extends AbstractRestfulController
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
    public function update($id, $data)
    {
        if ($data['new'] !== $data['confirm']) {
            throw new Exception('Passwords must be the same', 422);
        }

        $this->passwordService->changePassword($id, $data['old'], $data['new']);

        return new JsonModel(array('status' => 'ok'));
    }
}
