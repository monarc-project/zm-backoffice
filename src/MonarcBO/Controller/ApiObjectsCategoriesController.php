<?php

namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractController;
use Zend\View\Model\JsonModel;

/**
 * Api Objects Categories Controller
 *
 * Class ApiObjectsCategoriesController
 * @package MonarcBO\Controller
 */
class ApiObjectsCategoriesController extends AbstractController
{
    protected $dependencies = ['parent', 'root'];

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

        $objectCategories = $this->getService()->getList($page, $limit, $order, $filter);

        $recursiveArray = $this->recursiveArray($objectCategories, null, 0);

        return new JsonModel(array(
            'count' => $this->getService()->getFilteredCount($page, $limit, $order, $filter),
            'categories' => $recursiveArray
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
        $objectCategory = $this->getService()->getEntity($id);

        $this->formatDependencies($objectCategory, $this->dependencies);

        return new JsonModel($objectCategory);
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

    /**
     * Recursive array
     *
     * @param $array
     * @param $parent
     * @param $level
     * @return array
     */
    public function recursiveArray($array, $parent, $level)
    {
        $fields = ['id', 'label1', 'label2', 'label3', 'label4', 'position'];
        $recursiveArray = [];
        foreach ($array AS $node) {

            $parentId = null;
            if (! is_null($node['parent'])) {
                $parentId = $node['parent']->id;
            }

            $nodeArray = [];

            if ($parent == $parentId) {
                foreach($fields as $field) {
                    $nodeArray[$field] = $node[$field];
                }
                $nodeArray['child'] = $this->recursiveArray($array, $node['id'], ($level + 1));

            }
            if (!empty($nodeArray)) {
                $recursiveArray[] = $nodeArray;
            }
        }

        return $recursiveArray;
    }
}

