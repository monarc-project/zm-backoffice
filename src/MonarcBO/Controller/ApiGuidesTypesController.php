<?php

namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractController;
use Zend\View\Model\JsonModel;

class ApiGuidesTypesController extends AbstractController
{
    /**
     * Get List
     *
     * @return JsonModel
     */
    public function getList()
    {
        return new JsonModel(array(
            'type' => $this->getService()->getTypes()
        ));
    }
}

