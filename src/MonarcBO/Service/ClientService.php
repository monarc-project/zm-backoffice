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
    protected $forbiddenFields = ['model_id'];

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

    /**
     * Create
     *
     * @param $data
     * @param bool $last
     */
    public function create($data, $last = true)
    {
        /** @var ClientTable $clientTable */
        $clientTable = $this->get('clientTable');

        $entity = $this->get('clientEntity');
        $entity->exchangeArray($data);

        $clientTable->save($entity);

        $this->createJSON($entity);
    }

    public function update($id, $data) {

        //security
        $this->filterPatchFields($data);

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

        $entity = $clientTable->getEntity($id);

        $clientTable->delete($id);

        $this->deleteJSON($entity);
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


    /**
     * Create JSON
     *
     * @param $client
     * @return string
     */
    public function createJSON($client) {
        $serverTable = $this->get('serverTable');
        $server = $serverTable->getEntity($client->get('server_id'));

        if (is_null($server)) {
            return null;
        }
        if ($server->get('fqdn') == '') {
            return null;
        }

        $pathLocal = getcwd()."/config/autoload/local.php";
        $localConf = array();
        if(file_exists($pathLocal)){
            $localConf = require $pathLocal;
        }
        $salt = "";
        if(!empty($localConf['monarc']['salt'])){
            $salt = $localConf['monarc']['salt'];
        }

        //users table database client
        $fieldsUser = array(
            'id'            => 1,
            'status'        => 1,
            'firstname'     => $client->get('first_user_firstname'),
            'lastname'      => $client->get('first_user_lastname'),
            'email'         => $client->get('first_user_email'),
            'phone'         => $client->get('first_user_phone'),
            'password'      => password_hash($salt.$client->get('first_user_email'),PASSWORD_BCRYPT),
            'creator'       => 'System',
            'created_at'    => date('Y-m-d H:i:s')
        );

        $sqlDumpUsers = '';
        $listValues = $this->getListValues($fieldsUser, $serverTable);
        if ($listValues != '') {
            $sqlDumpUsers = 'INSERT INTO `users` SET ' . $listValues . ';';
        }

        //users_roles table database client
        $role1Values = [
            'user_id'       => 1,
            'role'          => 'superadminfo',
            'creator'       => 'System',
            'created_at'    => date('Y-m-d H:i:s')
        ];
        $role2Values = [
            'user_id'       => 1,
            'role'          => 'userfo',
            'creator'       => 'System',
            'created_at'    => date('Y-m-d H:i:s')
        ];
        $sqlDumpUsersRoles = '';
        $listValues = $this->getListValues($role1Values, $serverTable);
        if ($listValues != '') {
            $sqlDumpUsersRoles = 'INSERT INTO `users_roles` SET ' . $listValues . ';';
        }
        $listValues = $this->getListValues($role2Values, $serverTable);
        if ($listValues != '') {
            $sqlDumpUsersRoles .= ' INSERT INTO `users_roles` SET ' . $listValues . ';';
        }

        //clients table database client
        $fieldsClient = array(
            'id'                    => $client->get('id'),
            'model_id'              => $client->get('model_id'),
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
            'first_user_phone'      => $client->get('first_user_phone'),
            'creator'               => 'System',
            'created_at'            => date('Y-m-d H:i:s')
        );

        $sqlDumpClients = '';
        $listValues = $this->getListValues($fieldsClient, $serverTable);
        if ($listValues != '') {
            $sqlDumpClients = 'INSERT INTO `clients` SET ' . $listValues . ';';
        }

        $datas = array(
            'server' => $server->get('fqdn'),
            'proxy_alias' => $client->get('proxyAlias'),
            'sql_bootstrap' => $sqlDumpUsers . ' ' . $sqlDumpUsersRoles . ' ' . $sqlDumpClients
        );

        if (!is_dir(getcwd().'/data/json/')) {
            mkdir(getcwd().'/data/json/');
        }
        $now = date('YmdHis');
        $filename = getcwd().'/data/json/'.$now.'.json';
        file_put_contents($filename, json_encode($datas));

        return $filename;
    }

    public function deleteJSON($client) {

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
            'proxy_alias' => $client->get('proxyAlias')
        );

        if (!is_dir(getcwd().'/data/json/')) {
            mkdir(getcwd().'/data/json/');
        }
        $now = date('YmdHis');
        $filename = getcwd().'/data/json/'.$now.'.json';
        file_put_contents($filename, json_encode($datas));

        return $filename;
    }

    protected function getListValues($fieldsValues, $serverTable) {
        $listValues = '';
        foreach ($fieldsValues as $key => $value) {
            if ($key != '' && !is_null($value)) {
                if ($listValues != '') $listValues .= ', ';

                if (is_numeric($value)) {
                    $listValues .= "`$key` = ".$serverTable->getDb()->quote($value, \PDO::PARAM_INT);
                }
                else {
                    $listValues .= "`$key` = ".$serverTable->getDb()->quote($value, \PDO::PARAM_STR);
                }
            }
        }

        return $listValues;
    }
}
