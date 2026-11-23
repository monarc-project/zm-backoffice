<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2026 Luxembourg House of Cybersecurity LHC.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Monarc\Core\Controller\Handler\AbstractRestfulControllerRequestHandler;
use Monarc\Core\Controller\Handler\ControllerRequestResponseHandlerTrait;
use Monarc\Core\InputFormatter\Guide\GetGuideItemsInputFormatter;
use Monarc\Core\Service\GuideItemService;
use Monarc\Core\Validator\InputValidator\Guide\GuideItemDataInputValidator;

class ApiGuidesItemsController extends AbstractRestfulControllerRequestHandler
{
    use ControllerRequestResponseHandlerTrait;

    public function __construct(
        private GetGuideItemsInputFormatter $getGuideItemsInputFormatter,
        private GuideItemDataInputValidator $guideItemDataInputValidator,
        private GuideItemService $guideItemService
    ) {
    }

    public function getList()
    {
        $formattedParams = $this->getFormattedInputParams($this->getGuideItemsInputFormatter);

        return $this->getPreparedJsonResponse([
            'count' => $this->guideItemService->getCount($formattedParams),
            'items' => $this->guideItemService->getList($formattedParams),
        ]);
    }

    public function get($id)
    {
        return $this->getPreparedJsonResponse($this->guideItemService->getGuideItemData((int)$id));
    }

    public function create($data)
    {
        $this->validatePostParams($this->guideItemDataInputValidator, $data);
        $validData = $this->guideItemDataInputValidator->getValidData();

        return $this->getSuccessfulJsonResponse([
            'id' => $this->guideItemService->create($validData)->getId(),
        ]);
    }

    public function update($id, $data)
    {
        $this->validatePostParams($this->guideItemDataInputValidator, $data);
        $this->guideItemService->update((int)$id, $this->guideItemDataInputValidator->getValidData());

        return $this->getSuccessfulJsonResponse();
    }

    public function patch($id, $data)
    {
        $this->validatePostParams($this->guideItemDataInputValidator, $data, partial: true);
        $this->guideItemService->update((int)$id, $this->guideItemDataInputValidator->getValidData());

        return $this->getSuccessfulJsonResponse();
    }

    public function delete($id)
    {
        $this->guideItemService->delete((int)$id);

        return $this->getSuccessfulJsonResponse();
    }

    public function deleteList($data)
    {
        $this->guideItemService->deleteList($data);

        return $this->getSuccessfulJsonResponse();
    }
}
