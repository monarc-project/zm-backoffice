<?php
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2018 SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractControllerFactory;

/**
 * Factory class attached to ApiConfigController
 * @package MonarcFO\Controller
 */
class ApiConfigControllerFactory extends AbstractControllerFactory
{
    protected $serviceName = 'MonarcCore\Service\ConfigService';
}

