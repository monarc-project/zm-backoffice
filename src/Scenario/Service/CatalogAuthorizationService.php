<?php declare(strict_types=1);

namespace Monarc\BackOffice\Scenario\Service;

use Monarc\Core\Exception\ActionForbiddenException;
use Monarc\Core\Service\ConnectedUserService;
use Monarc\Core\Table\UserTable;
use Laminas\Http\PhpEnvironment\Request;

final class CatalogAuthorizationService
{
    public function __construct(
        private ConnectedUserService $connectedUserService,
        private LegacyBridgeService $legacyBridgeService,
        private Request $request,
        private UserTable $userTable
    ) {
    }

    public function userId(bool $reviewer = false): int
    {
        $user = $this->connectedUserService->getConnectedUser();
        if ($user === null) {
            $session = $this->request->getHeader('x-scenario-session');
            $serviceToken = $this->request->getHeader('x-scenario-service-token');
            if ($session !== false && $serviceToken !== false) {
                $resolved = $this->legacyBridgeService->resolveInternalSession(
                    $session->getFieldValue(),
                    $serviceToken->getFieldValue()
                );
                if ($resolved !== null && $resolved['office'] === 'backoffice') {
                    $user = $this->userTable->findById((int) $resolved['subject'], false);
                }
            }
        }
        if ($user === null || (!$user->hasRole('dbadmin') && !$user->hasRole('superadmin'))) {
            throw new ActionForbiddenException('Scenario catalog access is forbidden.');
        }
        if ($reviewer && !$user->hasRole('superadmin')) {
            throw new ActionForbiddenException('Scenario catalog publication requires a reviewer.');
        }

        return (int) $user->getId();
    }
}
