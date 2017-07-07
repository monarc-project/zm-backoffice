<?php
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) Cases is a registered trademark of SECURITYMADEIN.LU
 * @license   MyCases is licensed under the GNU Affero GPL v3 - See license.txt for more information
 */

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
     * @inheritdoc
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

        return new JsonModel(array(
            'count' => $service->getFilteredCount($filter, $asset, $category, $model, $anr),
            $this->name => $objects
        ));
    }

    /**
     * @inheritdoc
     */
    public function get($id)
    {
        /** @var ObjectService $service */
        $service = $this->getService();
        $mode = $this->params()->fromQuery('mode');
        $anr = (int) $this->params()->fromQuery('anr');
        $object = $service->getCompleteEntity($id, isset($mode) && $mode == Object::CONTEXT_ANR ? Object::CONTEXT_ANR : Object::CONTEXT_BDC, $anr);

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

