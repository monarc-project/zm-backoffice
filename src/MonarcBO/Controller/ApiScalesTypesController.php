<?php

namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractController;
use Zend\View\Model\JsonModel;

class ApiScalesTypesController extends AbstractController
{
    protected $dependencies = ['scale'];
    protected $name = 'types';
}

