<?php
namespace MonarcBO\Service;

use MonarcBO\Model\Entity\Client;
use MonarcBO\Model\Table\ClientTable;
use MonarcCore\Service\AbstractService;

class ClientService extends AbstractService
{
    protected $clientTable;
    protected $clientEntity;

    public function getTotalCount()
    {
        /** @var ClientTable $clientTable */
        $clientTable = $this->get('clientTable');
        return $clientTable->count();
    }

    public function getFilteredCount($page = 1, $limit = 25, $order = null, $filter = null, $filterAnd = null)
    {
        /** @var ClientTable $clientTable */
        $clientTable = $this->get('clientTable');

        return $clientTable->countFiltered($page, $limit, $this->parseFrontendOrder($order),
            $this->parseFrontendFilter($filter, array('name', 'address', 'postalcode', 'phone', 'email',
                'contact_fullname', 'contact_email', 'contact_phone')));
    }

    public function getList($page = 1, $limit = 25, $order = null, $filter = null, $filterAnd = null)
    {
        /** @var ClientTable $clientTable */
        $clientTable = $this->get('clientTable');

        return $clientTable->fetchAllFiltered(
            array('id', 'name', 'proxy_alias', 'address', 'postalcode', 'phone', 'fax', 'email', 'contact_fullname',
                'employees_number', 'contact_email', 'contact_phone', 'model_id'),
            $page,
            $limit,
            $this->parseFrontendOrder($order),
            $this->parseFrontendFilter($filter, array('name', 'address', 'postalcode', 'phone', 'email',
                'contact_fullname', 'contact_email', 'contact_phone'))
        );
    }

    public function getEntity($id)
    {
        return $this->get('clientTable')->get($id);
    }

    public function create($data)
    {
        /** @var ClientTable $clientTable */
        $clientTable = $this->get('clientTable');

        $entity = new Client();
        $entity->exchangeArray($data);

        $clientTable->save($entity);
    }

    public function update($id, $data) {
        /** @var ClientTable $clientTable */
        $clientTable = $this->get('clientTable');

        /** @var Client $entity */
        $entity = $clientTable->getEntity($id);

        if (isset($data['proxy_alias'])) {
            // Don't allow changing the proxy_alias once set
            unset($data['proxy_alias']);
        }

        if ($entity != null) {
            $entity->exchangeArray($data);
            $clientTable->save($entity);
            return true;
        } else {
            return false;
        }
    }

    public function delete($id)
    {
        /** @var ClientTable $clientTable */
        $clientTable = $this->get('clientTable');

        $clientTable->delete($id);
    }
}
