<?php
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2019  SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractControllerFactory;

/**
 * Factory class attached to ApiUserProfileController
 * @package MonarcFO\Controller
 */
class ApiUserProfileControllerFactory extends AbstractControllerFactory
{
    protected $serviceName = array(
    	'service' => '\MonarcCore\Service\UserProfileService',
    	'connectedUser' => '\MonarcCore\Service\ConnectedUserService',
    );
}
