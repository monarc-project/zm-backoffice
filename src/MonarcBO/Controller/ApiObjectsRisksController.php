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

    public function get($id)
    {
        $this->methodNotAllowed();
    }

    public function getList()
    {
        $this->methodNotAllowed();
    }

    public function create($data)
    {
        $this->methodNotAllowed();
    }

    public function delete($id)
    {
        $this->methodNotAllowed();
    }
}

