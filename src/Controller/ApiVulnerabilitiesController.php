<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2022  SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Laminas\Mvc\Controller\AbstractRestfulController;
use Monarc\Core\Controller\Handler\ControllerRequestResponseHandlerTrait;
use Monarc\Core\InputFormatter\Vulnerability\GetVulnerabilitiesInputFormatter;
use Monarc\Core\Service\VulnerabilityService;
use Monarc\Core\Validator\InputValidator\Vulnerability\PostVulnerabilityDataInputValidator;

class ApiVulnerabilitiesController extends AbstractRestfulController
{
    use ControllerRequestResponseHandlerTrait;

    private GetVulnerabilitiesInputFormatter $getVulnerabilitiesInputFormatter;

    private PostVulnerabilityDataInputValidator $postVulnerabilityDataInputValidator;

    private VulnerabilityService $vulnerabilityService;

    public function __construct(
        GetVulnerabilitiesInputFormatter $getVulnerabilitiesInputFormatter,
        PostVulnerabilityDataInputValidator $postVulnerabilityDataInputValidator,
        VulnerabilityService $vulnerabilityService
    ) {
        $this->getVulnerabilitiesInputFormatter = $getVulnerabilitiesInputFormatter;
        $this->postVulnerabilityDataInputValidator = $postVulnerabilityDataInputValidator;
        $this->vulnerabilityService = $vulnerabilityService;
    }

    public function getList()
    {
        $formattedParams = $this->getFormattedInputParams($this->getVulnerabilitiesInputFormatter);

        return $this->getPreparedJsonResponse([
            'count' => $this->vulnerabilityService->getCount($formattedParams),
            'vulnerabilities' => $this->vulnerabilityService->getList($formattedParams),
        ]);
    }

    public function get($id)
    {
        return $this->getPreparedJsonResponse($this->vulnerabilityService->getVulnerabilityData($id));
    }

    public function create($data)
    {
        if (!$this->isBatchDataRequest($data)) {
            $data = [$data];
        }
        foreach ($data as $requestParams) {
            $this->validatePostParams($this->postVulnerabilityDataInputValidator, $requestParams);
        }
        $vulnerability = null;
        foreach ($data as $requestParams) {
            $vulnerability = $this->vulnerabilityService->create($requestParams);
        }

        return $this->getPreparedJsonResponse([
            'status' => 'ok',
            'id' => $vulnerability ? $vulnerability->getUuid() : '',
        ]);
    }

    public function update($id, $data)
    {
        $this->validatePostParams($this->postVulnerabilityDataInputValidator, $data);

        $this->vulnerabilityService->update($id, $data);

        return $this->getPreparedJsonResponse(['status' => 'ok']);
    }

    public function patch($id, $data)
    {
        $this->vulnerabilityService->patch($id, $data);

        return $this->getPreparedJsonResponse(['status' => 'ok']);
    }

    public function delete($id)
    {
        $this->vulnerabilityService->delete($id);

        return $this->getPreparedJsonResponse(['status' => 'ok']);
    }

    public function deleteList($data)
    {
        $this->vulnerabilityService->deleteList($data);

        return $this->getPreparedJsonResponse(['status' => 'ok']);
    }
}
