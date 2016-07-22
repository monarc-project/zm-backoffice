<?php

namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractControllerFactory;

class ApiUserProfileControllerFactory extends AbstractControllerFactory
{
    protected $serviceName = array(
    	'service' => '\MonarcCore\Service\UserProfileService',
    	'connectedUser' => '\MonarcCore\Service\ConnectedUserService',
    );
}
