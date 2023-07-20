<?php
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2019  SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Service;

use Monarc\BackOffice\Model\Entity\Client;
use Monarc\BackOffice\Model\Entity\ClientModel;
use Monarc\BackOffice\Model\Table\ClientModelTable;
use Monarc\BackOffice\Model\Table\ClientTable;
use Monarc\Core\Service\AbstractService;
use PDO;
use RuntimeException;

/**
 * This class is the service that handles clients.
 *
 * @see     \Monarc\BackOffice\Model\Entity\Client
 * @see     \Monarc\BackOffice\Model\Table\ClientTable
 * @package Monarc\BackOffice\Service
 */
class ClientService extends AbstractService
{
    protected $clientTable;
    protected $clientEntity;
    protected $serverEntity;
    protected $serverTable;
    protected $clientModelEntity;
    protected $clientModelTable;
    protected $config;

    /**
     * @inheritdoc
     */
    public function getFilteredCount($filter = null, $filterAnd = null)
    {
        /**
         *   @var ClientTable $clientTable
         */
        $clientTable = $this->get('table');

        return $clientTable->countFiltered(
            $this->parseFrontendFilter(
                $filter, array('name', 'first_user_email',
                'proxyAlias', 'createdAt')
            )
        );
    }

    /**
     * @inheritdoc
     */
    public function getList(
        $page = 1,
        $limit = 25,
        $order = null,
        $filter = null,
        $filterAnd = null
    ) {
        /**
         * @var ClientTable $clientTable
         */
        $clientTable = $this->get('table');

        return $clientTable->fetchAllFiltered(
            [
                'id',
                'name',
                'first_user_email',
                'proxyAlias',
                'createdAt',
                'twoFactorAuthEnforced',
                'isBackgroundImportActive',
            ],
            $page,
            $limit,
            $this->parseFrontendOrder($order),
            $this->parseFrontendFilter($filter, ['name', 'first_user_email', 'proxyAlias', 'createdAt'])
        );
    }

    /**
     * @inheritdoc
     */
    public function getEntity($id)
    {
        $entity = $this->get('table')->findById($id);
        $return = $entity->getJsonArray();
        unset($return['models']); //need to unset to avoid circular issue
        foreach ($entity->getModels() as $model) {
            //model_id is asked by FE
            $return['model_id'][] = $model->getModelId();
        }
        return $return;
    }

