<?php
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2019  SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Monarc\Core\Controller\AbstractController;
use Monarc\Core\Controller\Handler\ControllerRequestResponseHandlerTrait;
use Monarc\Core\Entity\QuestionChoice;
use Monarc\Core\Model\Table\QuestionChoiceTable;
use Monarc\Core\Service\QuestionChoiceService;

/**
 * TODO: extend AbstractRestfulController and remove AbstractController.
 */
class ApiQuestionsChoicesController extends AbstractController
{
    use ControllerRequestResponseHandlerTrait;

    protected $dependencies = ['questions'];
    protected $name = 'choices';

    public function __construct(QuestionChoiceService $questionChoiceService)
    {
        parent::__construct($questionChoiceService);
    }

    /**
     * @inheritdoc
     */
    public function replaceList($data)
    {
        /** @var QuestionChoiceService $service */
        $service = $this->getService();

        /** @var QuestionChoiceTable $table */
        $table = $service->get('table');

        // Remove existing choices
        $questions = $table->fetchAllFiltered(['id'], 1, 0, null, null, ['question' => $data['questionId']]);
        foreach ($questions as $q) {
            $table->delete($q['id']);
        }

        $question = $this->getService()->get('questionTable')->getEntity($data['questionId']);

        // Add new choices
        $pos = 1;
        foreach ($data['choice'] ?? [] as $c) {
            $c['position'] = $pos;
            unset($c['question']);

            /** @var QuestionChoice $choiceEntity */
            $choiceEntity = new QuestionChoice();
            $choiceEntity->setQuestion($question);
            $choiceEntity->squeezeAutoPositionning(true);
            $choiceEntity->exchangeArray($c);
            $table->save($choiceEntity);
            ++$pos;
        }

        return $this->getSuccessfulJsonResponse();
    }
}
