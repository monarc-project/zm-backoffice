<?php
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2018 SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractController;
use Zend\View\Model\JsonModel;

/**
 * Api Measure Measure Controller
 *
 * Class ApiMeasureMeasureController
 * @package MonarcBO\Controller
 */
class ApiMeasureMeasureController extends AbstractController
{
    protected $name = 'MeasureMeasure';
    protected $dependencies = ['father','child'];

    /**
     * @inheritdoc
     */
    public function getList()
    {
        $page = $this->params()->fromQuery('page');
        $limit = $this->params()->fromQuery('limit');
        $order = $this->params()->fromQuery('order');
        $filter = $this->params()->fromQuery('filter');
      //  $status = $this->params()->fromQuery('status');
        $fatherId = $this->params()->fromQuery('fatherId');
        $childId = $this->params()->fromQuery('childId');
        $filterAnd = [];
        // if (is_null($status)) {
        //     $status = 1;
        // }
        // $filterAnd = ($status == "all") ? null : ['status' => (int) $status] ;
        if ($fatherId) {
          $filterAnd['father'] = (int) $fatherId;
        }
        if ($childId) {
          $filterAnd['child'] = (int) $childId;
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
}