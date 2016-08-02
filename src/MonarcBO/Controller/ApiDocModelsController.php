<?php
namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractController;
use Zend\View\Model\JsonModel;

/**
 * Api Doc Models Controller
 *
 * Class ApiDocModelsController
 * @package MonarcBO\Controller
 */
class ApiDocModelsController extends AbstractController
{
    protected $name = "docmodels";

    public function create($data)
    {
        unset($data['path']);
        $service = $this->getService();
        $file = $this->request->getFiles()->toArray();
        if(!empty($file['file'])){
            $data['path'] = $file['file'];
        }
        $service->create($data);

        return new JsonModel(array('status' => 'ok'));
    }

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

        $service = $this->getService();

        $entities = $service->getList($page, $limit, $order, $filter);
        if (count($this->dependencies)) {
            foreach ($entities as $key => $entity) {
                $this->formatDependencies($entities[$key], $this->dependencies);
            }
        }

        foreach($entities as $k => $v){
            $entities[$k]['path'] = './api/docmodels/'.$v['id'];
        }

        return new JsonModel(array(
            'count' => $service->getFilteredCount($page, $limit, $order, $filter),
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
        if(!empty($entity)){
            $name = pathinfo($entity['path'],PATHINFO_BASENAME);
            $name = explode('_',$name);
            unset($name[0]);
            $name = implode('_',$name);

            $fileContents = file_get_contents($entity['path']);
            if($fileContents !== false){
                $response = $this->getResponse();
                $response->setContent($fileContents);

                $headers = $response->getHeaders();
                $headers->clearHeaders()
                    ->addHeaderLine('Content-Type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document')
                    ->addHeaderLine('Content-Disposition', 'attachment; filename="' . $name . '"')
                    ->addHeaderLine('Content-Length', strlen($fileContents));

                return $this->response;
            }else{
                throw new \Exception('Document template not found');
            }
        } else {
            throw new \Exception('Document template not found');
        }
    }

    public function update($id, $data)
    {
        unset($data['path']);
        $service = $this->getService();
        $file = $this->request->getFiles()->toArray();
        if(!empty($file['file'])){
            $data['path'] = $file['file'];
        }
        $service->update($id,$data);
        return new JsonModel(array('status' => 'ok'));
    }

    public function patch($id, $data)
    {
        unset($data['path']);
        $service = $this->getService();
        $file = $this->request->getFiles()->toArray();
        if(!empty($file['file'])){
            $data['path'] = $file['file'];
        }
        $service->patch($id,$data);
        return new JsonModel(array('status' => 'ok'));
    }
}

