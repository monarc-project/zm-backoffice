<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2024 Luxembourg House of Cybersecurity LHC.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Laminas\Mvc\Controller\AbstractRestfulController;
use Monarc\Core\Controller\Handler\ControllerRequestResponseHandlerTrait;
use Monarc\Core\Service\MeasureLinkService;
use Monarc\Core\Validator\InputValidator\MeasureLink\PostMeasureLinkDataInputValidator;

class ApiMeasuresLinksController extends AbstractRestfulController
{
    use ControllerRequestResponseHandlerTrait;

    public function __construct(
        private MeasureLinkService $measureLinkService,
        private PostMeasureLinkDataInputValidator $postMeasureLinkDataInputValidator
    ) {
    }

    public function getList()
    {
        /* Fetches all the measures links. */
        $measuresLinksData = $this->measureLinkService->getList();

        return $this->getPreparedJsonResponse([
            'count' => \count($measuresLinksData),
            'measuresLinks' => $measuresLinksData,
        ]);
    }

    public function create($data)
    {
        $isBatchData = $this->isBatchData($data);
        $this->validatePostParams($this->postMeasureLinkDataInputValidator, $data, $isBatchData);

        if ($this->isBatchData($data)) {
            $this->measureLinkService->createList($this->postMeasureLinkDataInputValidator->getValidDataSets());
        } else {
            $this->measureLinkService->create($this->postMeasureLinkDataInputValidator->getValidData());
        }

        return $this->getSuccessfulJsonResponse();
    }

    public function deleteList($data)
    {
        $masterMeasureUuid = $this->params()->fromQuery('masterMeasureUuid');
        $linkedMeasureUuid = $this->params()->fromQuery('linkedMeasureUuid');
        $this->validatePostParams(
            $this->postMeasureLinkDataInputValidator,
            compact('masterMeasureUuid', 'linkedMeasureUuid')
        );

        $this->measureLinkService->delete($masterMeasureUuid, $linkedMeasureUuid);

        return $this->getSuccessfulJsonResponse();
    }
}
