<?php
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2019  SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Model\Table;

use Monarc\BackOffice\Model\DbCli;
use Monarc\BackOffice\Model\Entity\Client;
use Monarc\Core\Model\Table\AbstractEntityTable;
use Monarc\Core\Service\ConnectedUserService;

/**
 * Class ClientTable
 *
 * @package Monarc\BackOffice\Model\Table
 */
class ClientTable extends AbstractEntityTable
{
    public function __construct(
        DbCli $dbService,
        ConnectedUserService $connectedUserService
    ) {
        parent::__construct($dbService, Client::class, $connectedUserService);
    }

    public function findById(int $id): ?Client
    {
        return $this->getRepository()
            ->createQueryBuilder('s')
            ->where('s.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
