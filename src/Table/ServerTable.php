<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2022  SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Table;

use Doctrine\ORM\EntityManager;
use Monarc\BackOffice\Entity\Server;
use Monarc\Core\Table\AbstractTable;

class ServerTable extends AbstractTable
{
    public function __construct(EntityManager $entityManager, string $entityName = Server::class)
    {
        parent::__construct($entityManager, $entityName);
    }
}
