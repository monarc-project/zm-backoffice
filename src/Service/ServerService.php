<?php
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2019  SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */
namespace Monarc\BackOffice\Service;

use Monarc\BackOffice\Model\Entity\Server;
use Monarc\BackOffice\Model\Table\ServerTable;
use Monarc\Core\Service\AbstractService;

/**
 * This class is the service that handles servers.
 * @see \Monarc\BackOffice\Model\Entity\Server
 * @see \Monarc\BackOffice\Model\Table\ServerTable
 * @package Monarc\BackOffice\Service
 */
class ServerService extends AbstractService
{
    protected $serverTable;
    protected $serverEntity;

    /**
     * Counts and returns the number of servers in database
     * @return int The number of servers
     */
    public function getTotalCount()
    {
        /** @var ServerTable $serverTable */
        $serverTable = $this->get('serverTable');
        return $serverTable->count();
    }

    /**
     * @inheritdoc
     */
    public function getFilteredCount($filter = null, $filterAnd = null)
    {
        /** @var ServerTable $serverTable */
        $serverTable = $this->get('serverTable');

        return $serverTable->countFiltered($this->parseFrontendFilter($filter, array('label', 'ip_address', 'fqdn')), $filterAnd);
    }

    /**
     * @inheritdoc
     */
    public function getList($page = 1, $limit = 25, $order = null, $filter = null, $filterAnd = null)
    {
        /** @var ServerTable $serverTable */
        $serverTable = $this->get('serverTable');

        return $serverTable->fetchAllFiltered(
            array('id', 'label', 'ip_address', 'fqdn', 'status'),
            $page,
            $limit,
            $this->parseFrontendOrder($order),
            $this->parseFrontendFilter($filter, array('label', 'ip_address', 'fqdn')),
            $filterAnd
        );
    }

    /**
     * @inheritdoc
     */
    public function getEntity($id)
    {
        return $this->get('serverTable')->get($id);
    }

    /**
     * @inheritdoc
     */
    public function create($data, $last = true)
    {
        /** @var ServerTable $serverTable */
        $serverTable = $this->get('serverTable');

        $entity = new Server();
        $entity->exchangeArray($data);

        $serverTable->save($entity);
    }

    /**
     * @inheritdoc
     */
    public function update($id, $data) {
        /** @var ServerTable $serverTable */
        $serverTable = $this->get('serverTable');

        /** @var Server $entity */
        $entity = $serverTable->getEntity($id);

        if ($entity != null) {
            $entity->exchangeArray($data);
            $serverTable->save($entity);
            return true;
        } else {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function delete($id)
    {
        /** @var ServerTable $serverTable */
        $serverTable = $this->get('serverTable');

        $serverTable->delete($id);
    }
}
