<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2026 Luxembourg House of Cybersecurity LHC.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace MonarcBackOfficeTests\Unit\Controller;

use Monarc\Core\Tests\Fixture\QuestionControllerAssertionsTrait;
use PHPUnit\Framework\TestCase;

/** @coversNothing */
class QuestionControllerTest extends TestCase
{
    use QuestionControllerAssertionsTrait;

    private string $questionControllerClass = \Monarc\BackOffice\Controller\ApiQuestionsController::class;
    private string $choiceControllerClass = \Monarc\BackOffice\Controller\ApiQuestionsChoicesController::class;
    private string $moduleClass = \Monarc\BackOffice\Module::class;
    private string $allowedRole = 'dbadmin';
    private string $officeRoot = __DIR__ . '/../../..';
}
