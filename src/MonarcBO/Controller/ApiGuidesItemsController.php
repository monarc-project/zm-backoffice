<?php
/**
 * @link      https://github.com/CASES-LU for the canonical source repository
 * @copyright Copyright (c) Cases is a registered trademark of SECURITYMADEIN.LU
 * @license   MyCases is licensed under the GNU Affero GPL v3 - See license.txt for more information
 */

namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractController;
use Zend\View\Model\JsonModel;

/**
 * Api Guides Items Controller
 *
 * Class ApiGuidesItemsController
 * @package MonarcBO\Controller
 */
class ApiGuidesItemsController extends AbstractController
{
    protected $dependencies = ['guide'];
    protected $name = 'items';

    /**
     * @inheritdoc
     */
    public function getList()
    {
        $page = $this->params()->fromQuery('page');
        $limit = $this->params()->fromQuery('limit');
        $order = $this->params()->fromQuery('order');
        $filter = $this->params()->fromQuery('filter');
        $guide = $this->params()->fromQuery('guide');
        if (!is_null($guide)) {
            $filterAnd = ['guide' => (int) $guide];
        } else {
            $filterAnd = [];
        }

        $service = $this->getService();

        $entities = $service->getList($page, $limit, $order, $filter, $filterAnd);
        if (count($this->dependencies)) {
            foreach ($entities as $key => $entity) {
                $this->formatDependencies($entities[$key], $this->dependencies);
            }
        }

        return new JsonModel(array(
            'count' => $service->getFilteredCount($filter),
            $this->name => $entities
        ));
    }

}

