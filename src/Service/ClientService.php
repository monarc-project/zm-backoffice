<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2023  SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Service;

use Doctrine\DBAL\ParameterType;
use JsonException;
use Monarc\BackOffice\Entity\Client;
use Monarc\BackOffice\Entity\ClientModel;
use Monarc\BackOffice\Entity\Server;
use Monarc\BackOffice\Table\ClientModelTable;
use Monarc\BackOffice\Table\ClientTable;
use Monarc\BackOffice\Table\ServerTable;
use Monarc\Core\Exception\Exception;
use Monarc\Core\InputFormatter\FormattedInputParams;
use Monarc\Core\Model\Entity\Model;
use Monarc\Core\Model\Entity\User;
use Monarc\Core\Service\ConnectedUserService;
use Monarc\Core\Table\ModelTable;
use RuntimeException;

class ClientService
{
    private ClientTable $clientTable;

    private ServerTable $serverTable;

    private ModelTable $modelTable;

    private ClientModelTable $clientModelTable;

    private User $connectedUser;

    private array $config;

    public function __construct(
        ClientTable $clientTable,
        ServerTable $serverTable,
        ModelTable $modelTable,
        ClientModelTable $clientModelTable,
        ConnectedUserService $connectedUserService,
        array $config
    ) {
        $this->clientTable = $clientTable;
        $this->serverTable = $serverTable;
        $this->modelTable = $modelTable;
        $this->clientModelTable = $clientModelTable;
        $this->connectedUser = $connectedUserService->getConnectedUser();
        $this->config = $config;
    }

    public function getList(FormattedInputParams $formattedInputParams): array
    {
        /** @var Client[] $clients */
        $clients = $this->clientTable->findByParams($formattedInputParams);

        $clientsData = [];
        foreach ($clients as $client) {
            $clientsData[] = $this->getPreparedClientData($client);
        }

        return $clientsData;
    }

    public function getCount(FormattedInputParams $formattedInputParams): int
    {
        return $this->clientTable->countByParams($formattedInputParams);
    }

    public function getClientData(int $id): array
    {
        /** @var Client $client */
        $client = $this->clientTable->findById($id);

        return $this->getPreparedClientData($client);
    }

    public function create(array $data): Client
    {
        /** @var Server $server */
        $server = $this->serverTable->findById((int)$data['serverId']);

        $client = (new Client())
            ->setName($data['name'])
            ->setProxyAlias($data['proxyAlias'])
            ->setFirstUserFirstname($data['firstUserFirstname'])
            ->setFirstUserLastname($data['firstUserLastname'])
            ->setFirstUserEmail($data['firstUserEmail'])
            ->setLogoId($data['logoId'] ?? 0)
            ->setContactEmail($data['contactEmail'] ?? '')
            ->setServer($server)
            ->setCreator($this->connectedUser->getEmail());

        if (!isset($data['twoFactorAuthEnforced'])) {
            $client->setTwoFactorAuthEnforced((bool)$data['twoFactorAuthEnforced']);
        }
        if (!isset($data['isBackgroundImportActive'])) {
            $client->setIsBackgroundImportActive((bool)$data['isBackgroundImportActive']);
        }

        $models = [];
        if (!empty($data['modelId'])) {
            /** @var Model[] $models */
            $models = $this->modelTable->findByIds($data['modelId']);
        }
        foreach ($models as $model) {
            if (!$model->isActive()) {
                throw new Exception(sprintf(
                    'Model ID "%s" is inactive and can\'t be linked',
                    $model->getLabel($this->connectedUser->getLanguage())
                ));
            }
            $clientModel = (new ClientModel())
                ->setClient($client)
                ->setModelId($model->getId())
                ->setCreator($this->connectedUser->getEmail());
            $this->clientModelTable->save($clientModel, false);
        }

        $this->clientTable->save($client);

        $this->createJson($client);

        return $client;
    }

