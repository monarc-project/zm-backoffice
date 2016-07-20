<?php

namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractController;
use Zend\View\Model\JsonModel;

/**
 * Api Rolf Risks Controller
 *
 * Class ApiRolfRisksController
 * @package MonarcBO\Controller
 */
class ApiRolfRisksController extends AbstractController
{
    protected $name = 'risks';

    /**
     * Get list
     *
     * @return JsonModel
     */
    public function getList()
    {
        $page = $this->params()->fromQuery('page');
        $limit = $this->params()->fromQuery('limit');
        $order = $this->params()->fromQuery('order');
        $filter = $this->params()->fromQuery('filter');
        $category = $this->params()->fromQuery('category');
        $tag = $this->params()->fromQuery('tag');

        $service = $this->getService();

        $rolfRisks = $service->getListSpecific($page, $limit, $order, $filter, $category, $tag);
        foreach($rolfRisks as $key => $rolfRisk){

            $rolfRisk['categories']->initialize();
            $rolfCategories = $rolfRisk['categories']->getSnapshot();
            $rolfRisks[$key]['categories'] = array();
            foreach($rolfCategories as $rolfCategory){
                $rolfRisks[$key]['categories'][] = $rolfCategory->getJsonArray();
            }

            $rolfRisk['tags']->initialize();
            $rolfTags = $rolfRisk['tags']->getSnapshot();
            $rolfRisks[$key]['tags'] = array();
            foreach($rolfTags as $rolfTag){
                $rolfRisks[$key]['tags'][] = $rolfTag->getJsonArray();
            }
        }

        return new JsonModel(array(
            'count' => $service->getFilteredCount($page, $limit, $order, $filter),
            $this->name => $rolfRisks
        ));
    }

    /**
     * Get
     *
     * @param mixed $id
     * @return JsonModel
     */
    public function get($id)
    {
        $rolfRisk = $this->getService()->getEntity($id);

        $rolfRisk['categories']->initialize();
        $rolfCategories = $rolfRisk['categories']->getSnapshot();
        $rolfRisk['categories'] = array();
        foreach($rolfCategories as $rolfCategory){
            $rolfRisk['categories'][] = $rolfCategory->getJsonArray();
        }

        $rolfRisk['tags']->initialize();
        $rolfTags = $rolfRisk['tags']->getSnapshot();
        $rolfRisk['tags'] = array();
        foreach($rolfTags as $rolfTag){
            $rolfRisk['tags'][] = $rolfTag->getJsonArray();
        }

        return new JsonModel($rolfRisk);
    }
}

