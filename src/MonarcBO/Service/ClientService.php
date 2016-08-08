<?php
namespace MonarcBO\Service;

use MonarcBO\Model\Entity\Client;
use MonarcBO\Model\Table\ClientTable;
use MonarcCore\Service\AbstractService;

class ClientService extends AbstractService
{
    protected $clientTable;
    protected $clientEntity;
    protected $countryTable;
    protected $countryEntity;
    protected $cityTable;
    protected $cityEntity;
    protected $serverEntity;
    protected $serverTable;

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
        $client = $this->get('clientTable')->get($id);

        if(!empty($client['country_id'])){
            $country = $this->get('countryTable')->get($client['country_id']);
            $client['country'] = $country;
        }
        if(!empty($client['city_id'])){
            $city = $this->get('cityTable')->get($client['city_id']);
            $client['city'] = $city;
        }

        return $client;
    }

    public function create($data)
    {
        /** @var ClientTable $clientTable */
        $clientTable = $this->get('clientTable');

        $entity = $this->get('clientEntity');
        $entity->exchangeArray($data);

        $clientTable->save($entity);

        $this->createJSON($entity);
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
            $entity->exchangeArray($data,true);
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

    public function getJsonData() {
        $var = get_object_vars($this);
        foreach ($var as &$value) {
            if (is_object($value) && method_exists($value,'getJsonData')) {
                $value = $value->getJsonData();
            }
        }
        return $var;
    }


    public function createJSON($client) {
        $serverTable = $this->get('serverTable');
        $server = $serverTable->getEntity($client->get('server_id'));

        if (is_null($server)) {
            return null;
        }
        if ($server->get('fqdn') == '') {
            return null;
        }

        $datas = array(
            'server' => $server->get('fqdn'),
            'client' => array(
                'id'                    => $client->get('id'),
                'model_id'              => $client->get('model_id'),
                'server_id'             => $client->get('server_id'),
                'logo_id'               => $client->get('logo_id'),
                'country_id'            => $client->get('country_id'),
                'city_id'               => $client->get('city_id'),
                'name'                  => $client->get('name'),
                'proxy_alias'           => $client->get('proxy_alias'),
                'address'               => $client->get('address'),
                'postal_code'           => $client->get('postal_code'),
                'phone'                 => $client->get('phone'),
                'fax'                   => $client->get('fax'),
                'email'                 => $client->get('email'),
                'employees_number'      => $client->get('employees_number'),
                'contact_fullname'      => $client->get('contact_fullname'),
                'contact_email'         => $client->get('contact_email'),
                'contact_phone'         => $client->get('contact_phone'),
                'first_user_firstname'  => $client->get('first_user_firstname'),
                'first_user_lastname'   => $client->get('first_user_lastname'),
                'first_user_email'      => $client->get('first_user_email'),
                'first_user_phone'      => $client->get('first_user_phone')
            )
        );

        $now = date('YmdHis');
        $filename = getcwd().'/data/json/'.$now.'.json';
        file_put_contents($filename, json_encode($datas));

        return $filename;
    }
}
