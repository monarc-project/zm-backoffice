<?php

namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractController;
use Zend\View\Model\JsonModel;

/**
 * Api Themes Controller
 *
 * Class ApiThemesController
 * @package MonarcBO\Controller
 */
class ApiThemesController extends AbstractController
{
    protected $name = 'themes';

    public function delete($id)
    {
        return $this->methodNotAllowed();
    }

}

