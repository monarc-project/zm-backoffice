<?php
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2019  SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractController;
use Zend\View\Model\JsonModel;

/**
 * Api Measures Controller
 *
 * Class ApiMeasuresController
 * @package MonarcBO\Controller
 */
class ApiMeasuresController extends AbstractController
{
    protected $name = 'measures';
    protected $dependencies = ['category', 'referential', 'measuresLinked'];

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
        $referential = $this->params()->fromQuery('referential');
        $category = $this->params()->fromQuery('category');
        $filterAnd = [];
        if (is_null($status)) {
            $status = 1;
        }
        $filterAnd = ($status == "all") ? null : ['status' => (int) $status] ;
        if ($referential) {
          $filterAnd['referential'] = (array)$referential;
        }
        if ($category) {
          $filterAnd['category'] = (int)$category;
        }

        $service = $this->getService();

        $entities = $service->getList($page, $limit, $order, $filter, $filterAnd);
        if (count($this->dependencies)) {
            foreach ($entities as $key => $entity) {
                $this->formatDependencies($entities[$key], $this->dependencies);
            }
        }

        return new JsonModel(array(
           'count' => $service->getFilteredCount($filter, $filterAnd),
            $this->name => $entities
        ));
    }

    public function update($id, $data)
    {
      $data ['referential'] = $data['referential']['uniqid']; //all the objects is send but we just need the uniqid
      return parent::update($id,$data);
    }

    public function deleteList($data)
    {
      $new_data = [];
      foreach ($data as $uniqid) {
        $new_data[] = ['uniqid' => $uniqid];
      }
      return parent::deleteList($new_data);
    }
}
