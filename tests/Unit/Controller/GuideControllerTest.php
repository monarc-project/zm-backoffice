<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2026 Luxembourg House of Cybersecurity LHC.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace MonarcBackOfficeTests\Unit\Controller;

use Doctrine\ORM\EntityNotFoundException;
use Monarc\Core\Tests\Fixture\GuideControllerAssertionsTrait;
use PHPUnit\Framework\TestCase;

/** @coversNothing */
class GuideControllerTest extends TestCase
{
    use GuideControllerAssertionsTrait;

    private string $guideControllerClass = \Monarc\BackOffice\Controller\ApiGuidesController::class;
    private string $itemControllerClass = \Monarc\BackOffice\Controller\ApiGuidesItemsController::class;
    private string $moduleClass = \Monarc\BackOffice\Module::class;
    private string $allowedRole = 'superadmin';
    private string $itemListName = 'items';
    private string $officeRoot = __DIR__ . '/../../..';

    public function testTypesEndpointPreservesItsTypeMapAndRejectsMissingDeletionIds(): void
    {
        $controller = new \Monarc\BackOffice\Controller\ApiGuidesTypesController(
            $this->env->guideService,
            $this->env->guideValidator
        );
        self::assertSame([
            1 => 'Risk analysis context',
            2 => 'Risk management context',
            3 => 'Summary assessment of trends and threats',
            4 => 'Summary of assets / impacts',
        ], $controller->getList()->getVariable('type'));
        $id = $this->env->guideService->create($this->env->guideValidator->validate(['type' => 1, 'isWithItems' => 1]));
        try {
            $controller->deleteList([$id, 'invalid']);
            self::fail('Missing identifier must fail deletion');
        } catch (EntityNotFoundException $error) {
            self::assertStringContainsString('was not found', $error->getMessage());
        }
        self::assertSame(1, $this->env->guides->countAll());
        self::assertSame('ok', $controller->deleteList([$id])->getVariable('status'));
        self::assertSame(0, $this->env->guides->countAll());
    }
}
