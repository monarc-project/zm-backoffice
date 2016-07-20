<?php

namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractController;
use Zend\View\Model\JsonModel;

/**
 * Api Amvs Controller
 *
 * Class ApiAmvsController
 * @package MonarcBO\Controller
 */
class ApiAmvsController extends AbstractController
{
    protected $dependencies = ['asset', 'threat', 'vulnerability', 'measure1', 'measure2', 'measure3'];
    protected $name = "amvs";
}

