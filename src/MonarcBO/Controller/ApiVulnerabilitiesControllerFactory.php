<?php

namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractControllerFactory;

class ApiVulnerabilitiesControllerFactory extends AbstractControllerFactory
{
    protected $serviceName = '\MonarcCore\Service\VulnerabilityService';
}

