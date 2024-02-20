<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2023 Luxembourg House of Cybersecurity LHC.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Monarc\Core\Controller\Handler\AbstractRestfulControllerRequestHandler;
use Monarc\Core\Controller\Handler\ControllerRequestResponseHandlerTrait;
use Monarc\Core\InputFormatter\ObjectCategory\ObjectCategoriesInputFormatter;
use Monarc\Core\Service\ObjectCategoryService;
use Monarc\Core\Validator\InputValidator\ObjectCategory\PostObjectCategoryDataInputValidator;

class ApiObjectsCategoriesController extends AbstractRestfulControllerRequestHandler
{
    use ControllerRequestResponseHandlerTrait;

    private ObjectCategoryService $objectCategoryService;

    private ObjectCategoriesInputFormatter $objectCategoriesInputFormatter;

    private PostObjectCategoryDataInputValidator $postObjectCategoryDataInputValidator;

    public function __construct(
        ObjectCategoryService $objectCategoryService,
        ObjectCategoriesInputFormatter $objectCategoriesInputFormatter,
        PostObjectCategoryDataInputValidator $postObjectCategoryDataInputValidator
    ) {
        $this->objectCategoryService = $objectCategoryService;
        $this->objectCategoriesInputFormatter = $objectCategoriesInputFormatter;
        $this->postObjectCategoryDataInputValidator = $postObjectCategoryDataInputValidator;
    }

    public function getList()
    {
        $formattedParams = $this->getFormattedInputParams($this->objectCategoriesInputFormatter);
        $this->objectCategoriesInputFormatter->prepareCategoryFilter();

        return $this->getPreparedJsonResponse([
            'count' => $this->objectCategoryService->getCount(),
            'categories' => $this->objectCategoryService->getList($formattedParams),
        ]);
    }

    public function get($id)
    {
        return $this->getPreparedJsonResponse($this->objectCategoryService->getObjectCategoryData((int)$id));
    }

    /**
     * @param array $data
     */
    public function create($data)
    {
        $this->validatePostParams($this->postObjectCategoryDataInputValidator, $data);

        $objectCategory = $this->objectCategoryService->create($data);

        return $this->getSuccessfulJsonResponse([
            'categ' => [
                'id' => $objectCategory->getId(),
            ],
        ]);
    }

    /**
     * @param array $data
     */
    public function update($id, $data)
    {
        $this->validatePostParams($this->postObjectCategoryDataInputValidator, $data);

        $this->objectCategoryService->update((int)$id, $data);

        return $this->getSuccessfulJsonResponse();
    }

    public function delete($id)
    {
        $this->objectCategoryService->delete((int)$id);

        return $this->getSuccessfulJsonResponse();
    }
}
