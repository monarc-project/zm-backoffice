<?php declare(strict_types=1);

namespace Monarc\BackOffice\Scenario\Service;

use Monarc\BackOffice\Scenario\Table\LegacyBridgeTable;

final class ScenarioReadinessService
{
    public function __construct(private LegacyBridgeTable $legacyBridgeTable)
    {
    }

    public function isReady(): bool
    {
        return $this->legacyBridgeTable->isReady();
    }
}
