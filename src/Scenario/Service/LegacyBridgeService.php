<?php declare(strict_types=1);

namespace Monarc\BackOffice\Scenario\Service;

use DateTimeImmutable;
use DateTimeZone;
use Monarc\BackOffice\Scenario\Table\LegacyBridgeTable;
use Monarc\BackOffice\Scenario\Validator\LegacyHandoffRequestValidator;
use Monarc\Core\Scenario\Feature\ScenarioCapability;
use Monarc\Core\Table\UserTokenTable;

/**
 * Persists opaque, one-time bridge records; legacy tokens and raw codes are
 * never stored.
 */
final class LegacyBridgeService
{
    private const OFFICE = 'backoffice';

    private const DEFAULT_SESSION_TTL_MINUTES = 480;

    private int $sessionTtlMinutes;

    public function __construct(
        private LegacyBridgeTable $legacyBridgeTable,
        private UserTokenTable $userTokenTable,
        private ScenarioCapability $scenarioCapability,
        private array $scenarioConfig,
        private int $authTtlMinutes
    ) {
        $this->authTtlMinutes = max(1, $this->authTtlMinutes);
        $this->sessionTtlMinutes = $this->configuredSessionTtl();
    }

    /** @return array<string, int|string> */
    public function handle(string $path, mixed $data, ?string $legacyToken, ?string $serviceToken): array
    {
        if (!is_array($data)) {
            throw new \RuntimeException('Invalid Scenario handoff');
        }

        return match ($path) {
            '/api/scenario/v1/auth/bridge/consume' => $this->consumeRequest($data, $serviceToken),
            '/api/scenario/v1/auth/session/resolve' => $this->resolveRequest($data, $serviceToken),
            '/api/scenario/v1/auth/session/revoke' => $this->revokeRequest($data, $serviceToken),
            '/api/scenario/v1/auth/bridge/issue' => $this->issueRequest($data, $legacyToken),
            default => throw new \RuntimeException('Invalid Scenario handoff'),
        };
    }

    /** @return array<string, int|string> */
    public function issue(array $data, string $token): array
    {
        $userToken = $this->userTokenTable->findByToken($token);
        if ($userToken === null || $userToken->getDateEnd() <= $this->utcDateTime()) {
            throw new \RuntimeException('Invalid Scenario handoff');
        }

        if (!$this->scenarioCapability->isEnabled()) {
            throw new \RuntimeException('Invalid Scenario handoff');
        }

        $code = bin2hex(random_bytes(32));
        $expires = $this->utcExpiry('+5 minutes');
        $this->legacyBridgeTable->createHandoff(
            $code,
            $data['nonce'],
            $userToken->getUser()->getId(),
            self::OFFICE,
            $data['returnPath'],
            $expires,
            $token
        );

        return [
            'code' => $code,
            'expiresAt' => $this->utcTimestamp($expires),
            'returnPath' => $data['returnPath'],
        ];
    }

    /** @return array<string, int|string> */
    public function consume(array $data): array
    {
        $session = bin2hex(random_bytes(32));
        $expires = $this->utcExpiry(sprintf('+%d minutes', $this->sessionTtlMinutes));
        $handoff = $this->legacyBridgeTable->consumeHandoff(
            $data['code'],
            $data['nonce'],
            $data['audience'],
            $session,
            $expires,
            $this->utcNow(),
            $this->utcNow()
        );
        if ($handoff === null) {
            throw new \RuntimeException('Invalid Scenario handoff');
        }

        return [
            'session' => $session,
            'subject' => (string) $handoff['userId'],
            'office' => $handoff['office'],
            'expiresAt' => $this->utcTimestamp($handoff['expiresAt']),
        ];
    }

    /** @return array<string, int|string>|null */
    public function resolve(string $session): ?array
    {
        if (!$this->scenarioCapability->isEnabled()) {
            $this->legacyBridgeTable->revokeSession($session);

            return null;
        }

        $row = $this->legacyBridgeTable->refreshActiveSession(
            $session,
            $this->utcExpiry(sprintf('+%d minutes', $this->authTtlMinutes)),
            $this->utcNow(),
            $this->utcNow()
        );
        if ($row === null) {
            $this->legacyBridgeTable->revokeSession($session);

            return null;
        }

        return [
            'subject' => (string) $row['user_id'],
            'office' => $row['office'],
            'expiresAt' => $this->utcTimestamp($row['expires_at']),
        ];
    }

    public function revoke(string $session): void
    {
        $this->legacyBridgeTable->revokeSession($session);
    }

    /**
     * Resolves a BFF-held Scenario session for an internal BackOffice request.
     * The browser never receives the service credential used for this path.
     *
     * @return array<string, int|string>|null
     */
    public function resolveInternalSession(string $session, ?string $serviceToken): ?array
    {
        $this->assertInternalCaller($serviceToken);

        return $this->resolve($session);
    }

    public function revokeUserSessions(int $userId): void
    {
        $this->legacyBridgeTable->revokeUserSessions($userId);
    }

    /** @return array<string, int|string> */
    private function issueRequest(array $data, ?string $legacyToken): array
    {
        if ($legacyToken === null || $legacyToken === '') {
            throw new \RuntimeException('Invalid Scenario handoff');
        }

        LegacyHandoffRequestValidator::issue($data);

        return $this->issue($data, $legacyToken);
    }

    /** @return array<string, int|string> */
    private function consumeRequest(array $data, ?string $serviceToken): array
    {
        $this->assertInternalCaller($serviceToken);
        LegacyHandoffRequestValidator::consume($data);

        return $this->consume($data);
    }

    /** @return array<string, int|string> */
    private function resolveRequest(array $data, ?string $serviceToken): array
    {
        $this->assertInternalCaller($serviceToken);
        LegacyHandoffRequestValidator::session($data);
        $session = $this->resolve((string) $data['session']);
        if ($session === null) {
            throw new \RuntimeException('Invalid Scenario handoff');
        }

        return $session;
    }

    /** @return array<string, int|string> */
    private function revokeRequest(array $data, ?string $serviceToken): array
    {
        $this->assertInternalCaller($serviceToken);
        LegacyHandoffRequestValidator::session($data);
        $this->revoke((string) $data['session']);

        return [];
    }

    private function assertInternalCaller(?string $provided): void
    {
        $expected = $this->scenarioConfig['legacyBridgeServiceToken'] ?? null;
        if (!is_string($expected)
            || strlen($expected) < 32
            || !is_string($provided)
            || !hash_equals($expected, $provided)
        ) {
            throw new \RuntimeException('Invalid Scenario handoff');
        }
    }

    private function utcExpiry(string $modifier): string
    {
        return (new DateTimeImmutable('now', new DateTimeZone('UTC')))
            ->modify($modifier)
            ->format('Y-m-d H:i:s');
    }

    private function utcNow(): string
    {
        return (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format('Y-m-d H:i:s');
    }

    private function utcTimestamp(string $date): int
    {
        return (new DateTimeImmutable($date, new DateTimeZone('UTC')))->getTimestamp();
    }

    private function utcDateTime(): DateTimeImmutable
    {
        return new DateTimeImmutable('now', new DateTimeZone('UTC'));
    }

    private function configuredSessionTtl(): int
    {
        $ttl = $this->scenarioConfig['sessionTtl'] ?? self::DEFAULT_SESSION_TTL_MINUTES;

        return is_numeric($ttl) ? max(1, (int) $ttl) : self::DEFAULT_SESSION_TTL_MINUTES;
    }
}
