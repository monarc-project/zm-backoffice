<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2026 Luxembourg House of Cybersecurity LHC.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Monarc\Core\Controller\Handler\AbstractRestfulControllerRequestHandler;
use Monarc\Core\Controller\Handler\ControllerRequestResponseHandlerTrait;
use Monarc\Core\InputFormatter\Question\GetQuestionChoicesInputFormatter;
use Monarc\Core\Service\QuestionChoiceService;
use Monarc\Core\Validator\InputValidator\Question\QuestionChoiceDataInputValidator;

class ApiQuestionsChoicesController extends AbstractRestfulControllerRequestHandler
{
    use ControllerRequestResponseHandlerTrait;

    public function __construct(
        private GetQuestionChoicesInputFormatter $getQuestionChoicesInputFormatter,
        private QuestionChoiceDataInputValidator $questionChoiceDataInputValidator,
        private QuestionChoiceService $questionChoiceService
    ) {
    }

    public function getList()
    {
        $formattedParams = $this->getFormattedInputParams($this->getQuestionChoicesInputFormatter);

        return $this->getPreparedJsonResponse([
            'count' => $this->questionChoiceService->getCount($formattedParams),
            'choices' => $this->questionChoiceService->getList($formattedParams),
        ]);
    }

    public function get($id)
    {
        return $this->getPreparedJsonResponse($this->questionChoiceService->getQuestionChoiceData((int)$id));
    }

    public function create($data)
    {
        $this->validatePostParams($this->questionChoiceDataInputValidator, $data);
        $validData = $this->questionChoiceDataInputValidator->getValidData();

        return $this->getSuccessfulJsonResponse([
            'id' => $this->questionChoiceService->create($validData)->getId(),
        ]);
    }

    public function update($id, $data)
    {
        $this->validatePostParams($this->questionChoiceDataInputValidator, $data);
        $this->questionChoiceService->update((int)$id, $this->questionChoiceDataInputValidator->getValidData());

        return $this->getSuccessfulJsonResponse();
    }

    public function patch($id, $data)
    {
        $this->validatePostParams($this->questionChoiceDataInputValidator, $data, partial: true);
        $this->questionChoiceService->update((int)$id, $this->questionChoiceDataInputValidator->getValidData());

        return $this->getSuccessfulJsonResponse();
    }

    public function delete($id)
    {
        $this->questionChoiceService->delete((int)$id);

        return $this->getSuccessfulJsonResponse();
    }

    public function deleteList($data)
    {
        $this->questionChoiceService->deleteList($data);

        return $this->getSuccessfulJsonResponse();
    }

    public function replaceList($data)
    {
        $this->questionChoiceService->replaceList($data);

        return $this->getSuccessfulJsonResponse();
    }
}
