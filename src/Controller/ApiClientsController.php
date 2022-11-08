<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2022  SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Laminas\Mvc\Controller\AbstractRestfulController;
use Monarc\BackOffice\Service\ClientService;
use Monarc\BackOffice\Validator\InputValidator\Client\PostClientInputValidator;
use Monarc\Core\Controller\Handler\ControllerRequestResponseHandlerTrait;
use Monarc\Core\InputFormatter\Client\GetClientsInputFormatter;

class ApiClientsController extends AbstractRestfulController
{
    use ControllerRequestResponseHandlerTrait;

    private ClientService $clientService;

    private GetClientsInputFormatter $getClientsInputFormatter;

    private PostClientInputValidator $clientInputValidator;

    public function __construct(
        ClientService $clientService,
        GetClientsInputFormatter $getClientsInputFormatter,
        PostClientInputValidator $clientInputValidator
    ) {
        $this->clientService = $clientService;
        $this->getClientsInputFormatter = $getClientsInputFormatter;
        $this->clientInputValidator = $clientInputValidator;
    }

    public function getList()
    {
        $formattedInputParams = $this->getFormattedInputParams($this->getClientsInputFormatter);

        return $this->getPreparedJsonResponse([
            'count' => $this->clientService->getCount($formattedInputParams),
            'clients' => $this->clientService->getList($formattedInputParams),
        ]);
    }

    public function get($id)
    {
        return $this->getPreparedJsonResponse($this->clientService->getClientData((int)$id));
    }

    public function create($data)
    {
        $this->validatePostParams($this->clientInputValidator, $data);

        $client = $this->clientService->create($this->clientInputValidator->getValidData());

        return $this->getSuccessfulJsonResponse(['id' => $client->getId()]);
    }

    public function update($id, $data)
    {
        $this->clientInputValidator->setCurrentClientId((int)$id);
        $this->validatePostParams($this->clientInputValidator, $data);

        $this->clientService->update((int)$id, $data);

        return $this->getSuccessfulJsonResponse();
    }

    public function delete($id)
    {
        $this->clientService->delete((int)$id);

        return $this->getSuccessfulJsonResponse();
    }

    public function deleteList($data)
    {
        foreach ($data as $id) {
            $this->clientService->delete((int)$id);
        }

        return $this->getSuccessfulJsonResponse();
    }
}
