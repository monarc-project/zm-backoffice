<?php

namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractController;
use Zend\View\Model\JsonModel;

class ApiAnrLibraryController extends AbstractController
{
    protected $name = 'categories';

    public function getList()
    {
        $anrId = $this->params()->fromRoute('anrid');


        $fields = ['id', 'label1', 'label2', 'label3', 'label4', 'position', 'objects'];

        $objectsCategories = $this->getService()->getCategoriesLibraryByAnr($anrId);
        $recursiveArray = $this->recursiveArray($objectsCategories, null, 0, $fields);

        return new JsonModel(array(
            $this->name => $recursiveArray
        ));
    }

    public function get($id)
    {
        return $this->methodNotAllowed();
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
        $anrId = $this->params()->fromRoute('anrid');

        if (!isset($data['objectId'])) {
            throw new \Exception('objectId is missing');
        }

        $id = $this->getService()->attachObjectToAnr($data['objectId'], $anrId);

        return new JsonModel(
            array(
                'status' => 'ok',
                'id' => $id,
            )
        );
    }

    public function update($id, $data)
    {
        return $this->methodNotAllowed();
    }

    public function patch($id, $data)
    {
        return $this->methodNotAllowed();
    }

    public function delete($id)
    {
        return $this->methodNotAllowed();
    }
}

