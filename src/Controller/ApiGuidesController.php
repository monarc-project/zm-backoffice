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
        return $this->getPreparedJsonResponse($this->guideService->getGuideData((int)$id));
    }

    public function create($data)
    {
        $this->validatePostParams($this->guideDataInputValidator, $data);
        $validData = $this->guideDataInputValidator->getValidData();

        return $this->getSuccessfulJsonResponse([
            'id' => $this->guideService->create($validData)->getId(),
        ]);
    }

    public function update($id, $data)
    {
        $this->validatePostParams($this->guideDataInputValidator, $data);
        $this->guideService->update((int)$id, $this->guideDataInputValidator->getValidData());

        return $this->getSuccessfulJsonResponse();
    }

    public function patch($id, $data)
    {
        $this->validatePostParams($this->guideDataInputValidator, $data, partial: true);
        $this->guideService->update((int)$id, $this->guideDataInputValidator->getValidData());

        return $this->getSuccessfulJsonResponse();
    }

    public function delete($id)
    {
        $this->guideService->delete((int)$id);

        return $this->getSuccessfulJsonResponse();
    }

    public function deleteList($data)
    {
        $this->guideService->deleteList($data);

        return $this->getSuccessfulJsonResponse();
    }
}
