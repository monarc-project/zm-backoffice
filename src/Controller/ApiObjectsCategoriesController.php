<?php
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2019  SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Monarc\Core\Controller\AbstractController;
use Monarc\Core\Service\ObjectCategoryService;
use Laminas\View\Model\JsonModel;

/**
 * TODO: extend AbstractRestfulController and remove AbstractController.
 *
 * Class ApiObjectsCategoriesController
 * @package Monarc\BackOffice\Controller
 */
class ApiObjectsCategoriesController extends AbstractController
{
    protected $dependencies = ['parent', 'root'];
    protected $name = 'categories';

    public function __construct(ObjectCategoryService $objectCategoryService)
    {
        parent::__construct($objectCategoryService);
    }

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

        $modelId = $this->params()->fromQuery('model');

        $objectCategories = $service->getListSpecific($page, $limit, $order, $filter, $filterAnd, $modelId);

        $fields = ['id', 'label1', 'label2', 'label3', 'label4', 'position', 'objects'];

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

