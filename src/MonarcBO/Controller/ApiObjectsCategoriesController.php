<?php
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) Cases is a registered trademark of SECURITYMADEIN.LU
 * @license   MyCases is licensed under the GNU Affero GPL v3 - See license.txt for more information
 */

namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractController;
use MonarcCore\Service\ObjectCategoryService;
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
     * @inheritdoc
     */
    public function getList()
    {

        $page = $this->params()->fromQuery('page');
        $limit = $this->params()->fromQuery('limit');
        $order = $this->params()->fromQuery('order');
        if(empty($order)){
            $order = "position";
        }
        $filter = $this->params()->fromQuery('filter');
        $lock = $this->params()->fromQuery('lock') == "false" ? false : true;

        /** @var ObjectCategoryService $service */
        $service = $this->getService();

        $filterAnd = [];
        $catid = (int)$this->params()->fromQuery('catid');
        $parentId = (int) $this->params()->fromQuery('parentId');
        if(!empty($catid)){
            $filterAnd['id'] = [
                'op' => 'NOT IN',
                'value' => [$catid],
            ];
            if ($parentId > 0) {
                $filterAnd['id']['value'][] = $parentId;
                $filterAnd['parent'] = $parentId;
            }else{
                $filterAnd['parent'] = null;
            }
        }elseif ($parentId > 0) {
            $filterAnd['parent'] = $parentId;
        }elseif(!$lock){
            $filterAnd['parent'] = null;
        }

        $objectCategories = $service->getListSpecific($page, $limit, $order, $filter, $filterAnd);

        $fields = ['id', 'label1', 'label2', 'label3', 'label4', 'position'];;

        if ($parentId > 0 && $lock) {
            $recursiveArray = $this->getCleanFields($objectCategories, $fields);
        } else {
            $recursiveArray = $this->recursiveArray($objectCategories, null, 0, $fields);
        }

        return new JsonModel(array(
            'count' => $this->getService()->getFilteredCount($filter,$filterAnd),
            $this->name => $recursiveArray
        ));
    }

    /**
     * @inheritdoc
     */
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

    /**
     * @inheritdoc
     */
    public function create($data)
    {
        $obj = $this->getService()->create($data);

        return new JsonModel(
            array(
                'status' => 'ok',
                'categ' => $obj,
            )
        );
    }
}

