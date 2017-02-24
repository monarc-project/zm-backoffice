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
 * Api Vulnerabilities Controller
 *
 * Class ApiAssetsController
 * @package MonarcBO\Controller
 */
class ApiVulnerabilitiesController extends AbstractController
{
    protected $name = 'vulnerabilities';
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

        $vulnerabilities = $service->getList($page, $limit, $order, $filter, $filterAnd);
        foreach($vulnerabilities as $key => $vulnerability){
            $vulnerability['models']->initialize();
            $models = $vulnerability['models']->getSnapshot();
            $vulnerabilities[$key]['models'] = array();
            foreach($models as $model){
                $vulnerabilities[$key]['models'][] = $model->getJsonArray();
            }
        }

        return new JsonModel(array(
            'count' => $service->getFilteredCount($filter, $filterAnd),
            $this->name => $vulnerabilities
        ));
    }

    /**
     * @inheritdoc
     */
    public function get($id)
    {
        $vulnerability = $this->getService()->getEntity($id);
        $vulnerability['models']->initialize();
        $models = $vulnerability['models']->getSnapshot();
        $vulnerability['models'] = array();
        foreach($models as $model){
            $vulnerability['models'][] = $model->getJsonArray();
        }

        return new JsonModel($vulnerability);
    }


}

