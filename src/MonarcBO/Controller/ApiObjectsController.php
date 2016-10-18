<?php

namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractController;
use MonarcCore\Model\Entity\Object;
use MonarcCore\Service\ObjectService;
use Zend\View\Model\JsonModel;

/**
 * Api Objects Controller
 *
 * Class ApiObjectsController
 * @package MonarcBO\Controller
 */
class ApiObjectsController extends AbstractController
{
    protected $dependencies = ['category', 'asset', 'rolfTag'];
    protected $name = 'objects';

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
        $asset = (int) $this->params()->fromQuery('asset');
        $category = (int) $this->params()->fromQuery('category');
        $model = (int) $this->params()->fromQuery('model');
        $anr = (int) $this->params()->fromQuery('anr');
        $lock = $this->params()->fromQuery('lock');

        /** @var ObjectService $service */
        $service = $this->getService();
        $objects =  $service->getListSpecific($page, $limit, $order, $filter, $asset, $category, $model, $anr, $lock);

        if ($lock == 'true') {
            foreach($objects as $key => $object){
                $this->formatDependencies($objects[$key], $this->dependencies);
            }
        }

        return new JsonModel(array(
            'count' => $service->getFilteredCount($page, $limit, $order, $filter, $asset, $category, $model, $anr),
            $this->name => $objects
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
        /** @var ObjectService $service */
        $service = $this->getService();
        $object = $service->getCompleteEntity($id, Object::FRONT_OFFICE);

        if (count($this->dependencies)) {
            $this->formatDependencies($object, $this->dependencies);
        }

        $anrs = [];
        foreach($object['anrs'] as $key => $anr) {
            $anrs[] = $anr->getJsonArray();
        }
        $object['anrs'] = $anrs;

        return new JsonModel($object);
    }

}

