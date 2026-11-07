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
        $id = $this->guideItemDataInputValidator->validateIds([$id])[0];

        return $this->getPreparedJsonResponse($this->guideItemService->getEntity($id));
    }

    public function create($data)
    {
        $validData = $this->guideItemDataInputValidator->validate($data);
        $id = $this->guideItemService->create($validData);

        return $this->getSuccessfulJsonResponse(['id' => $id]);
    }

    public function update($id, $data)
    {
        $id = $this->guideItemDataInputValidator->validateIds([$id])[0];
        $validData = $this->guideItemDataInputValidator->validate($data);
        $this->guideItemService->update($id, $validData);

        return $this->getSuccessfulJsonResponse();
    }

    public function patch($id, $data)
    {
        $id = $this->guideItemDataInputValidator->validateIds([$id])[0];
        $validData = $this->guideItemDataInputValidator->validate($data, true);
        $this->guideItemService->update($id, $validData);

        return $this->getSuccessfulJsonResponse();
    }

    public function delete($id)
    {
        $id = $this->guideItemDataInputValidator->validateIds([$id])[0];
        $this->guideItemService->delete($id);

        return $this->getSuccessfulJsonResponse();
    }

    public function deleteList($data)
    {
        $ids = $this->guideItemDataInputValidator->validateIds($data);
        $this->guideItemService->deleteList($ids);

        return $this->getSuccessfulJsonResponse();
    }
}
