<?php
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2019  SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Monarc\Core\Controller\AbstractController;
use Monarc\Core\Controller\Handler\ControllerRequestResponseHandlerTrait;
use Monarc\Core\Service\GuideItemService;

/**
 * TODO: extend AbstractRestfulController and remove AbstractController.
 */
class ApiGuidesItemsController extends AbstractController
{
    use ControllerRequestResponseHandlerTrait;

    protected $dependencies = ['guide'];
    protected $name = 'items';

    public function __construct(GuideItemService $guideItemService)
    {
        parent::__construct($guideItemService);
    }

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

        return $this->getPreparedJsonResponse([
            'count' => $service->getFilteredCount($filter),
            $this->name => $entities
        ]);
    }
}
