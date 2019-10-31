<?php
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2019  SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Monarc\Core\Controller\AbstractController;
use Monarc\Core\Service\AssetService;
use Zend\View\Model\JsonModel;

/**
 * TODO: extend AbstractRestfulController and remove AbstractController.
 *
 * Class ApiAssetsController
 * @package Monarc\BackOffice\Controller
 */
class ApiAssetsController extends AbstractController
{
    protected $name = 'assets';

    public function __construct(AssetService $assetService)
    {
        parent::__construct($assetService);
    }

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
            'count' => $service->getFilteredCount($filter, $filterAnd),
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
