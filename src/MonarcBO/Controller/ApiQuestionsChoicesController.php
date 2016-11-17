<?php

namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractController;
use Zend\View\Model\JsonModel;

class ApiQuestionsChoicesController extends AbstractController
{
    protected $dependencies = ['questions'];
    protected $name = 'choices';
}

