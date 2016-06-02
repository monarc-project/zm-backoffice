<?php

namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractController;
use Zend\View\Model\JsonModel;

/**
 * Api Config Controller
 *
 * Class ApiConfigController
 * @package MonarcBO\Controller
 */
class ApiConfigController extends AbstractController
{
    /**
     * Get list
     *
     * @return JsonModel
     */
    public function getList()
    {
        return new JsonModel($this->getService()->getLanguage());
    }

}

