<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2022 SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Middleware;

use Doctrine\ORM\EntityNotFoundException;
use Fig\Http\Message\StatusCodeInterface;
use Laminas\Diactoros\ResponseFactory;
use Laminas\Router\RouteMatch;
use Monarc\Core\Model\Entity\UserRole;
use Monarc\Core\Model\Entity\UserSuperClass;
use Monarc\Core\Service\ConnectedUserService;
use Monarc\Core\Table\AnrTable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AnrValidationMiddleware implements MiddlewareInterface
{
    private AnrTable $anrTable;
    private UserSuperClass $connectedUser;
    private ResponseFactory $responseFactory;

    public function __construct(
        AnrTable $anrTable,
        ConnectedUserService $connectedUserService,
        ResponseFactory $responseFactory
    ) {
        $this->anrTable = $anrTable;
        $this->connectedUser = $connectedUserService->getConnectedUser();
        $this->responseFactory = $responseFactory;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var RouteMatch $routeMatch */
        $routeMatch = $request->getAttribute(RouteMatch::class);
        /* The anrId param can be passed as inside of URL, as a query param or inside the post data. */
        $anrId = (int)$routeMatch->getParam('anrid');
        if ($anrId === 0) {
            $anrId = (int)($request->getQueryParams()['anr'] ?? 0);
        }
        if ($anrId === 0) {
            $anrId = (int)($request->getParsedBody()['anrId'] ?? 0);
        }

        if ($anrId !== 0) {
            try {
                $anr = $this->anrTable->findById($anrId);
            } catch (EntityNotFoundException $e) {
                return $this->responseFactory->createResponse(
                    StatusCodeInterface::STATUS_NOT_FOUND,
                    sprintf('Analysis with ID %s not found.', $anrId)
                );
            }

            if (!$this->connectedUser->hasRole(UserRole::SUPER_ADMIN)) {
                return $this->responseFactory->createResponse(
                    StatusCodeInterface::STATUS_FORBIDDEN,
                    sprintf('The models access is only allowed for "%s" users.', UserRole::SUPER_ADMIN)
                );
            }

            $request = $request->withAttribute('anr', $anr);
        }

        return $handler->handle($request);
    }
}
