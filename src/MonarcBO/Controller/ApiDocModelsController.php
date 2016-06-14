<?php
namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractController;
use Zend\View\Model\JsonModel;

/**
 * Api Admin Historicals Controller
 *
 * Class ApiAdminHistoricalsController
 * @package MonarcBO\Controller
 */
class ApiDocModelsController extends AbstractController
{
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

        return new JsonModel(array(
            'count' => $this->getService()->getFilteredCount($page, $limit, $order, $filter),
            'docmodels' => $this->getService()->getList($page, $limit, $order, $filter)
        ));
    }

    public function create($data)
    {
        $service = $this->getService();
        $file = $this->request->getFiles()->toArray();
        if(!empty($file['file'])){
            $data['path'] = $file['file'];
        }
        $service->create($data);

        return new JsonModel(array('status' => 'ok'));
    }

    /**
     * Delete
     *
     * @param mixed $id
     * @return JsonModel
     */
    public function delete($id)
    {
        $this->getService()->delete($id);

        return new JsonModel(array('status' => 'ok'));
    }
}

