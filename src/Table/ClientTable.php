<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2024 Luxembourg House of Cybersecurity LHC.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Table;

use Doctrine\ORM\EntityManager;
use Monarc\BackOffice\Entity\Client;
use Monarc\Core\Table\AbstractTable;

class ClientTable extends AbstractTable
{
    public function __construct(EntityManager $entityManager, string $entityName = Client::class)
    {
        parent::__construct($entityManager, $entityName);
    }

    public function findOneByProxyAlias(string $proxyAlias): ?Client
    {
        return $this->getRepository()->createQueryBuilder('c')
            ->where('c.proxyAlias = :proxyAlias')
            ->setParameter('proxyAlias', $proxyAlias)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
