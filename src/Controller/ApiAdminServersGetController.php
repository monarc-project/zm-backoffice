<?php
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2019  SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Monarc\BackOffice\Service\ServerService;
use Monarc\BackOffice\Controller\ApiAdminServersController;
use Zend\View\Model\JsonModel;

/**
 * Api Admin Servers Get Controller
 *
 * Class ApiAdminServersGetController
 * @package Monarc\BackOffice\Controller
 */
class ApiAdminServersGetController extends ApiAdminServersController
{
    /**
     * @inheritdoc
     */
    public function create($data)
    {
        $this->methodNotAllowed();
    }
    /**
     * @inheritdoc
     */
    public function delete($id){
        $this->methodNotAllowed();
    }
    /**
     * @inheritdoc
     */
    public function deleteList($data){
        $this->methodNotAllowed();
    }
    /**
     * @inheritdoc
     */
    public function update($id, $data){
        $this->methodNotAllowed();
    }
    /**
     * @inheritdoc
     */
    public function patch($id, $data){
        $this->methodNotAllowed();
    }
}

