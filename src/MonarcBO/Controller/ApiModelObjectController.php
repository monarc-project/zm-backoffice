<?php

namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractController;
use Zend\View\Model\JsonModel;

/**
 * Api Models Controller
 *
 * Class ApiModelsController
 * @package MonarcBO\Controller
 */
class ApiModelObjectController extends AbstractController
{
	/**
     * Get list
     *
     * @return JsonModel
     */
    public function getList()
    {
    	$idModel = intval($this->params()->fromRoute('idm'));
    	$page = $this->params()->fromQuery('page');
        $limit = $this->params()->fromQuery('limit');
        $order = $this->params()->fromQuery('order');
        $filter = $this->params()->fromQuery('filter');

        $service = $this->getService();

        $entities = $service->getList($page, $limit, $order, $filter,array('model'=>$idModel));

        if (count($this->dependencies)) {
            foreach ($entities as $key => $entity) {
                $this->formatDependencies($entities[$key], $this->dependencies);
            }
        }

        return new JsonModel(array(
            'count' => $service->getFilteredCount($page, $limit, $order, $filter,array('model'=>$idModel)),
            $this->name => $entities
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
    	$entity = $this->getService()->getEntity($id);

        if (count($this->dependencies)) {
            $this->formatDependencies($entity, $this->dependencies);
        }

        if($entity->get('model') == $this->params()->fromRoute('idm')){
        	return new JsonModel($entity);
        }else{
        	return new JsonModel(array());
        }
    }

    /**
     * Create
     *
     * @param mixed $data
     * @return JsonModel
     */
    public function create($data)
    {
    	$data['model'] = $this->params()->fromRoute('idm');
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
        $data['model'] = $this->params()->fromRoute('idm');
        $this->getService()->update($id,$data);

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
        $this->getService()->delete($id,$this->params()->fromRoute('idm'));

        return new JsonModel(array('status' => 'ok'));
    }
}
