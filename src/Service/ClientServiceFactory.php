<?php
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2019  SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Service;

use Monarc\Core\Service\AbstractServiceFactory;

/**
 * Factory class attached to ClientService
 * @package Monarc\BackOffice\Service
 */
class ClientServiceFactory extends AbstractServiceFactory
{
    protected $ressources = array(
        'clientTable'=> '\Monarc\BackOffice\Model\Table\ClientTable',
        'clientEntity'=> '\Monarc\BackOffice\Model\Entity\Client',
        'serverTable'=> '\Monarc\BackOffice\Model\Table\ServerTable',
        'serverEntity'=> '\Monarc\BackOffice\Model\Entity\Server',
        'config' => 'Config',
    );
}
