<?php
/**
 * @link      https://github.com/CASES-LU for the canonical source repository
 * @copyright Copyright (c) Cases is a registered trademark of SECURITYMADEIN.LU
 * @license   MyCases is licensed under the GNU Affero GPL v3 - See license.txt for more information
 */

namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractController;
use Zend\View\Model\JsonModel;

/**
 * Api Assets Controller
 *
 * Class ApiAssetsController
 * @package MonarcBO\Controller
 */
class ApiAssetsController extends AbstractController
{
    protected $name = 'assets';

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
        $type = $this->params()->fromQuery('type');
        if (!empty($type)) {
            $filterAnd['type'] = (int)$type;
        }


        $service = $this->getService();

        $assets = $service->getList($page, $limit, $order, $filter, $filterAnd);
        foreach($assets as $key => $asset){
            $asset['models']->initialize();
            $models = $asset['models']->getSnapshot();
            $assets[$key]['models'] = array();
            foreach($models as $model){
                $assets[$key]['models'][] = $model->getJsonArray();
            }
        }

        return new JsonModel(array(
            'count' => $service->getFilteredCount($page, $limit, $order, $filter, $filterAnd),
            $this->name => $assets
        ));
    }

    /**
     * @inheritdoc
     */
    public function get($id)
    {
        $asset = $this->getService()->getEntity($id);
        $asset['models']->initialize();
        $models = $asset['models']->getSnapshot();
        $asset['models'] = array();
        foreach($models as $model){
            $asset['models'][] = $model->getJsonArray();
        }

        return new JsonModel($asset);
    }
}

