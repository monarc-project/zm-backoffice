<?php

namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractController;
use MonarcCore\Service\InstanceRiskOpService;

class ApiAnrInstancesRisksOpController extends AbstractController
{
    protected $name = 'instances-oprisks';

    public function getList()
    {
        return $this->methodNotAllowed();
    }

    public function get($id)
    {
        return $this->methodNotAllowed();
    }

    public function create($data)
    {
        return $this->methodNotAllowed();
    }
}

