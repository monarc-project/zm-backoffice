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
 * Api Cities Controller
 *
 * Class ApiCitiesController
 * @package MonarcBO\Controller
 */
class ApiCitiesController extends AbstractController
{
    protected $name = 'cities';

    /**
     * @inheritdoc
     */
    public function getList()
    {
        $page = $this->params()->fromQuery('page');
        $limit = $this->params()->fromQuery('limit');
        $order = $this->params()->fromQuery('order');
        $filter = $this->params()->fromQuery('filter');
        $country_id = (int)$this->params()->fromQuery('country_id');

        if (is_null($limit)) {
            $limit = 20;
            $page = 1;
        }
        if (is_null($order)) {
            $order = 'label';
        }

        $service = $this->getService();

        if (is_null($country_id)) {
            $entities = $service->getList($page, $limit, $order, $filter);
            $count = $service->getFilteredCount($filter);
        }
        else {
            $entities = $service->getList($page, $limit, $order, $filter, array('country_id' => $country_id));
            $count = $service->getFilteredCount($filter, array('country_id' => $country_id));
        }

        if (count($this->dependencies)) {
            foreach ($entities as $key => $entity) {
                $this->formatDependencies($entities[$key], $this->dependencies);
            }
        }

        return new JsonModel(array(
            'count' => $count,
            $this->name => $entities
        ));
    }
}

