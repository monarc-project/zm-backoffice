<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2026 Luxembourg House of Cybersecurity LHC.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Monarc\Core\Controller\Handler\AbstractRestfulControllerRequestHandler;
use Monarc\Core\Controller\Handler\ControllerRequestResponseHandlerTrait;
use Monarc\Core\InputFormatter\Guide\GetGuidesInputFormatter;
use Monarc\Core\Service\GuideService;
use Monarc\Core\Validator\InputValidator\Guide\GuideDataInputValidator;

class ApiGuidesController extends AbstractRestfulControllerRequestHandler
{
    use ControllerRequestResponseHandlerTrait;

    public function __construct(
        private GetGuidesInputFormatter $getGuidesInputFormatter,
        private GuideDataInputValidator $guideDataInputValidator,
        private GuideService $guideService
    ) {
    }

    public function getList()
    {
        $formattedParams = $this->getFormattedInputParams($this->getGuidesInputFormatter);

        return $this->getPreparedJsonResponse([
            'count' => $this->guideService->getCount($formattedParams),
            'guides' => $this->guideService->getList($formattedParams),
        ]);
    }

    public function get($id)
    {
        $id = $this->guideDataInputValidator->validateIds([$id])[0];

        return $this->getPreparedJsonResponse($this->guideService->getGuideData($id));
    }

    public function create($data)
    {
        $validData = $this->guideDataInputValidator->validate($data);
        $id = $this->guideService->create($validData);

        return $this->getSuccessfulJsonResponse(['id' => $id]);
    }

    public function update($id, $data)
    {
        $id = $this->guideDataInputValidator->validateIds([$id])[0];
        $validData = $this->guideDataInputValidator->validate($data);
        $this->guideService->update($id, $validData);

        return $this->getSuccessfulJsonResponse();
    }

    public function patch($id, $data)
    {
        $id = $this->guideDataInputValidator->validateIds([$id])[0];
        $validData = $this->guideDataInputValidator->validate($data, true);
        $this->guideService->update($id, $validData);

        return $this->getSuccessfulJsonResponse();
    }

    public function delete($id)
    {
        $id = $this->guideDataInputValidator->validateIds([$id])[0];
        $this->guideService->delete($id);

        return $this->getSuccessfulJsonResponse();
    }

    public function deleteList($data)
    {
        $ids = $this->guideDataInputValidator->validateIds($data);
        $this->guideService->deleteList($ids);

        return $this->getSuccessfulJsonResponse();
    }
}
