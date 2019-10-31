<?php
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2019  SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Monarc\Core\Controller\AbstractController;
use Monarc\Core\Service\VulnerabilityService;
use Zend\View\Model\JsonModel;

/**
 * Api Vulnerabilities Controller
 *
 * Class ApiAssetsController
 * @package Monarc\BackOffice\Controller
 */
class ApiVulnerabilitiesController extends AbstractController
{
    protected $name = 'vulnerabilities';

    public function __construct(VulnerabilityService $vulnerabilityService)
    {
        parent::__construct($vulnerabilityService);
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
        $status = $this->params()->fromQuery('status');
        if ($status === null) {
            $status = 1;
        }
        $filterAnd = $status === 'all' ? null : ['status' => (int)$status];

        $service = $this->getService();

        $vulnerabilities = $service->getList($page, $limit, $order, $filter, $filterAnd);
        foreach ($vulnerabilities as $key => $vulnerability) {
            $vulnerability['models']->initialize();
            $models = $vulnerability['models']->getSnapshot();
            $vulnerabilities[$key]['models'] = array();
            foreach ($models as $model) {
                $vulnerabilities[$key]['models'][] = $model->getJsonArray();
            }
        }

        return new JsonModel(array(
            'count' => $service->getFilteredCount($filter, $filterAnd),
            $this->name => $vulnerabilities
        ));
    }

    /**
     * @inheritdoc
     */
    public function get($id)
    {
        $vulnerability = $this->getService()->getEntity($id);
        $vulnerability['models']->initialize();
        $models = $vulnerability['models']->getSnapshot();
        $vulnerability['models'] = array();
        foreach ($models as $model) {
            $vulnerability['models'][] = $model->getJsonArray();
        }

        return new JsonModel($vulnerability);
    }
}
