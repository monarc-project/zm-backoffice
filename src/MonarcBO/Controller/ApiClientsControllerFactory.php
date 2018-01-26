<?php
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2018 SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractControllerFactory;

/**
 * Factory class attached to ApiClientsController
 * @package MonarcFO\Controller
 */
class ApiClientsControllerFactory extends AbstractControllerFactory
{
    protected $serviceName = '\MonarcBO\Service\ClientService';
}

