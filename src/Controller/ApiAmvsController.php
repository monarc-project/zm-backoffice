<?php
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2019  SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Monarc\Core\Controller\AbstractController;
use Monarc\Core\Model\Entity\Measure;
use Monarc\Core\Service\AmvService;
use Laminas\View\Model\JsonModel;

/**
 * TODO: extend AbstractRestfulController and remove AbstractController.
 *
 * Class ApiAmvsController
 * @package Monarc\BackOffice\Controller
 */
class ApiAmvsController extends AbstractController
{
    protected $dependencies = ['asset', 'threat', 'vulnerability', 'measures'];
    protected $name = "amvs";

    public function __construct(AmvService $amvService)
    {
        parent::__construct($amvService);
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
        $asset = $this->params()->fromQuery('asset');
        $amvid = $this->params()->fromQuery('amvid');
        if ($status === null) {
            $status = 1;
        }
        $filterAnd = [];

        if ($status !== 'all') {
            $filterAnd['status'] = (int) $status;
        }
        if ($asset !== null) {
            $filterAnd['a.uuid'] = $asset;
        }

        if (!empty($amvid)) {
            $filterAnd['uuid'] = [
                'op' => '!=',
                'value' => $amvid,
            ];
        }

        $service = $this->getService();

        $entities = $service->getList($page, $limit, $order, $filter, $filterAnd);
        if (count($this->dependencies)) {
            foreach ($entities as $key => $entity) {
                $this->formatDependencies($entities[$key], $this->dependencies, Measure::class, ['referential']);
            }
        }

        return new JsonModel(array(
            'count' => $service->getFilteredCount($filter, $filterAnd),
            $this->name => $entities
        ));
    }

    /**
     * @inheritdoc
     */
    public function get($id)
    {
        $entity = $this->getService()->getEntity($id);

        if (count($this->dependencies)) {
            $this->formatDependencies($entity, $this->dependencies, Measure::class, ['referential']);
        }

        // Find out the entity's implicitPosition and previous
        if ($entity['position'] == 1) {
            $entity['implicitPosition'] = 1;
        } else {
            // We're not at the beginning, get all AMV links of the same asset, and figure out position and previous
            $amvsAsset = $this->getService()->getList(1, 0, 'position', null, ['a.uuid' => $entity['asset']['uuid']->toString()]);
            $i = 0;
            foreach ($amvsAsset as $amv) {
                if ($amv['uuid'] == $entity['uuid']) {
                    if ($i == count($amvsAsset) - 1) {
                        $entity['implicitPosition'] = 2;
                    } else {
                        if ($i == 0) {
                            $entity['implicitPosition'] = 1;
                            $entity['previous'] = null;
                        } else {
                            $entity['implicitPosition'] = 3;
                            $entity['previous'] = $amvsAsset[$i - 1];
                            $this->formatDependencies($entity['previous'], $this->dependencies);
                        }
                    }

                    break;
                }

                ++$i;
            }
        }

        return new JsonModel($entity);
    }

    public function patchList($data)
    {
        $service = $this->getService();

        $service->createLinkedAmvs($data['fromReferential'], $data['toReferential']);

        return new JsonModel([
          'status' =>  'ok',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function create($data)
    {
        if (array_keys($data) === range(0, \count($data) - 1)) {
            /** @var AmvService $amvService */
            $amvService = $this->getService();
            $data = $amvService->createAmvsItems(null, $data);

            if (empty($data)) {
                throw new \Monarc\Core\Exception\Exception('No new information risks to be imported. Already exist in Knowledge Base', 412);
            }
        }

        return parent::create($data);
    }
}
