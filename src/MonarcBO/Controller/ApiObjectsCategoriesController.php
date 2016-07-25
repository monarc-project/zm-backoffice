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
    protected $name = 'categories';

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
        $parentId = (int) $this->params()->fromQuery('parentId');
        $lock = $this->params()->fromQuery('lock') == "false" ? false : true;

        $objectCategories = $this->getService()->getListSpecific($page, $limit, $order, $filter, $parentId);
        $fields = ['id', 'label1', 'label2', 'label3', 'label4', 'position'];;

        if ($parentId > 0 && $lock) {
            $recursiveArray = $this->getCleanFields($objectCategories, ['id', 'label1', 'label2', 'label3', 'label4', 'position']);
        } else {
            $recursiveArray = $this->recursiveArray($objectCategories, null, 0, $fields);
        }

        return new JsonModel(array(
            'count' => $this->getService()->getFilteredCount($page, $limit, $order, $filter),
            $this->name => $recursiveArray
        ));
    }

    public function getCleanFields($items, $fields) {
        $output = [];
        foreach ($items as $item) {
            $item_output = [];

            foreach ($item as $key => $value) {
                if (in_array($key, $fields)) {
                    $item_output[$key] = $value;
                }
            }

            $output[] = $item_output;
        }
        return $output;
    }
}

