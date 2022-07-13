<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2022  SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
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

    public function get($id)
    {
        return $this->getPreparedJsonResponse($this->assetService->getAssetData($id));
    }

    public function create($data)
    {
        $isBatchData = $this->isBatchData($data);
        $this->validatePostParams($this->postAssetDataInputValidator, $data, $isBatchData);

        $assetsUuids = [];
        $validatedData = $isBatchData
            ? $this->postAssetDataInputValidator->getValidDataSets()
            : [$this->postAssetDataInputValidator->getValidData()];
        foreach ($validatedData as $validatedDataRow) {
            $assetsUuids[] = $this->assetService->create($validatedDataRow)->getUuid();
        }

        return $this->getPreparedJsonResponse([
            'status' => 'ok',
            'id' => implode(', ', $assetsUuids),
        ]);
    }

    public function update($id, $data)
    {
        $this->validatePostParams($this->postAssetDataInputValidator, $data);

        $this->assetService->update($id, $this->postAssetDataInputValidator->getValidData());

        return $this->getPreparedJsonResponse(['status' => 'ok']);
    }

    public function patch($id, $data)
    {
        $this->assetService->patch($id, $data);

        return $this->getPreparedJsonResponse(['status' => 'ok']);
    }

    public function delete($id)
    {
        $this->assetService->delete($id);

        return $this->getPreparedJsonResponse(['status' => 'ok']);
    }

    public function deleteList($data)
    {
        $this->assetService->deleteList($data);

        return $this->getPreparedJsonResponse(['status' => 'ok']);
    }
}
