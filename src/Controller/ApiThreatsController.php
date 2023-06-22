<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2023 Luxembourg House of Cybersecurity LHC.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Laminas\Mvc\Controller\AbstractRestfulController;
use Monarc\BackOffice\Validator\InputValidator\Threat\PostThreatDataInputValidator;
use Monarc\Core\Controller\Handler\ControllerRequestResponseHandlerTrait;
use Monarc\Core\InputFormatter\Threat\GetThreatsInputFormatter;
use Monarc\Core\Service\ThreatService;

class ApiThreatsController extends AbstractRestfulController
{
    use ControllerRequestResponseHandlerTrait;

    private GetThreatsInputFormatter $getThreatsInputFormatter;

    private PostThreatDataInputValidator $postThreatDataInputValidator;

    private ThreatService $threatService;

    public function __construct(
        GetThreatsInputFormatter $getThreatsInputFormatter,
        PostThreatDataInputValidator $postThreatDataInputValidator,
        ThreatService $threatService
    ) {
        $this->getThreatsInputFormatter = $getThreatsInputFormatter;
        $this->postThreatDataInputValidator = $postThreatDataInputValidator;
        $this->threatService = $threatService;
    }

    public function getList()
    {
        $formattedParams = $this->getFormattedInputParams($this->getThreatsInputFormatter);

        return $this->getPreparedJsonResponse([
            'count' => $this->threatService->getCount($formattedParams),
            'threats' => $this->threatService->getList($formattedParams),
        ]);
    }

    /**
     * @param string $id
     */
    public function get($id)
    {
        return $this->getPreparedJsonResponse($this->threatService->getThreatData($id));
    }

    /**
     * @param array $data
     */
    public function create($data)
    {
        $isBatchData = $this->isBatchData($data);
        $this->validatePostParams($this->postThreatDataInputValidator, $data, $isBatchData);

        $threatsUuids = [];
        $validatedData = $isBatchData
            ? $this->postThreatDataInputValidator->getValidDataSets()
            : [$this->postThreatDataInputValidator->getValidData()];
        foreach ($validatedData as $validatedDataSet) {
            $threatsUuids[] = $this->threatService->create($validatedDataSet)->getUuid();
        }

        return $this->getSuccessfulJsonResponse([
            'id' => \count($threatsUuids) === 1 ? current($threatsUuids) : $threatsUuids,
        ]);
    }

    /**
     * @param string $id
     * @param array $data
     */
    public function update($id, $data)
    {
        $this->validatePostParams($this->postThreatDataInputValidator, $data);

        $this->threatService->update($id, $this->postThreatDataInputValidator->getValidData());

        return $this->getSuccessfulJsonResponse();
    }

    /**
     * @param string $id
     * @param array $data
     */
    public function patch($id, $data)
    {
        $this->threatService->patch($id, $data);

        return $this->getSuccessfulJsonResponse();
    }

    /**
     * @param string $id
     */
    public function delete($id)
    {
        $this->threatService->delete($id);

        return $this->getSuccessfulJsonResponse();
    }

    /**
     * @param array $data
     */
    public function deleteList($data)
    {
        $this->threatService->deleteList($data);

        return $this->getSuccessfulJsonResponse();
    }
}
