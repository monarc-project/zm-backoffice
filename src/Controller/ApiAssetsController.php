<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2023 Luxembourg House of Cybersecurity LHC.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Laminas\Mvc\Controller\AbstractRestfulController;
use Monarc\BackOffice\Validator\InputValidator\Asset\PostAssetDataInputValidator;
use Monarc\Core\Controller\Handler\ControllerRequestResponseHandlerTrait;
use Monarc\Core\InputFormatter\Asset\GetAssetsInputFormatter;
use Monarc\Core\Service\AssetService;

class ApiAssetsController extends AbstractRestfulController
{
    use ControllerRequestResponseHandlerTrait;

    private GetAssetsInputFormatter $getAssetsInputFormatter;

    private PostAssetDataInputValidator $postAssetDataInputValidator;

    private AssetService $assetService;

    public function __construct(
        GetAssetsInputFormatter $getAssetsInputFormatter,
        PostAssetDataInputValidator $postAssetDataInputValidator,
        AssetService $assetService
    ) {
        $this->getAssetsInputFormatter = $getAssetsInputFormatter;
        $this->postAssetDataInputValidator = $postAssetDataInputValidator;
        $this->assetService = $assetService;
    }

    public function getList()
    {
        $formattedParams = $this->getFormattedInputParams($this->getAssetsInputFormatter);

        return $this->getPreparedJsonResponse([
            'count' => $this->assetService->getCount($formattedParams),
            'assets' => $this->assetService->getList($formattedParams),
        ]);
    }

    /**
     * @param string $id
     */
    public function get($id)
    {
        return $this->getPreparedJsonResponse($this->assetService->getAssetData($id));
    }

    /**
     * @param array $data
     */
    public function create($data)
    {
        $isBatchData = $this->isBatchData($data);
        $this->validatePostParams($this->postAssetDataInputValidator, $data, $isBatchData);

        if ($isBatchData) {
            return $this->getSuccessfulJsonResponse([
                'id' => $this->assetService->createList($this->postAssetDataInputValidator->getValidDataSets()),
            ]);
        }

        return $this->getSuccessfulJsonResponse([
            'id' => $this->assetService->create($this->postAssetDataInputValidator->getValidData())->getUuid(),
        ]);
    }

    /**
     * @param string $id
     * @param array $data
     */
    public function update($id, $data)
    {
        $this->validatePostParams($this->postAssetDataInputValidator->setExcludeFilter(['uuid' => $id]), $data);

        $this->assetService->update($id, $this->postAssetDataInputValidator->getValidData());

        return $this->getSuccessfulJsonResponse();
    }

    /**
     * @param string $id
     * @param array $data
     */
    public function patch($id, $data)
    {
        $this->assetService->patch($id, $data);

        return $this->getSuccessfulJsonResponse();
    }

    /**
     * @param string $id
     */
    public function delete($id)
    {
        $this->assetService->delete($id);

        return $this->getSuccessfulJsonResponse();
    }

    /**
     * @param array $data
     */
    public function deleteList($data)
    {
        $this->assetService->deleteList($data);

        return $this->getSuccessfulJsonResponse();
    }
}
