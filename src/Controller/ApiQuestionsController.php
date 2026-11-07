<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2026 Luxembourg House of Cybersecurity LHC.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Monarc\Core\Controller\Handler\AbstractRestfulControllerRequestHandler;
use Monarc\Core\Controller\Handler\ControllerRequestResponseHandlerTrait;
use Monarc\Core\InputFormatter\Question\GetQuestionsInputFormatter;
use Monarc\Core\Service\QuestionService;
use Monarc\Core\Validator\InputValidator\Question\QuestionDataInputValidator;

class ApiQuestionsController extends AbstractRestfulControllerRequestHandler
{
    use ControllerRequestResponseHandlerTrait;

    public function __construct(
        private GetQuestionsInputFormatter $getQuestionsInputFormatter,
        private QuestionDataInputValidator $questionDataInputValidator,
        private QuestionService $questionService
    ) {
    }

    public function getList()
    {
        $formattedParams = $this->getFormattedInputParams($this->getQuestionsInputFormatter);

        return $this->getPreparedJsonResponse([
            'count' => $this->questionService->getCount($formattedParams),
            'questions' => $this->questionService->getList($formattedParams),
        ]);
    }

    public function get($id)
    {
        $id = $this->questionDataInputValidator->validateIds([$id])[0];

        return $this->getPreparedJsonResponse($this->questionService->getEntity($id));
    }

    public function create($data)
    {
        $validData = $this->questionDataInputValidator->validate($data);
        $id = $this->questionService->create($validData);

        return $this->getSuccessfulJsonResponse(['id' => $id]);
    }

    public function update($id, $data)
    {
        $id = $this->questionDataInputValidator->validateIds([$id])[0];
        $validData = $this->questionDataInputValidator->validate($data);
        $this->questionService->update($id, $validData);

        return $this->getSuccessfulJsonResponse();
    }

    public function patch($id, $data)
    {
        $id = $this->questionDataInputValidator->validateIds([$id])[0];
        $validData = $this->questionDataInputValidator->validate($data, true);
        $this->questionService->update($id, $validData);

        return $this->getSuccessfulJsonResponse();
    }

    public function delete($id)
    {
        $id = $this->questionDataInputValidator->validateIds([$id])[0];
        $this->questionService->delete($id);

        return $this->getSuccessfulJsonResponse();
    }

    public function deleteList($data)
    {
        $ids = $this->questionDataInputValidator->validateIds($data);
        $this->questionService->deleteList($ids);

        return $this->getSuccessfulJsonResponse();
    }
}
