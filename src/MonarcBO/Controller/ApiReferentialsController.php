<?php
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2018 SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractController;
use Zend\View\Model\JsonModel;

/**
 * Api Referentials Controller
 *
 * Class ApiReferentialsController
 * @package MonarcBO\Controller
 */
class ApiReferentialsController extends AbstractController
{
    protected $name = 'referentials';
    protected $dependencies = ['measures'];

    /**
     * @inheritdoc
     */
    public function getList()
    {
        file_put_contents('php://stderr', print_r('ApiReferentialsController::getList', TRUE).PHP_EOL);
        $page = $this->params()->fromQuery('page');
        $limit = $this->params()->fromQuery('limit');
        $order = $this->params()->fromQuery('order');
        $filter = $this->params()->fromQuery('filter');

        $service = $this->getService();

        $entities = $service->getList($page, $limit, $order, $filter);
        if (count($this->dependencies)) {
            foreach ($entities as $key => $entity) {
                $this->formatDependencies($entities[$key], $this->dependencies);
            }
        }

        return new JsonModel(array(
            'count' => $service->getFilteredCount($filter),
            $this->name => $entities
        ));
    }

    public function get($id)
    {
        $entity = $this->getService()->getEntity(['uniqid' => $id]); //set the id in a table because it's a uniqid

        if (count($this->dependencies)) {
            $this->formatDependencies($entity, $this->dependencies);
        }

        return new JsonModel($entity);
    }

    public function update($id, $data)
    {
        $this->getService()->update(['uniqid' => $id], $data);

        return new JsonModel(['status' => 'ok']);
    }
}
