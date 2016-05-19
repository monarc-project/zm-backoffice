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

        $service = $this->getService();

        $rolfRisks = $service->getList($page, $limit, $order, $filter);
        foreach($rolfRisks as $key => $rolfRisk){

            $rolfRisk['rolfCategories']->initialize();
            $rolfCategories = $rolfRisk['rolfCategories']->getSnapshot();
            $rolfRisks[$key]['rolfCategories'] = array();
            foreach($rolfCategories as $rolfCategory){
                $rolfRisks[$key]['rolfCategories'][] = $rolfCategory->getJsonArray();
            }

            $rolfRisk['rolfTags']->initialize();
            $rolfTags = $rolfRisk['rolfTags']->getSnapshot();
            $rolfRisks[$key]['rolfTags'] = array();
            foreach($rolfTags as $rolfTag){
                $rolfRisks[$key]['rolfTags'][] = $rolfTag->getJsonArray();
            }
        }

        return new JsonModel(array(
            'count' => $service->getFilteredCount($page, $limit, $order, $filter),
            'risks' => $rolfRisks
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

        $rolfRisk['rolfCategories']->initialize();
        $rolfCategories = $rolfRisk['rolfCategories']->getSnapshot();
        $rolfRisk['rolfCategories'] = array();
        foreach($rolfCategories as $rolfCategory){
            $rolfRisk['rolfCategories'][] = $rolfCategory->getJsonArray();
        }

        $rolfRisk['rolfTags']->initialize();
        $rolfTags = $rolfRisk['rolfTags']->getSnapshot();
        $rolfRisk['rolfTags'] = array();
        foreach($rolfTags as $rolfTag){
            $rolfRisk['rolfTags'][] = $rolfTag->getJsonArray();
        }

        return new JsonModel($rolfRisk);
    }

    /**
     * Create
     *
     * @param mixed $data
     * @return JsonModel
     */
    public function create($data)
    {
        $id = $this->getService()->create($data);

        return new JsonModel(
            array(
                'status' => 'ok',
                'id' => $id,
            )
        );
    }

    /**
     * Update
     *
     * @param mixed $id
     * @param mixed $data
     * @return JsonModel
     */
    public function update($id, $data)
    {
        $this->getService()->update($id, $data);

        return new JsonModel(array('status' => 'ok'));
    }

    /**
     * Delete
     *
     * @param mixed $id
     * @return JsonModel
     */
    public function delete($id)
    {
        $service = $this->getService();
        $service->delete($id);

        return new JsonModel(array('status' => 'ok'));
    }
}

