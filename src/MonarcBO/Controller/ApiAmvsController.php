<?php

namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractController;
use Zend\View\Model\JsonModel;

/**
 * Api Amvs Controller
 *
 * Class ApiAmvsController
 * @package MonarcBO\Controller
 */
class ApiAmvsController extends AbstractController
{
    protected $dependencies = ['asset', 'threat', 'vulnerability', 'measure1', 'measure2', 'measure3'];
    protected $name = "amvs";

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
        $status = $this->params()->fromQuery('status');
        $asset = $this->params()->fromQuery('asset');
        if (is_null($status)) {
            $status = 1;
        }
        $filterAnd = [];

        if ($status != 'all') {
            $filterAnd['status'] = (int) $status;
        }
        if ($asset > 0) {
            $filterAnd['asset'] = (int) $asset;
        }

        $service = $this->getService();

        $entities = $service->getList($page, $limit, $order, $filter, $filterAnd);
        if (count($this->dependencies)) {
            foreach ($entities as $key => $entity) {
                $this->formatDependencies($entities[$key], $this->dependencies);
            }
        }

        return new JsonModel(array(
            'count' => $service->getFilteredCount($page, $limit, $order, $filter, $filterAnd),
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

        // Find out the entity's implicitPosition and previous
        if ($entity['position'] == 1) {
            $entity['implicitPosition'] = 1;
        } else {
            // We're not at the beginning, get all AMV links of the same asset, and figure out position and previous
            $amvsAsset = $this->getService()->getList(1, 0, 'position', null, ['asset' => $entity['asset']['id']]);

            $i = 0;
            foreach ($amvsAsset as $amv) {
                if ($amv['id'] == $entity['id']) {
                    if ($i == count($amvsAsset) - 1) {
                        $entity['implicitPosition'] = 2;
                    } else {
                        $entity['implicitPosition'] = 3;
                        $entity['previous'] = $amvsAsset[$i - 1];
                        $this->formatDependencies($entity['previous'], $this->dependencies);
                    }

                    break;
                }

                ++$i;
            }
        }

        return new JsonModel($entity);
    }
}

