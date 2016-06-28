<?php

namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractController;
use Zend\View\Model\JsonModel;

/**
 * Api Objects Risks Controller
 *
 * Class ApiObjectsRisksController
 * @package MonarcBO\Controller
 */
class ApiObjectsRisksController extends AbstractController
{
    protected $dependencies = ['object', 'amv', 'asset', 'threat', 'vulnerability'];
    protected $name = 'risks';
}

