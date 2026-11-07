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
        $id = $this->questionChoiceDataInputValidator->validateIds([$id])[0];

        return $this->getPreparedJsonResponse($this->questionChoiceService->getEntity($id));
    }

    public function create($data)
    {
        $validData = $this->questionChoiceDataInputValidator->validate($data);
        $id = $this->questionChoiceService->create($validData);

        return $this->getSuccessfulJsonResponse(['id' => $id]);
    }

    public function update($id, $data)
    {
        $id = $this->questionChoiceDataInputValidator->validateIds([$id])[0];
        $validData = $this->questionChoiceDataInputValidator->validate($data);
        $this->questionChoiceService->update($id, $validData);

        return $this->getSuccessfulJsonResponse();
    }

    public function patch($id, $data)
    {
        $id = $this->questionChoiceDataInputValidator->validateIds([$id])[0];
        $validData = $this->questionChoiceDataInputValidator->validate($data, true);
        $this->questionChoiceService->update($id, $validData);

        return $this->getSuccessfulJsonResponse();
    }

    public function delete($id)
    {
        $id = $this->questionChoiceDataInputValidator->validateIds([$id])[0];
        $this->questionChoiceService->delete($id);

        return $this->getSuccessfulJsonResponse();
    }

    public function deleteList($data)
    {
        $ids = $this->questionChoiceDataInputValidator->validateIds($data);
        $this->questionChoiceService->deleteList($ids);

        return $this->getSuccessfulJsonResponse();
    }

    public function replaceList($data)
    {
        $validData = $this->questionChoiceDataInputValidator->validateReplacement($data);
        $this->questionChoiceService->replaceList($validData);

        return $this->getSuccessfulJsonResponse();
    }
}