    /**
     * @inheritdoc
     */
    public function create($data, $last = true)
    {
        /**
         * @var ClientTable $clientTable
         */
        $clientTable = $this->get('table');

        /**
         * @var Client $client
         */
        $client = $this->get('clientEntity');
        $client->exchangeArray($data);

        $client->setCreator($this->getConnectedUser()->getEmail());

        if (isset($data['twoFactorAuthEnforced'])) {
            $client->setTwoFactorAuthEnforced((bool)$data['twoFactorAuthEnforced']);
        }
        if (isset($data['isBackgroundImportActive'])) {
            $client->setIsBackgroundImportActive((bool)$data['isBackgroundImportActive']);
        }

        $clientTable->save($client, false);
        $dataModels = null;

        if (isset($data['model_id'])) {
            $dataModels = $data['model_id'];
            unset($data['model_id']);
        }
        if ($dataModels !== null) {
            $clientModelTable = $this->get('clientModelTable');
            //link model
            foreach ($dataModels as $newModel) {
                    $clientModel = (new ClientModel())
                        ->setClient($client)
                        ->setModelId($newModel)
                        ->setCreator($this->getConnectedUser()->getEmail());
                    $clientModelTable->save($clientModel);
                    $client->getModels()->add($clientModel);
            }
        }
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

        /**
         * @var ClientTable $clientTable
         */
        $clientTable = $this->get('table');

        /**
         * @var Client $entity
         */
        $entity = $clientTable->getEntity($id);

        if (isset($data['proxy_alias'])) {
            // Don't allow changing the proxy_alias once set
            unset($data['proxy_alias']);
        }

        if ($entity !== null) {
            $updateData = [];

            $dataModels = null;
            if (isset($data['model_id'])) {
                $dataModels = $data['model_id'];
                unset($data['model_id']);
            }

            if ($data['first_user_email'] !== $entity->get('first_user_email')
                || $data['first_user_firstname'] !== $entity->get('first_user_firstname')
                || $data['first_user_lastname'] !== $entity->get('first_user_lastname')
            ) {
                $updateData['client'] = [
                    'oldEmail' => $entity->get('first_user_email'),
                    'email' => $data['first_user_email'],
                    'firstName' => $data['first_user_firstname'],
                    'lastName' => $data['first_user_lastname'],
                ];
            }

            if (isset($data['twoFactorAuthEnforced'])
                && (bool)$data['twoFactorAuthEnforced'] !== $entity->isTwoFactorAuthEnforced()
            ) {
                $updateData['twoFactorAuthEnforced'] = (bool)$data['twoFactorAuthEnforced'];
            }
            if (isset($data['isBackgroundImportActive'])
                && (bool)$data['isBackgroundImportActive'] !== $entity->isBackgroundImportActive()
            ) {
                $updateData['isBackgroundImportActive'] = (bool)$data['isBackgroundImportActive'];
            }
            if (!empty($data['resetTwoFactorAuth'])) {
                $updateData['resetTwoFactorAuth'] = true;
            }

            $entity->exchangeArray($data, true);
            $entity->setUpdater($this->getConnectedUser()->getEmail());

            if (isset($data['twoFactorAuthEnforced'])) {
                $entity->setTwoFactorAuthEnforced((bool)$data['twoFactorAuthEnforced']);
            }
            if (!isset($data['isBackgroundImportActive'])) {
                $entity->setIsBackgroundImportActive((bool)$data['isBackgroundImportActive']);
            }

            if ($dataModels !== null) {
                /** @var ClientModelTable $clientModelTable */
                $clientModelTable = $this->get('clientModelTable');

                $existingModelIds = [];
                foreach ($entity->getModels() as $model) {
                    if (\in_array($model->getModelId(), $dataModels, true)) {
                        $existingModelIds[] = $model->getModelId();
                    } else {
                        $updateData['modelIdsToRemove'][] = $model->getModelId();
                    }
                }
                $modelIdsToAdd = array_diff($dataModels, $existingModelIds);

                //link model
                foreach ($modelIdsToAdd as $newModelId) {
                    $clientModel = (new ClientModel())
                        ->setClient($entity)
                        ->setModelId($newModelId)
                        ->setCreator($this->getConnectedUser()->getEmail());
                    $clientModelTable->save($clientModel, false);
                    $entity->getModels()->add($clientModel);

                    $updateData['modelIdsToAdd'][] = $newModelId;
                }
                if (!empty($updateData['modelIdsToRemove'])) {
                    $clientModelTable->deleteByModelIds($updateData['modelIdsToRemove']);
                }
            }

            $clientTable->save($entity);

            if (!empty($updateData)) {
                $this->generateUpdateClientJson($entity, $updateData);
            }

            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function delete($id)
    {
        /**
         * @var ClientTable $clientTable
         */
        $clientTable = $this->get('table');

        $entity = $clientTable->getEntity($id);

        $clientTable->delete($id);

        if ($this->deleteJson($entity)) {
            return true;
        }

        return false;
    }

    /**
     * Created the JSON file to build the client environment
     * on a server and stores it in data/json/.
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
            'password' => password_hash(
                $salt . $client->get('first_user_email'), PASSWORD_BCRYPT
            ),
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
            $sqlDumpUsersRoles
                = 'INSERT INTO `users_roles` SET ' . $listValues . ';';
        }
        $listValues = $this->getListValues($role2Values, $serverTable);
        if ($listValues !== '') {
            $sqlDumpUsersRoles .=
                ' INSERT INTO `users_roles` SET ' . $listValues . ';';
        }

        //clients table database client
        $fieldsClient = array(
            'id' => $client->get('id'),
           // 'models' => $client->getModels(),
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
        if ($listValues !== '') {
            $sqlDumpClients = 'INSERT INTO `clients` SET ' . $listValues . ';';
        }

        //clients_models table DB client
        $fieldsClientsModels = [];
        foreach ($client->getModels() as $model) {
            $fieldsClientsModels[] = [
                'client_id' => $client->get('id'),
                'model_id' => $model->getModelId(),
                'creator' => 'System',
                'created_at' => date('Y-m-d H:i:s'),
            ];
        }

        $sqlDumpClientsModels = '';
        $listValuesModels = $this->getListValues($fieldsClientsModels, $serverTable);
        if (\is_array($listValuesModels)) {
            foreach ($listValuesModels as $listValuesModel) {
                $sqlDumpClientsModels
                    .= 'INSERT INTO `clients_models` SET ' . $listValuesModel . ';';
            }
        } elseif ($listValuesModels !== '') {
            $sqlDumpClientsModels
                = 'INSERT INTO `clients_models` SET ' . $listValuesModels . ';';
        }

        $datas = [
            'server' => $server->get('fqdn'),
            'proxy_alias' => $client->get('proxyAlias'),
            'twoFactorAuthEnforced' => $client->isTwoFactorAuthEnforced(),
            'isBackgroundImportActive' => $client->isBackgroundImportActive(),
            'sql_bootstrap' => $sqlDumpUsers . ' ' .
                $sqlDumpUsersRoles . ' ' .
                $sqlDumpClients . ' ' .
                $sqlDumpClientsModels,
        ];

        $path = $this->config['spool_path_create'];

        return $this->createJsonFile($path, $datas);
    }

    /**
     * Created the JSON file to delete the client environment
     * on a server and stores it in data/json/.
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
            if (is_array($value)) {
                if (!is_array($listValues)) {
                    $listValues = [];
                }
                $listValues[] = $this->getListValues($value, $serverTable);
            }
            if (!is_array($value) && $key !== '' && $value !== null) {
                if ($listValues !== '') {
                    $listValues .= ', ';
                }

                if (is_numeric($value)) {
                    $listValues .= "`$key` = " .
                        $serverTable->getDb()->quote($value, PDO::PARAM_INT);
                } else {
                    $listValues .= "`$key` = " .
                        $serverTable->getDb()->quote($value, PDO::PARAM_STR);
                }
            }
        }

        return $listValues;
    }

    private function createJsonFile($path, array $datas): string
    {
        if (!is_dir($path) && !mkdir($path, 0750, true) && !is_dir($path)) {
            throw new RuntimeException(
                sprintf('Directory "%s" was not created', $path)
            );
        }
        $now = date('YmdHis');
        $filename = $path . $now . '.json';
        file_put_contents($filename, json_encode($datas));

        return $filename;
    }

    private function generateUpdateClientJson(Client $client, array $updateData)
    {
        $clientUpdateSql = '';
        /* Generate the client and user updates. */
        if (isset($updateData['client'])) {
            $clientUpdateSql = sprintf(
                'UPDATE `clients` SET `first_user_firstname` = "%s", `first_user_lastname` = "%s", '
                . '`first_user_email` = "%s" ORDER BY `id` LIMIT 1; ',
                $updateData['client']['firstName'],
                $updateData['client']['lastName'],
                $updateData['client']['email'],
            );

            $resetTwoFactorAuthSqlPart = '';
            if (!empty($updateData['resetTwoFactorAuth'])) {
                $resetTwoFactorAuthSqlPart = ', `is_two_factor_enabled` = 0, `secret_key` = "", '
                    . '`recovery_codes` = NULL ';
            }

            $passwordRestSqlPart = '';
            if ($updateData['client']['oldEmail'] !== $updateData['client']['email']) {
                $passwordRestSqlPart = ', `password` = "' . password_hash(md5((string)time()), PASSWORD_BCRYPT) . '" ';
            }

            $clientUpdateSql .= sprintf(
                'UPDATE `users` SET `firstname` = "%s", `lastname` = "%s", `email` = "%s" ' . $passwordRestSqlPart
                . $resetTwoFactorAuthSqlPart . 'WHERE `id` = 1 OR `email` = "%s"; ',
                $updateData['client']['firstName'],
                $updateData['client']['lastName'],
                $updateData['client']['email'],
                $updateData['client']['oldEmail'],
            );
        } elseif (!empty($updateData['resetTwoFactorAuth'])) {
            $clientUpdateSql = 'UPDATE `users` SET `is_two_factor_enabled` = 0, `secret_key` = "", '
                . '`recovery_codes` = NULL WHERE `id` = 1 OR `email` = "' . $client->get('first_user_email') . '"; ';
        }

        /* Generate the models_clients inserts. */
        if (isset($updateData['modelIdsToAdd'])) {
            $values = [];
            foreach ($updateData['modelIdsToAdd'] as $modelIdToAdd) {
                $values[] = '(' . $modelIdToAdd . ', (SELECT id from clients ORDER BY `id` LIMIT 1), "System", NOW())';
            }
            $clientUpdateSql .= sprintf(
                'INSERT INTO `clients_models` (`model_id`, `client_id`, `creator`, `created_at`) VALUES %s;',
                implode(', ', $values),
            );
        }
        /* Generate the models_clients deletes. */
        if (isset($updateData['modelIdsToRemove'])) {
            $clientUpdateSql .= sprintf(
                'DELETE FROM `clients_models` WHERE `model_id` in (%s); ',
                implode(', ', $updateData['modelIdsToRemove'])
            );
        }

        $serverTable = $this->get('serverTable');
        $server = $serverTable->getEntity($client->get('server_id'));

        $data = [
            'server' => $server->get('fqdn'),
            'proxy_alias' => $client->get('proxyAlias'),
        ];

        if ($clientUpdateSql !== '') {
            $data['sql_update'] = $clientUpdateSql;
        }
        if (isset($updateData['twoFactorAuthEnforced'])) {
            $data['twoFactorAuthEnforced'] = $client->isTwoFactorAuthEnforced();
        }
        if (isset($updateData['isBackgroundImportActive'])) {
            $data['isBackgroundImportActive'] = $client->isBackgroundImportActive();
        }

        $path = $this->config['spool_path_update'];

        $this->createJsonFile($path, $data);
    }
}
