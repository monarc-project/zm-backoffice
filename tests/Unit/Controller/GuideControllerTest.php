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

    public function testTypesRouteUsesTheRequestHandlerPipelineAndExistingRoleAuthorization(): void
    {
        require_once $this->officeRoot . '/Module.php';
        $config = require $this->officeRoot . '/config/module.config.php';
        $router = \Laminas\Router\Http\TreeRouteStack::factory($config['router']);
        $request = (new \Laminas\Http\Request())->setUri('/api/guides-types');
        $match = $router->match($request);
        self::assertSame('monarc_api_guides_types', $match->getMatchedRouteName());
        self::assertSame(\Laminas\Mvc\Middleware\PipeSpec::class, $match->getParam('controller'));
        self::assertSame([
            \Monarc\BackOffice\Controller\ApiGuidesTypesController::class,
        ], $match->getParam('middleware')->getSpec());

        $connectedUserService = $this->createMock(\Monarc\Core\Service\ConnectedUserService::class);
        $currentUser = null;
        $connectedUserService->method('getConnectedUser')->willReturnCallback(
            function () use (&$currentUser) {
                return $currentUser;
            }
        );
        $container = new \Laminas\ServiceManager\ServiceManager(['services' => [
            'Config' => $config,
            \Monarc\Core\Service\ConnectedUserService::class => $connectedUserService,
        ]]);
        $application = $this->createMock(\Laminas\Mvc\Application::class);
        $application->method('getServiceManager')->willReturn($container);
        $event = (new \Laminas\Mvc\MvcEvent())->setApplication($application)->setRouteMatch($match)
            ->setResponse(new \Laminas\Http\Response())->setViewModel(new \Laminas\View\Model\ViewModel());
        $module = new \Monarc\BackOffice\Module();
        $module->initRbac($event);
        self::assertSame(401, $module->checkRbac($event)->getStatusCode());
        $currentUser = \Monarc\Core\Tests\Fixture\GuideTestEnvironment::user()->setRoles(['guest']);
        self::assertSame(403, $module->checkRbac($event)->getStatusCode());
        $currentUser = \Monarc\Core\Tests\Fixture\GuideTestEnvironment::user()->setRoles(['superadmin']);
        self::assertNull($module->checkRbac($event));
    }

    public function testTypesEndpointPreservesItsTypeMapAndRejectsMissingDeletionIds(): void
    {
        $controller = new \Monarc\BackOffice\Controller\ApiGuidesTypesController(
            $this->env->guideService
        );
        self::assertSame([
            1 => 'Risk analysis context',
            2 => 'Risk management context',
            3 => 'Summary assessment of trends and threats',
            4 => 'Summary of assets / impacts',
        ], $this->request($controller, 'GET')['type']);
        $id = $this->env->guideService->create(
            $this->env->guideValidator->validate(['type' => 1, 'isWithItems' => 1])
        )->getId();
        try {
            $this->request($controller, 'DELETE', data: [$id, 999]);
            self::fail('Missing identifier must fail deletion');
        } catch (EntityNotFoundException $error) {
            self::assertStringContainsString('was not found', $error->getMessage());
        }
        self::assertSame(1, $this->env->guides->countAll());
        self::assertSame('ok', $this->request($controller, 'DELETE', data: [$id])['status']);
        self::assertSame(0, $this->env->guides->countAll());
    }
}
