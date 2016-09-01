<?php

namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractController;
use MonarcCore\Service\InstanceService;
use Zend\View\Model\JsonModel;

class ApiAnrInstancesController extends AbstractController
{
    protected $name = 'instances';

    protected $dependencies = ['anr', 'asset', 'object', 'root', 'parent'];

    /**
     * Get List
     *
     * @return JsonModel
     */
    public function getList()
    {
        $anrId = (int) $this->params()->fromRoute('anrid');

        /** @var InstanceService $service */
        $service = $this->getService();
        $instances = $service->findByAnr($anrId);

        $fields = ['id', 'level',
            'c', 'i', 'd', 'ch', 'ih', 'dh',
            'name1', 'name2', 'name3', 'name4',
            'label1', 'label2', 'label3', 'label4',
            'description1', 'description2', 'description3', 'description4'];
        $recursiveArray = $this->recursiveArray($instances, null, 0, $fields);

        return new JsonModel(array(
            $this->name => $recursiveArray
        ));
    }

    /**
     * Update
     *
     * @param mixed $id
     * @param mixed $data
     * @return JsonModel
     */
    public function update($id, $data)
    {
        $anrId = (int) $this->params()->fromRoute('anrid');

        /** @var InstanceService $service */
        $service = $this->getService();
        $service->updateInstance($anrId, $id, $data);

        return new JsonModel(array('status' => 'ok'));
    }

    /**
     * Patch
     *
     * @param mixed $id
     * @param mixed $data
     * @return JsonModel
     */
    public function patch($id, $data)
    {
        $anrId = (int) $this->params()->fromRoute('anrid');

        /** @var InstanceService $service */
        $service = $this->getService();
        $service->patchInstance($anrId, $id, $data);

        return new JsonModel(array('status' => 'ok'));
    }


    public function get($id)
    {
        $anrId = (int) $this->params()->fromRoute('anrid');

        /** @var InstanceService $service */
        $service = $this->getService();
        $entity = $service->getEntityByIdAndAnr($id, $anrId);

        if (count($this->dependencies)) {
            $this->formatDependencies($entity, $this->dependencies);
        }

        return new JsonModel($entity);
    }

    /**
     * Create
     *
     * @param mixed $data
     * @return JsonModel
     * @throws \Exception
     */
    public function create($data)
    {
        $anrId = (int) $this->params()->fromRoute('anrid');

        //verification required
        $required = ['object', 'parent', 'position'];
        $missing = [];
        foreach ($required as $field) {
            if (!array_key_exists($field, $data)) {
                $missing[] = $field . ' missing';
            }
        }
        if (count($missing)) {
            throw new \Exception(implode(', ', $missing), 412);
        }

        /** @var InstanceService $service */
        $service = $this->getService();
        $id = $service->instantiateObjectToAnr($anrId, $data);

        return new JsonModel(
            array(
                'status' => 'ok',
                'id' => $id,
            )
        );
    }
}

