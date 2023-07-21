<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2023 Luxembourg House of Cybersecurity LHC.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Laminas\Mvc\Controller\AbstractRestfulController;
use Monarc\Core\Controller\Handler\ControllerRequestResponseHandlerTrait;
use Monarc\Core\InputFormatter\Amv\GetAmvsInputFormatter;
use Monarc\Core\Service\AmvService;
use Monarc\Core\Validator\InputValidator\Amv\PostAmvDataInputValidator;

class ApiAmvsController extends AbstractRestfulController
{
    use ControllerRequestResponseHandlerTrait;

    private GetAmvsInputFormatter $getAmvsInputFormatter;

    private PostAmvDataInputValidator $postAmvDataInputValidator;

    private AmvService $amvService;

    public function __construct(
        GetAmvsInputFormatter $getAmvsInputFormatter,
        PostAmvDataInputValidator $postAmvDataInputValidator,
        AmvService $amvService
    ) {
        $this->getAmvsInputFormatter = $getAmvsInputFormatter;
        $this->postAmvDataInputValidator = $postAmvDataInputValidator;
        $this->amvService = $amvService;
    }

    public function getList()
    {
        $formattedParams = $this->getFormattedInputParams($this->getAmvsInputFormatter);

        return $this->getPreparedJsonResponse([
            'count' => $this->amvService->getCount($formattedParams),
            'amvs' => $this->amvService->getList($formattedParams)
        ]);
    }

    /**
     * @param string $id
     */
    public function get($id)
    {
        return $this->getPreparedJsonResponse($this->amvService->getAmvData($id));
    }

    /**
     * @param array $data
     */
    public function create($data)
    {
        if ($this->isBatchData($data)) {
            return $this->getSuccessfulJsonResponse([
                'id' => current($this->amvService->createAmvItems($data))
            ]);
        }

        $this->validatePostParams($this->postAmvDataInputValidator, $data);

        return $this->getSuccessfulJsonResponse([
            'id' => $this->amvService->create($data),
        ]);
    }

    /**
     * @param string $id
     * @param array $data
     */
    public function update($id, $data)
    {
        $this->validatePostParams($this->postAmvDataInputValidator, $data);

        $this->amvService->update($id, $data);

        return $this->getSuccessfulJsonResponse();
    }

    public function patch($id, $data)
    {
        $this->amvService->patch($id, $data);

        return $this->getSuccessfulJsonResponse();
    }

    public function patchList($data)
    {
        $this->amvService->createLinkedAmvs($data['fromReferential'], $data['toReferential']);

        return $this->getSuccessfulJsonResponse();
    }

    public function delete($id)
    {
        $this->amvService->delete($id);

        return $this->getSuccessfulJsonResponse();
    }

    public function deleteList($data)
    {
        $this->amvService->deleteList($data);

        return $this->getSuccessfulJsonResponse();
    }
}