    public function update(int $id, array $data): Client
    {
        /** @var Client $client */
        $client = $this->clientTable->findById($id);

        $client->setName($data['name'])
            ->setUpdater($this->connectedUser->getEmail());

        if (!empty($data['logoId'])) {
            $client->setLogoId($data['logoId']);
        }
        if (!empty($data['contactEmail'])) {
            $client->setContactEmail($data['contactEmail']);
        }

        $updateData = [];
        if ($data['firstUserEmail'] !== $client->getFirstUserEmail()
            || $data['firstUserFirstname'] !== $client->getFirstUserFirstname()
            || $data['firstUserLastname'] !== $client->getFirstUserLastname()
        ) {
            $updateData['client'] = [
                'oldEmail' => $client->getFirstUserEmail(),
                'email' => $data['firstUserEmail'],
                'firstName' => $data['firstUserFirstname'],
                'lastName' => $data['firstUserLastname'],
            ];
            $client->setFirstUserEmail($data['firstUserEmail'])
                ->setFirstUserFirstname($data['firstUserFirstname'])
                ->setFirstUserLastname($data['firstUserLastname']);
        }
        if (isset($data['twoFactorAuthEnforced'])
            && (bool)$data['twoFactorAuthEnforced'] !== $client->isTwoFactorAuthEnforced()
        ) {
            $client->setTwoFactorAuthEnforced((bool)$data['twoFactorAuthEnforced']);
            $updateData['twoFactorAuthEnforced'] = $client->isTwoFactorAuthEnforced();
        }
        if (isset($data['isBackgroundImportActive'])
            && (bool)$data['isBackgroundImportActive'] !== $client->isBackgroundImportActive()
        ) {
            $client->setIsBackgroundImportActive((bool)$data['isBackgroundImportActive']);
            $updateData['isBackgroundImportActive'] = $client->isBackgroundImportActive();
        }

        $linkedModelIds = [];
        foreach ($client->getClientModels() as $clientModel) {
            if (\in_array($clientModel->getModelId(), $data['modelId'], true)) {
                $linkedModelIds[] = $clientModel->getModelId();
            } else {
                $client->removeClientModel($clientModel);
                $this->clientTable->save($client, false);

                $updateData['modelIdsToRemove'][] = $clientModel->getModelId();
            }
        }
        $models = [];
        if (!empty($data['modelId'])) {
            /** @var Model[] $models */
            $models = $this->modelTable->findByIds($data['modelId']);
        }
        foreach ($models as $model) {
            if (!$model->isActive()) {
                throw new Exception(sprintf(
                    'Model ID "%s" is inactive and can\'t be linked',
                    $model->getLabel($this->connectedUser->getLanguage())
                ));
            }
            if (!\in_array($model->getId(), $linkedModelIds, true)) {
                $clientModel = (new ClientModel())
                    ->setClient($client)
                    ->setModelId($model->getId())
                    ->setCreator($this->connectedUser->getEmail());
                $this->clientModelTable->save($clientModel, false);

                $updateData['modelIdsToAdd'][] = $model->getId();
            }
        }

        $this->clientTable->save($client);

        if (!empty($updateData)) {
            $this->generateUpdateClientJson($client, $updateData);
        }

        return $client;
    }

    public function delete($id): void
    {
        /** @var Client $client */
        $client = $this->clientTable->findById($id);

        $this->clientTable->remove($client);

        $this->deleteJson($client);
    }

    public function unlinkModel(int $modelId): void
    {
        foreach ($this->clientModelTable->findByModelId($modelId) as $clientModel) {
            $client = $clientModel->getClient();
            $client->removeClientModel($clientModel);
            $this->clientTable->save($client, false);
            $this->clientModelTable->remove($clientModel);
        }
    }

