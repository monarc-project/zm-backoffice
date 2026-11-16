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
        return $this->getPreparedJsonResponse($this->questionService->getQuestionData((int)$id));
    }

    public function create($data)
    {
        $this->validatePostParams($this->questionDataInputValidator, $data);

        return $this->getSuccessfulJsonResponse([
            'id' => $this->questionService->create($this->questionDataInputValidator->getValidData())->getId(),
        ]);
    }

    public function update($id, $data)
    {
        $this->validatePostParams($this->questionDataInputValidator, $data);
        $this->questionService->update((int)$id, $this->questionDataInputValidator->getValidData());

        return $this->getSuccessfulJsonResponse();
    }

    public function patch($id, $data)
    {
        $this->validatePostParams($this->questionDataInputValidator, $data, partial: true);
        $this->questionService->update((int)$id, $this->questionDataInputValidator->getValidData());

        return $this->getSuccessfulJsonResponse();
    }

    public function delete($id)
    {
        $this->questionService->delete((int)$id);

        return $this->getSuccessfulJsonResponse();
    }

    public function deleteList($data)
    {
        $this->questionService->deleteList($data);

        return $this->getSuccessfulJsonResponse();
    }
}
