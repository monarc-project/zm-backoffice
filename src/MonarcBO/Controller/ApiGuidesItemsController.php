<?php

namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractController;
use Zend\View\Model\JsonModel;

class ApiGuidesItemsController extends AbstractController
{
    protected $dependencies = ['guide'];
    protected $name = 'items';
}