    /**
     * Creates the JSON file to build the client environment on a server and stores it in data/json/.
     *
     * @throws JsonException|Exception
     */
    private function createJson(Client $client): void
    {
        if (!isset($this->config['spool_path_create'])) {
            throw new Exception('The config option "spool_path_create" is required to generate clients creation.', 412);
        }
        if ($client->getServer()->getFqdn() === '') {
            return;
        }

        $salt = $this->config['monarc']['salt'] ?: '';

        $sqlBootstrap = '';

        /* Generate an instance admin user's insert. */
        $listValues = $this->listFieldsAndValuesAsString([
            'id' => 1,
            'status' => 1,
            'firstname' => $client->getFirstUserFirstname(),
            'lastname' => $client->getFirstUserLastname(),
            'email' => $client->getFirstUserEmail(),
            'language' => 1,
            'password' => password_hash($salt . $client->getFirstUserEmail(), PASSWORD_BCRYPT),
            'creator' => 'System',
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        if ($listValues !== '') {
            $sqlBootstrap .= 'INSERT INTO `users` SET ' . $listValues . '; ';
        }

        /* Generate the users_roles table inserts. */
        $listValues = $this->listFieldsAndValuesAsString([
            'user_id' => 1,
            'role' => 'superadminfo',
            'creator' => 'System',
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        if ($listValues !== '') {
            $sqlBootstrap .= 'INSERT INTO `users_roles` SET ' . $listValues . '; ';
        }
        $listValues = $this->listFieldsAndValuesAsString([
            'user_id' => 1,
            'role' => 'userfo',
            'creator' => 'System',
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        if ($listValues !== '') {
            $sqlBootstrap = ' INSERT INTO `users_roles` SET ' . $listValues . '; ';
        }

        /* Generates the clients table insert. */
        $listValues = $this->listFieldsAndValuesAsString([
            'id' => $client->getId(),
            'logo_id' => $client->getLogoId(),
            'name' => $client->getName(),
            'proxy_alias' => $client->getProxyAlias(),
            'first_user_firstname' => $client->getFirstUserFirstname(),
            'first_user_lastname' => $client->getFirstUserLastname(),
            'first_user_email' => $client->getFirstUserEmail(),
            'creator' => 'System',
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        if ($listValues !== '') {
            $sqlBootstrap = 'INSERT INTO `clients` SET ' . $listValues . '; ';
        }

        /* Generates the clients_models table inserts. */
        foreach ($client->getClientModels() as $clientModel) {
            $listValues = $this->listFieldsAndValuesAsString([
                'client_id' => $client->getId(),
                'model_id' => $clientModel->getModelId(),
                'creator' => 'System',
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            $sqlBootstrap .= 'INSERT INTO `clients_models` SET ' . $listValues . '; ';
        }

        $this->createJsonFileWithDataContent($this->config['spool_path_create'], [
            'server' => $client->getServer()->getFqdn(),
            'proxy_alias' => $client->getProxyAlias(),
            'twoFactorAuthEnforced' => $client->isTwoFactorAuthEnforced(),
            'isBackgroundImportActive' => $client->isBackgroundImportActive(),
            'sql_bootstrap' => rtrim($sqlBootstrap),
        ]);
    }

    /**
     * Created the JSON file to delete the client environment on a server and stores it in data/json/.
     *
     * @throws JsonException|Exception
     */
    private function deleteJson(Client $client): void
    {
        if (!isset($this->config['spool_path_delete'])) {
            throw new Exception('The config option "spool_path_delete" is required to generate clients deletion.', 412);
        }
        if ($client->getServer()->getFqdn() === '') {
            return;
        }

        $this->createJsonFileWithDataContent($this->config['spool_path_delete'], [
            'server' => $client->getServer()->getFqdn(),
            'proxy_alias' => $client->getProxyAlias(),
        ]);
    }

    /**
     * Creates a formatted list of data to insert.
     *
     * @return string The formatted list of data to insert.
     */
    private function listFieldsAndValuesAsString(array $fieldsValues): string
    {
        $listValues = '';
        foreach ($fieldsValues as $key => $value) {
            if ($key !== '' && $value !== null) {
                if ($listValues !== '') {
                    $listValues .= ', ';
                }

                $listValues .= "`$key` = " . $this->clientTable->quote(
                    $value,
                    is_numeric($value) ? ParameterType::INTEGER : ParameterType::STRING
                );
            }
        }

        return $listValues;
    }

    /**
     * @throws JsonException|RuntimeException
     */
    private function createJsonFileWithDataContent($path, array $data): bool
    {
        if (!is_dir($path) && !mkdir($path, 0750, true) && !is_dir($path)) {
            throw new RuntimeException(
                sprintf('Directory "%s" was not created', $path)
            );
        }

        $filename = $path . date('YmdHis') . '.json';

        return (bool)file_put_contents($filename, json_encode($data, JSON_THROW_ON_ERROR));
    }

    private function getPreparedClientData(Client $client): array
    {
        $modelIds = [];
        foreach ($client->getClientModels() as $clientModel) {
            $modelIds[] = $clientModel->getModelId();
        }

        return [
            'id' => $client->getId(),
            'name' => $client->getName(),
            'proxyAlias' => $client->getProxyAlias(),
            'contactEmail' => $client->getContactEmail(),
            'firstUserFirstname' => $client->getFirstUserFirstname(),
            'firstUserLastname' => $client->getFirstUserLastname(),
            'firstUserEmail' => $client->getFirstUserEmail(),
            'twoFactorAuthEnforced' => $client->isTwoFactorAuthEnforced(),
            'isBackgroundImportActive' => $client->isBackgroundImportActive(),
            'logoId' => $client->getLogoId(),
            'serverId' => $client->getServer()->getId(),
            'modelId' => $modelIds,
            'createdAt' => [
                'date' => $client->getCreatedAt()->format('Y-m-d H:i'),
            ],
        ];
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
            $clientUpdateSql .= sprintf(
                'UPDATE `users` SET `firstname` = "%s", `lastname` = "%s", `email` = "%s" '
                . 'WHERE `id` = 1 OR `email` = "%s"; ',
                $updateData['client']['firstName'],
                $updateData['client']['lastName'],
                $updateData['client']['email'],
                $updateData['client']['oldEmail'],
            );
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

        $data = [
            'server' => $client->getServer()->getFqdn(),
            'proxy_alias' => $client->getProxyAlias(),
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

        $this->createJsonFileWithDataContent($path, $data);
    }
}
