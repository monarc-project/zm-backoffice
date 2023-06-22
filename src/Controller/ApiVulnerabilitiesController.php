<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2023 Luxembourg House of Cybersecurity LHC.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Laminas\Mvc\Controller\AbstractRestfulController;
use Monarc\BackOffice\Validator\InputValidator\Vulnerability\PostVulnerabilityDataInputValidator;
use Monarc\Core\Controller\Handler\ControllerRequestResponseHandlerTrait;
use Monarc\Core\InputFormatter\Vulnerability\GetVulnerabilitiesInputFormatter;
use Monarc\Core\Service\VulnerabilityService;

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

    /**
     * @param string $id
     */
    public function get($id)
    {
        return $this->getPreparedJsonResponse($this->vulnerabilityService->getVulnerabilityData($id));
    }

    /**
     * @param array $data
     */
    public function create($data)
    {
        $isBatchData = $this->isBatchData($data);
        $this->validatePostParams($this->postVulnerabilityDataInputValidator, $data, $isBatchData);

        $vulnerabilitiesUuids = [];
        $validatedData = $isBatchData
            ? $this->postVulnerabilityDataInputValidator->getValidDataSets()
            : [$this->postVulnerabilityDataInputValidator->getValidData()];
        foreach ($validatedData as $validatedDataRow) {
            $vulnerabilitiesUuids[] = $this->vulnerabilityService->create($validatedDataRow)->getUuid();
        }

        return $this->getSuccessfulJsonResponse([
            'id' => \count($vulnerabilitiesUuids) === 1 ? current($vulnerabilitiesUuids) : $vulnerabilitiesUuids,
        ]);
    }

    /**
     * @param string $id
     * @param array $data
     */
    public function update($id, $data)
    {
        $this->validatePostParams($this->postVulnerabilityDataInputValidator, $data);

        $this->vulnerabilityService->update($id, $this->postVulnerabilityDataInputValidator->getValidData());

        return $this->getSuccessfulJsonResponse();
    }

    /**
     * @param string $id
     * @param array $data
     */
    public function patch($id, $data)
    {
        $this->vulnerabilityService->patch($id, $data);

        return $this->getSuccessfulJsonResponse();
    }

    /**
     * @param string $id
     */
    public function delete($id)
    {
        $this->vulnerabilityService->delete($id);

        return $this->getSuccessfulJsonResponse();
    }

    public function deleteList($data)
    {
        $this->vulnerabilityService->deleteList($data);

        return $this->getSuccessfulJsonResponse();
    }
}
