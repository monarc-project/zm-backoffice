<?php
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2018 SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace MonarcBO\Service;

use MonarcCore\Service\AbstractServiceFactory;

/**
 * Factory class attached to ClientService
 * @package MonarcBO\Service
 */
class ClientServiceFactory extends AbstractServiceFactory
{
    protected $ressources = array(
        'clientTable'=> '\MonarcBO\Model\Table\ClientTable',
        'clientEntity'=> '\MonarcBO\Model\Entity\Client',
        'serverTable'=> '\MonarcBO\Model\Table\ServerTable',
        'serverEntity'=> '\MonarcBO\Model\Entity\Server',
        'config' => 'Config',
    );
}
