<?php
namespace MonarcBO\Service;

use MonarcBO\Model\Entity\Server;
use MonarcBO\Model\Table\ServerTable;
use MonarcCore\Service\AbstractService;

class ServerService extends AbstractService
{
    protected $serverTable;
    protected $serverEntity;

    public function getTotalCount()
    {
        /** @var ServerTable $serverTable */
        $serverTable = $this->get('serverTable');
        return $serverTable->count();
    }

    public function getFilteredCount($page = 1, $limit = 25, $order = null, $filter = null, $filterAnd = null)
    {
        /** @var ServerTable $serverTable */
        $serverTable = $this->get('serverTable');

        return $serverTable->countFiltered($page, $limit, $this->parseFrontendOrder($order),
            $this->parseFrontendFilter($filter, array('label', 'ip_address', 'fqdn')));
    }

    public function getList($page = 1, $limit = 25, $order = null, $filter = null, $filterAnd = null)
    {
        /** @var ServerTable $serverTable */
        $serverTable = $this->get('serverTable');

        return $serverTable->fetchAllFiltered(
            array('id', 'label', 'ip_address', 'fqdn', 'status'),
            $page,
            $limit,
            $this->parseFrontendOrder($order),
            $this->parseFrontendFilter($filter, array('label', 'ip_address', 'fqdn'))
        );
    }

    public function getEntity($id)
    {
        return $this->get('serverTable')->get($id);
    }

    public function create($data)
    {
        /** @var ServerTable $serverTable */
        $serverTable = $this->get('serverTable');

        $entity = new Server();
        $entity->exchangeArray($data);

        $serverTable->save($entity);
    }

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

    public function delete($id)
    {
        /** @var ServerTable $serverTable */
        $serverTable = $this->get('serverTable');

        $serverTable->delete($id);
    }
}