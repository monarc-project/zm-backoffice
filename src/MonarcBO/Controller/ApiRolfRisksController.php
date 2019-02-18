<?php
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2019  SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractController;
use MonarcCore\Service\RolfRiskService;
use Zend\View\Model\JsonModel;

/**
 * Api Rolf Risks Controller
 *
 * Class ApiRolfRisksController
 * @package MonarcBO\Controller
 */
class ApiRolfRisksController extends AbstractController
{
    protected $name = 'risks';
    protected $dependencies = ['measures','tags'];

    public function get($id)
    {
        $entity = $this->getService()->getEntity($id);

        if (count($this->dependencies)) {
            $this->formatDependencies($entity, $this->dependencies, '\MonarcCore\Model\Entity\Measure', ['referential']);
        }

        return new JsonModel($entity);
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
        $category = $this->params()->fromQuery('category');
        $tag = $this->params()->fromQuery('tag');

        /** @var RolfRiskService $service */
        $service = $this->getService();

        $rolfRisks = $service->getListSpecific($page, $limit, $order, $filter, $category, $tag);
        foreach($rolfRisks as $key => $rolfRisk){

            if (count($this->dependencies)) {
                    $this->formatDependencies($rolfRisks[$key], $this->dependencies, '\MonarcCore\Model\Entity\Measure', ['referential']);
            }

            $rolfRisk['tags']->initialize();
            $rolfTags = $rolfRisk['tags']->getSnapshot();
            $rolfRisks[$key]['tags'] = array();
            foreach($rolfTags as $rolfTag){
                $rolfRisks[$key]['tags'][] = $rolfTag->getJsonArray();
            }
        }

        return new JsonModel(array(
            'count' => $service->getFilteredCount($filter),
            $this->name => $rolfRisks
        ));
    }

    public function update($id, $data)
    {
      $measures= array();
      foreach ($data['measures'] as $measure) {
        $measures[] = ['uuid' => $measure];
      }
      $data['measures'] = $measures;
      return parent::update($id, $data);
    }

    public function patchList($data)
    {
      $service = $this->getService();

      $service->createLinkedRisks($data['fromReferential'],$data['toReferential']);

      return new JsonModel([
          'status' =>  'ok',
      ]);
    }

}
