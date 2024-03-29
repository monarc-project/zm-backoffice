<?php
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2019  SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Model\Table;

use Monarc\BackOffice\Model\DbCli;
use Monarc\BackOffice\Model\Entity\Client;
use Monarc\BackOffice\Model\Entity\ClientModel;
use Monarc\Core\Model\Table\AbstractEntityTable;
use Monarc\Core\Service\ConnectedUserService;

/**
 * Class ClientModelTable
 *
 * @package Monarc\BackOffice\Model\Table
 */
class ClientModelTable extends AbstractEntityTable
{
    public function __construct(
        DbCli $dbService,
        ConnectedUserService $connectedUserService
    ) {
        parent::__construct($dbService, ClientModel::class, $connectedUserService);
    }

    public function deleteByClientAndModelIds(Client $client, array $modelIds): void
    {
        $queryBuilder = $this->getRepository()->createQueryBuilder('cm');
        $clientModels = $queryBuilder
            ->where('cm.client = :client')
            ->andWhere($queryBuilder->expr()->in('cm.modelId', array_map('\intval', $modelIds)))
            ->setParameter('client', $client)
            ->getQuery()
            ->getResult();

        foreach ($clientModels as $clientModel) {
            $this->getDb()->delete($clientModel);
        }
    }
}
