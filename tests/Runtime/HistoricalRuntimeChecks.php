<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2026 Luxembourg House of Cybersecurity LHC.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace MonarcBackOfficeTests\Runtime;

use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\StreamFactory;
use Laminas\Mvc\Middleware\PipeSpec;
use Laminas\Router\Http\TreeRouteStack;
use Laminas\Router\RouteMatch;
use Monarc\BackOffice\Controller\ApiAdminHistoricalController;
use Monarc\Core\InputFormatter\Historical\GetHistoricalsInputFormatter;
use Monarc\Core\Service\HistoricalService;
use Psr\Container\ContainerInterface;
use RuntimeException;

class HistoricalRuntimeChecks
{
    public static function run(ContainerInterface $container, string &$phase): array
    {
        $phase = 'BackOffice historical route and controller DI';
        $config = $container->get('Config');
        $router = TreeRouteStack::factory($config['router']);
        $controller = $container->get('ControllerManager')->get(ApiAdminHistoricalController::class);
        $service = $container->get(HistoricalService::class);
        foreach (['/api/admin/historical', '/api/admin/historical/1'] as $uri) {
            $match = $router->match((new \Laminas\Http\Request())->setUri($uri));
            self::check($match->getMatchedRouteName() === 'monarc_api_admin_historicals', 'route name');
            self::check($match->getParam('controller') === PipeSpec::class, 'PipeSpec controller');
            self::check($match->getParam('middleware') instanceof PipeSpec, 'PipeSpec middleware');
        }
        foreach ([null, 1] as $id) {
            foreach (['GET', 'POST', 'PUT', 'PATCH', 'DELETE'] as $method) {
                $query = ['order' => '-createdAt', 'page' => 1, 'limit' => 20];
                $request = (new ServerRequest([], [], '/api/admin/historical', $method))
                    ->withAttribute(RouteMatch::class, new RouteMatch($id === null ? [] : ['id' => $id]))
                    ->withQueryParams($query)
                    ->withHeader('Content-Type', 'application/json')
                    ->withBody((new StreamFactory())->createStream('{}'));
                $response = $controller->handle($request);
                self::check(
                    $response->getStatusCode() === ($method === 'GET' && $id === null ? 200 : 405),
                    'read-only HTTP action'
                );
                if ($method === 'GET' && $id === null) {
                    $data = json_decode((string)$response->getBody(), true, 512, JSON_THROW_ON_ERROR);
                    $params = (new GetHistoricalsInputFormatter())->format($query);
                    self::check($data['count'] === $service->getCount($params), 'count response contract');
                    self::check($data['historical'] === $service->getList($params), 'list response contract');
                }
            }
        }

        return ['route' => 'PipeSpec/DI passed', 'httpActions' => 'collection GET 200; all writes/item GET 405'];
    }

    private static function check(bool $passed, string $name): void
    {
        if (!$passed) {
            throw new RuntimeException('BackOffice historical check failed: ' . $name);
        }
    }
}
