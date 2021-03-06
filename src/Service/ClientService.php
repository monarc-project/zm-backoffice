<?php
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2019  SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Service;

use Monarc\BackOffice\Model\Entity\Client;
use Monarc\BackOffice\Model\Table\ClientTable;
use Monarc\Core\Service\AbstractService;
use PDO;
use RuntimeException;

/**
 * This class is the service that handles clients.
 * @see \Monarc\BackOffice\Model\Entity\Client
 * @see \Monarc\BackOffice\Model\Table\ClientTable
 * @package Monarc\BackOffice\Service
 */
class ClientService extends AbstractService
{
    protected $clientTable;
    protected $clientEntity;
    protected $serverEntity;
    protected $serverTable;
    protected $forbiddenFields = ['model_id'];
    protected $config;

    /**
     * @inheritdoc
     */
    public function getFilteredCount($filter = null, $filterAnd = null)
    {
        /** @var ClientTable $clientTable */
        $clientTable = $this->get('table');

        return $clientTable->countFiltered($this->parseFrontendFilter($filter, array('name', 'first_user_email',
            'proxyAlias', 'createdAt')));
    }

    /**
     * @inheritdoc
     */
    public function getList($page = 1, $limit = 25, $order = null, $filter = null, $filterAnd = null)
    {
        /** @var ClientTable $clientTable */
        $clientTable = $this->get('table');

        return $clientTable->fetchAllFiltered(
            array('id', 'name', 'first_user_email', 'proxyAlias', 'createdAt', 'model_id'),
            $page,
            $limit,
            $this->parseFrontendOrder($order),
            $this->parseFrontendFilter($filter, array('name', 'first_user_email', 'proxyAlias', 'createdAt', 'model_id'))
        );
    }

    /**
     * @inheritdoc
     */
    public function getEntity($id)
    {
        return $this->get('table')->get($id);
    }

    /**
     * @inheritdoc
     */
    public function create($data, $last = true)
    {
        /** @var ClientTable $clientTable */
        $clientTable = $this->get('table');

        /** @var Client $client */
        $client = $this->get('clientEntity');
        $client->exchangeArray($data);

        $client->setCreator($this->getConnectedUser()->getFirstname() . ' ' . $this->getConnectedUser()->getLastname());

        $clientTable->save($client);

        $this->createJson($client);
    }

