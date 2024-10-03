<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2024 Luxembourg House of Cybersecurity LHC.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Laminas\Mvc\Controller\AbstractRestfulController;
use Monarc\Core\Controller\Handler\ControllerRequestResponseHandlerTrait;
use Monarc\Core\InputFormatter\SoaCategory\GetSoaCategoriesInputFormatter;
use Monarc\Core\Service\SoaCategoryService;
use Monarc\Core\Validator\InputValidator\SoaCategory\PostSoaCategoryDataInputValidator;

class ApiSoaCategoryController extends AbstractRestfulController
{
    use ControllerRequestResponseHandlerTrait;

    public function __construct(
        private SoaCategoryService $soaCategoryService,
        private GetSoaCategoriesInputFormatter $getSoaCategoriesInputFormatter,
        private PostSoaCategoryDataInputValidator $postSoaCategoryDataInputValidator
    ) {
    }

    public function getList()
    {
        $formatterParams = $this->getFormattedInputParams($this->getSoaCategoriesInputFormatter);

        return $this->getPreparedJsonResponse([
            'categories' => $this->soaCategoryService->getList($formatterParams),
        ]);
    }

    public function get($id)
    {
        return $this->getPreparedJsonResponse($this->soaCategoryService->getSoaCategoryData((int)$id));
    }

    /**
     * @param array $data
     */
    public function create($data)
    {
        $this->validatePostParams($this->postSoaCategoryDataInputValidator, $data);

        return $this->getSuccessfulJsonResponse([
            'id' => $this->soaCategoryService->create(
                $this->postSoaCategoryDataInputValidator->getValidData()
            )->getId(),
        ]);
    }

    /**
     * @param array $data
     */
    public function update($id, $data)
    {
        $this->validatePostParams($this->postSoaCategoryDataInputValidator, $data);

        $this->soaCategoryService->update((int)$id, $this->postSoaCategoryDataInputValidator->getValidData());

        return $this->getSuccessfulJsonResponse();
    }

    public function delete($id)
    {
        $this->soaCategoryService->delete((int)$id);

        return $this->getSuccessfulJsonResponse();
    }
}
