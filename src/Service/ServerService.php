<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2022  SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Service;

use Monarc\BackOffice\Entity\Server;
use Monarc\BackOffice\Table\ServerTable;
use Monarc\Core\InputFormatter\FormattedInputParams;
use Monarc\Core\Model\Entity\User;
use Monarc\Core\Service\ConnectedUserService;

class ServerService
{
    private ServerTable $serverTable;

    private User $connectedUser;

    public function __construct(ServerTable $serverTable, ConnectedUserService $connectedUserService)
    {
        $this->serverTable = $serverTable;
        $this->connectedUser = $connectedUserService->getConnectedUser();
    }

    public function getList(FormattedInputParams $formattedInputParams): array
    {
        /** @var Server[] $servers */
        $servers = $this->serverTable->findByParams($formattedInputParams);

        $serversData = [];
        foreach ($servers as $server) {
            $serversData[] = $this->getPreparedServerData($server);
        }

        return $serversData;
    }

    public function getFilteredCount(FormattedInputParams $formattedInputParams): int
    {
        return $this->serverTable->countByParams($formattedInputParams);
    }

    public function getServerData(int $id)
    {
        /** @var Server $server */
        $server = $this->serverTable->findById($id);

        return $this->getPreparedServerData($server);
    }

    public function create(array $data): Server
    {
        $server = (new Server($data))->setCreator($this->connectedUser->getEmail());

        $this->serverTable->save($server);

        return $server;
    }

    public function update(int $id, array $data): Server
    {
        /** @var Server $server */
        $server = $this->serverTable->findById($id);

        $server->setLabel($data['label'])
            ->setIpAddress($data['ipAddress'])
            ->setFqdn($data['fqdn'])
            ->setUpdater($this->connectedUser->getEmail());

        if (isset($data['status'])) {
            $server->setStatus((bool)$data['status']);
        }

        $this->serverTable->save($server);

        return $server;
    }

    public function delete(int $id): void
    {
        /** @var Server $server */
        $server = $this->serverTable->findById($id);

        $this->serverTable->remove($server);
    }

    private function getPreparedServerData(Server $server): array
    {
        return [
            'id' => $server->getId(),
            'label' => $server->getLabel(),
            'ipAddress' => $server->getIpAddress(),
            'status' => $server->isActive(),
            'fqdn' => $server->getFqdn(),
        ];
    }
}
