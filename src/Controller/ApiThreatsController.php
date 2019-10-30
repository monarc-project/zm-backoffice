<?php
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2019  SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Monarc\Core\Controller\AbstractController;
use Zend\View\Model\JsonModel;

/**
 * Api Threats Controller
 *
 * Class ApiThreatsController
 * @package Monarc\BackOffice\Controller
 */
class ApiThreatsController extends AbstractController
{
    protected $dependencies = ['theme'];
    protected $name = 'threats';

    /**
     * @inheritdoc
     */
    public function getList()
    {
        $page = $this->params()->fromQuery('page');
        $limit = $this->params()->fromQuery('limit');
        $order = $this->params()->fromQuery('order');
        $filter = $this->params()->fromQuery('filter');
        $status = $this->params()->fromQuery('status');
        if (is_null($status)) {
            $status = 1;
        }
        $filterAnd = ($status == "all") ? null : ['status' => (int) $status] ;

        $service = $this->getService();

        $threats = $service->getList($page, $limit, $order, $filter, $filterAnd);
        foreach($threats as $key => $threat){
            $threat['models']->initialize();
            $models = $threat['models']->getSnapshot();
            $threats[$key]['models'] = array();
            foreach($models as $model){
                $threats[$key]['models'][] = $model->getJsonArray();
            }

            $this->formatDependencies($threats[$key], $this->dependencies);
        }

        return new JsonModel(array(
            'count' => $service->getFilteredCount($filter, $filterAnd),
            $this->name => $threats
        ));
    }

    /**
     * @inheritdoc
     */
    public function get($id)
    {
        $threat = $this->getService()->getEntity($id);

        $threat['models']->initialize();
        $models = $threat['models']->getSnapshot();
        $threat['models'] = array();
        foreach($models as $model){
            $threat['models'][] = $model->getJsonArray();
        }

        $this->formatDependencies($threat, $this->dependencies);

        return new JsonModel($threat);
    }
}

