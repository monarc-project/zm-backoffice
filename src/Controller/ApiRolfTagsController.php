<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2024 Luxembourg House of Cybersecurity LHC.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Laminas\Mvc\Controller\AbstractRestfulController;
use Monarc\Core\Controller\Handler\ControllerRequestResponseHandlerTrait;
use Monarc\Core\InputFormatter\RolfTag\GetRolfTagsInputFormatter;
use Monarc\Core\Service\RolfTagService;
use Monarc\Core\Validator\InputValidator\RolfTag\PostRolfTagDataInputValidator;

class ApiRolfTagsController extends AbstractRestfulController
{
    use ControllerRequestResponseHandlerTrait;

    public function __construct(
        private RolfTagService $rolfTagService,
        private GetRolfTagsInputFormatter $getRolfTagsInputFormatter,
        private PostRolfTagDataInputValidator $postRolfTagDataInputValidator
    ) {
    }

    public function getList()
    {
        $formattedParams = $this->getFormattedInputParams($this->getRolfTagsInputFormatter);

        return $this->getPreparedJsonResponse([
            'count' => $this->rolfTagService->getCount($formattedParams),
            'tags' => $this->rolfTagService->getList($formattedParams),
        ]);
    }

    public function get($id)
    {
        return $this->getPreparedJsonResponse($this->rolfTagService->getRolfTagData((int)$id));
    }

    public function create($data)
    {
        $isBatchData = $this->isBatchData($data);
        $this->validatePostParams($this->postRolfTagDataInputValidator, $data, $isBatchData);

        if ($this->isBatchData($data)) {
            return $this->getSuccessfulJsonResponse([
                'id' => $this->rolfTagService->createList($this->postRolfTagDataInputValidator->getValidDataSets()),
            ]);
        }

        return $this->getSuccessfulJsonResponse([
            'id' => $this->rolfTagService->create($this->postRolfTagDataInputValidator->getValidData())->getId(),
        ]);
    }

    /**
     * @param array $data
     */
    public function update($id, $data)
    {
        $this->validatePostParams($this->postRolfTagDataInputValidator->setExcludeFilter(['id' => (int)$id]), $data);

        $this->rolfTagService->update((int)$id, $this->postRolfTagDataInputValidator->getValidData());

        return $this->getSuccessfulJsonResponse();
    }

    public function delete($id)
    {
        $this->rolfTagService->delete((int)$id);

        return $this->getSuccessfulJsonResponse();
    }

    public function deleteList($data)
    {
        $this->rolfTagService->deleteList($data);

        return $this->getSuccessfulJsonResponse();
    }
}
