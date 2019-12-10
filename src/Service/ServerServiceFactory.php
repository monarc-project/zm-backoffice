<?php
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2019  SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Service;

use Monarc\BackOffice\Model\Entity\Server;
use Monarc\BackOffice\Model\Table\ServerTable;
use Monarc\Core\Service\AbstractServiceFactory;

/**
 * Factory class attached to ServerService
 * @package Monarc\BackOffice\Service
 */
class ServerServiceFactory extends AbstractServiceFactory
{
    protected $ressources = array(
        'table'=> ServerTable::class,
        'serverEntity'=> Server::class,
    );
}
