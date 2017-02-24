<?php
/**
 * @link      https://github.com/CASES-LU for the canonical source repository
 * @copyright Copyright (c) Cases is a registered trademark of SECURITYMADEIN.LU
 * @license   MyCases is licensed under the GNU Affero GPL v3 - See license.txt for more information
 */

namespace MonarcBO\Controller;

use MonarcBO\Service\ServerService;
use MonarcBO\Controller\ApiAdminServersController;
use Zend\View\Model\JsonModel;

/**
 * Api Admin Servers Get Controller
 *
 * Class ApiAdminServersGetController
 * @package MonarcBO\Controller
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

