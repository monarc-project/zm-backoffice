<?php
/**
 * @link      https://github.com/CASES-LU for the canonical source repository
 * @copyright Copyright (c) Cases is a registered trademark of SECURITYMADEIN.LU
 * @license   MyCases is licensed under the GNU Affero GPL v3 - See license.txt for more information
 */

namespace MonarcBO\Controller;

use MonarcBO\Service\ClientService;
use MonarcCore\Controller\AbstractController;
use Zend\View\Model\JsonModel;

/**
 * Api Clients Controller
 *
 * Class ApiAClientsController
 * @package MonarcBO\Controller
 */
class ApiClientsController extends AbstractController
{
    protected $name = 'clients';

    /**
     * @inheritdoc
     */
    public function create($data)
    {
        /** @var ClientService $service */
        $service = $this->getService();

        // Security: Don't allow changing role, password, status and history fields. To clean later.
        if (isset($data['id'])) unset($data['id']);
        if (isset($data['updatedAt'])) unset($data['updatedAt']);
        if (isset($data['updater'])) unset($data['updater']);
        if (isset($data['createdAt'])) unset($data['createdAt']);
        if (isset($data['creator'])) unset($data['creator']);

        $service->create($data);

        return new JsonModel(array('status' => 'ok'));
    }

    /**
     * @inheritdoc
     */
    public function update($id, $data)
    {
        /** @var ClientService $service */
        $service = $this->getService();

        // Security: Don't allow changing role, password, status and history fields. To clean later.
        if (isset($data['updatedAt'])) unset($data['updatedAt']);
        if (isset($data['updater'])) unset($data['updater']);
        if (isset($data['createdAt'])) unset($data['createdAt']);
        if (isset($data['creator'])) unset($data['creator']);

        $service->update($id, $data);

        return new JsonModel(array('status' => 'ok'));
    }
}

