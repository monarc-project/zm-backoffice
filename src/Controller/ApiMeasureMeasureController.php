<?php
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2019  SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Monarc\Core\Controller\AbstractController;
use Monarc\Core\Service\MeasureMeasureService;
use Zend\View\Model\JsonModel;

/**
 * TODO: extend AbstractRestfulController and remove AbstractController.
 *
 * Class ApiMeasureMeasureController
 * @package Monarc\BackOffice\Controller
 */
class ApiMeasureMeasureController extends AbstractController
{
    protected $name = 'MeasureMeasure';
    protected $dependencies = ['father','child'];

    public function __construct(MeasureMeasureService $measureMeasureService)
    {
        parent::__construct($measureMeasureService);
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
        $fatherId = $this->params()->fromQuery('fatherId');
        $childId = $this->params()->fromQuery('childId');
        $filterAnd = [];

        if ($fatherId) {
          $filterAnd['father'] = $fatherId;
        }
        if ($childId) {
          $filterAnd['child'] = $childId;
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

    public function deleteList($data)
    {
        if($data==null) //we delete one measuremeasure
        {
          $fatherId = $this->params()->fromQuery('father');
          $childId = $this->params()->fromQuery('child');
          $this->getService()->delete(['father'=>$fatherId, 'child'=>$childId]);
          return new JsonModel(['status' => 'ok']);
        }else
          return parent::deleteList($data);
    }
}