    /**
     * @inheritdoc
     */
    public function update($id, $data)
    {
        //security
        $this->filterPatchFields($data);

        /** @var ClientTable $clientTable */
        $clientTable = $this->get('table');

        /** @var Client $entity */
        $entity = $clientTable->getEntity($id);

        if (isset($data['proxy_alias'])) {
            // Don't allow changing the proxy_alias once set
            unset($data['proxy_alias']);
        }

        if ($entity !== null) {
            $entity->exchangeArray($data, true);
            $entity->setUpdater(
                $this->getConnectedUser()->getFirstname() . ' ' . $this->getConnectedUser()->getLastname()
            );

            $clientTable->save($entity);

            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function delete($id)
    {
        /** @var ClientTable $clientTable */
        $clientTable = $this->get('table');

        $entity = $clientTable->getEntity($id);

        $clientTable->delete($id);

        if ($this->deleteJson($entity)) {
            return true;
        }

        return false;
    }

    /**
     * Created the JSON file to build the client environment on a server and stores it in data/json/.
     * Then returns the JSON file path.
     *
     * @return string The JSON file path
     */
    private function createJson(Client $client)
    {
        $serverTable = $this->get('serverTable');
        $server = $serverTable->getEntity($client->get('server_id'));

        if ($server === null) {
            return null;
        }
        if ($server->get('fqdn') === '') {
            return null;
        }

        $pathLocal = getcwd() . "/config/autoload/local.php";
        $localConf = array();
        if (file_exists($pathLocal)) {
            $localConf = require $pathLocal;
        }
        $salt = '';
        if (!empty($localConf['monarc']['salt'])) {
            $salt = $localConf['monarc']['salt'];
        }

        //users table database client
        $fieldsUser = array(
            'id' => 1,
            'status' => 1,
            'firstname' => $client->get('first_user_firstname'),
            'lastname' => $client->get('first_user_lastname'),
            'email' => $client->get('first_user_email'),
            'language' => 1,
            'password' => password_hash($salt . $client->get('first_user_email'), PASSWORD_BCRYPT),
            'creator' => 'System',
            'created_at' => date('Y-m-d H:i:s')
        );

        $sqlDumpUsers = '';
        $listValues = $this->getListValues($fieldsUser, $serverTable);
        if ($listValues !== '') {
            $sqlDumpUsers = 'INSERT INTO `users` SET ' . $listValues . ';';
        }

        //users_roles table database client
        $role1Values = [
            'user_id' => 1,
            'role' => 'superadminfo',
            'creator' => 'System',
            'created_at' => date('Y-m-d H:i:s')
        ];
        $role2Values = [
            'user_id' => 1,
            'role' => 'userfo',
            'creator' => 'System',
            'created_at' => date('Y-m-d H:i:s')
        ];
        $sqlDumpUsersRoles = '';
        $listValues = $this->getListValues($role1Values, $serverTable);
        if ($listValues !== '') {
            $sqlDumpUsersRoles = 'INSERT INTO `users_roles` SET ' . $listValues . ';';
        }
        $listValues = $this->getListValues($role2Values, $serverTable);
        if ($listValues !== '') {
            $sqlDumpUsersRoles .= ' INSERT INTO `users_roles` SET ' . $listValues . ';';
        }

        //clients table database client
        $fieldsClient = array(
            'id' => $client->get('id'),
            'model_id' => $client->get('model_id'),
            'logo_id' => $client->get('logo_id'),
            'name' => $client->get('name'),
            'proxy_alias' => $client->get('proxy_alias'),
            'first_user_firstname' => $client->get('first_user_firstname'),
            'first_user_lastname' => $client->get('first_user_lastname'),
            'first_user_email' => $client->get('first_user_email'),
            'creator' => 'System',
            'created_at' => date('Y-m-d H:i:s')
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

        $path = $this->config['spool_path_create'];

        return $this->createJsonFile($path, $datas);
    }

    /**
     * Created the JSON file to delete the client environment on a server and stores it in data/json/.
     * Then returns the JSON file path.
     *
     * @return string The JSON file path
     */
    private function deleteJson(Client $client)
    {
        $serverTable = $this->get('serverTable');
        $server = $serverTable->getEntity($client->get('server_id'));

        if ($server === null) {
            return null;
        }
        if ($server->get('fqdn') === '') {
            return null;
        }

        $datas = array(
            'server' => $server->get('fqdn'),
            'proxy_alias' => $client->get('proxyAlias')
        );

        $path = $this->config['spool_path_delete'];

        return $this->createJsonFile($path, $datas);
    }

    /**
     * Create a formatted list of data to insert.
     *
     * @param array $fieldsValues The list of data to be inserted
     * @param \Monarc\BackOffice\Model\Table\ServerTable $serverTable
     *
     * @return string The formatted list of data to insert
     */
    private function getListValues($fieldsValues, $serverTable)
    {
        $listValues = '';
        foreach ($fieldsValues as $key => $value) {
            if ($key !== '' && $value !== null) {
                if ($listValues !== '') {
                    $listValues .= ', ';
                }

                if (is_numeric($value)) {
                    $listValues .= "`$key` = " . $serverTable->getDb()->quote($value, PDO::PARAM_INT);
                } else {
                    $listValues .= "`$key` = " . $serverTable->getDb()->quote($value, PDO::PARAM_STR);
                }
            }
        }

        return $listValues;
    }

    private function createJsonFile($path, array $datas): string
    {
        if (!is_dir($path) && !mkdir($path, 0750, true) && !is_dir($path)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $path));
        }
        $now = date('YmdHis');
        $filename = $path . $now . '.json';
        file_put_contents($filename, json_encode($datas));

        return $filename;
    }
}
