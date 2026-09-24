<?php declare(strict_types=1);

namespace Monarc\BackOffice\Tests\Unit\Scenario\Validator;

use Monarc\BackOffice\Scenario\Validator\CatalogRequestValidator;
use PHPUnit\Framework\TestCase;

final class CatalogRequestValidatorTest extends TestCase
{
    public function testItAcceptsANormalizedTemplateComposition(): void
    {
        CatalogRequestValidator::write([
            'provenance' => 'synthetic-test-source',
            'semanticVersion' => '1.2.0',
            'translations' => ['en' => ['title' => 'Template', 'description' => 'Description']],
            'tags' => ['synthetic'],
            'references' => [['type' => 'framework', 'value' => 'TEST-1']],
            'suggestions' => [[
                'type' => 'control', 'referenceType' => 'measure', 'referenceValue' => 'TEST-2',
            ]],
            'eventRefs' => [['stableUuid' => '00000000-0000-4000-8000-000000000001', 'version' => 1]],
        ], 'template');

        $this->addToAssertionCount(1);
    }

    public function testItRejectsEventCompositionOnAnEventDefinition(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        CatalogRequestValidator::write([
            'provenance' => 'synthetic-test-source',
            'category' => 'test',
            'translations' => ['en' => ['title' => 'Event', 'description' => 'Description']],
            'eventRefs' => [['stableUuid' => '00000000-0000-4000-8000-000000000001', 'version' => 1]],
        ], 'event');
    }

    public function testItRequiresAnOptimisticConcurrencyRevision(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        CatalogRequestValidator::revision([]);
    }
}
