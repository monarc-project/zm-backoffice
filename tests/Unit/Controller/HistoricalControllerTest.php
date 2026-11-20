<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2026 Luxembourg House of Cybersecurity LHC.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace MonarcBackOfficeTests\Unit\Controller;

use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\StreamFactory;
use Laminas\Mvc\Middleware\PipeSpec;
use Laminas\Router\RouteMatch;
use Monarc\BackOffice\Controller\ApiAdminHistoricalController;
use Monarc\Core\Entity\UserSuperClass;
use Monarc\Core\InputFormatter\Historical\GetHistoricalsInputFormatter;
use Monarc\Core\Service\ConnectedUserService;
use Monarc\Core\Tests\Fixture\HistoricalTestEnvironment;
use PHPUnit\Framework\TestCase;

/** @coversNothing */
class HistoricalControllerTest extends TestCase
{
    private HistoricalTestEnvironment $env;

    protected function setUp(): void
    {
        require_once __DIR__ . '/../../../../zm-core/tests/Fixture/HistoricalTestEnvironment.php';
        $identity = $this->createMock(ConnectedUserService::class);
        $user = $this->createMock(UserSuperClass::class);
        $user->method('getEmail')->willReturn('history@example.invalid');
        $identity->method('getConnectedUser')->willReturn($user);
        $this->env = new HistoricalTestEnvironment($identity);
    }

    protected function tearDown(): void
    {
        $this->env->close();
    }

    public function testLogScreenContractSortingSearchPaginationAndUuidNavigation(): void
    {
        $uuid = '5c732341-2aa6-4d28-b1f3-bff6ecbc1339';
        $this->env->manager->getConnection()->insert('historicals', [
            'source_id' => $uuid,
            'type' => 'amv',
            'label1' => 'Legacy risk',
            'details' => null,
        ]);
        $this->env->service->create(['type' => 'amv', 'label1' => 'New risk', 'details' => 'a / b']);
        $response = $this->request('GET', query: ['order' => '-createdAt', 'page' => 1, 'limit' => 1]);
        self::assertSame(200, $response->getStatusCode());
        $data = json_decode((string)$response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame(2, $data['count']);
        self::assertCount(1, $data['historical']);
        self::assertSame('New risk', $data['historical'][0]['label1']);
        $data = json_decode((string)$this->request('GET', query: ['filter' => 'Legacy'])->getBody(), true);
        self::assertSame(1, $data['count']);
        $row = $data['historical'][0];
        self::assertSame($uuid, $row['sourceId']);
        self::assertSame('amv', $row['type']);
        self::assertSame([''], $row['details']);
        self::assertSame('', $row['createdAt']);
        self::assertSame([
            'id', 'type', 'sourceId', 'action', 'details', 'creator', 'createdAt',
            'label1', 'label2', 'label3', 'label4',
        ], array_keys($row));
    }

    public function testOnlyCollectionGetIsSupported(): void
    {
        foreach ([null, 1] as $id) {
            foreach (['POST', 'PUT', 'PATCH', 'DELETE'] as $method) {
                self::assertSame(405, $this->request($method, $id)->getStatusCode());
            }
        }
        self::assertSame(405, $this->request('GET', 1)->getStatusCode());
        self::assertSame(0, $this->env->table->countAll());
    }

    public function testHistoricalPipeSpecRetainsSysadminAndRejectsOtherRolesAndGuests(): void
    {
        require_once __DIR__ . '/../../../Module.php';
        $config = require __DIR__ . '/../../../config/module.config.php';
        $router = \Laminas\Router\Http\TreeRouteStack::factory($config['router']);
        foreach (['/api/admin/historical', '/api/admin/historical/23'] as $uri) {
            $match = $router->match((new \Laminas\Http\Request())->setUri($uri));
            self::assertSame('monarc_api_admin_historicals', $match->getMatchedRouteName());
            self::assertSame(PipeSpec::class, $match->getParam('controller'));
            self::assertInstanceOf(PipeSpec::class, $match->getParam('middleware'));
        }
        $identity = $this->createMock(ConnectedUserService::class);
        $user = $this->createMock(\Monarc\Core\Entity\UserSuperClass::class);
        $connectedUser = $user;
        $identity->method('getConnectedUser')->willReturnCallback(
            static function () use (&$connectedUser): ?\Monarc\Core\Entity\UserSuperClass {
                return $connectedUser;
            }
        );
        $roles = [];
        $user->method('getRolesArray')->willReturnCallback(static function () use (&$roles): array {
            return $roles;
        });
        $services = new \Laminas\ServiceManager\ServiceManager(['services' => [
            'Config' => $config,
            ConnectedUserService::class => $identity,
        ]]);
        $application = $this->createMock(\Laminas\Mvc\Application::class);
        $application->method('getServiceManager')->willReturn($services);
        $event = (new \Laminas\Mvc\MvcEvent())->setApplication($application)
            ->setRouteMatch($match)->setResponse(new \Laminas\Http\Response())
            ->setViewModel(new \Laminas\View\Model\ViewModel());
        $module = new \Monarc\BackOffice\Module();
        $module->initRbac($event);
        foreach (['sysadmin'] as $role) {
            $roles = [$role];
            self::assertNull($module->checkRbac($event));
        }
        foreach (['guest', 'dbadmin', 'accadmin'] as $role) {
            $roles = [$role];
            self::assertSame(403, $module->checkRbac($event)->getStatusCode());
        }
        $connectedUser = null;
        self::assertSame(401, $module->checkRbac($event)->getStatusCode());
    }

    private function request(string $method, ?int $id = null, array $query = []): object
    {
        $controller = new ApiAdminHistoricalController(new GetHistoricalsInputFormatter(), $this->env->service);
        $request = (new ServerRequest([], [], '/api/admin/historical', $method))
            ->withAttribute(RouteMatch::class, new RouteMatch($id === null ? [] : ['id' => $id]))
            ->withQueryParams($query)
            ->withHeader('Content-Type', 'application/json')
            ->withBody((new StreamFactory())->createStream('{}'));

        return $controller->handle($request);
    }
}
