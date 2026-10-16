<?php declare(strict_types=1);

namespace Monarc\BackOffice\Scenario\Validator;

use InvalidArgumentException;

/** Validates the public catalog shape before it reaches the domain service. */
final class CatalogRequestValidator
{
    public static function write(array $data, string $kind): void
    {
        if (!isset($data['translations']) || !is_array($data['translations']) || $data['translations'] === []
        ) {
            throw new InvalidArgumentException('Catalog translations are required.');
        }

        foreach ($data['translations'] as $locale => $translation) {
            if (!is_string($locale) || !preg_match('/^[a-z]{2,3}(-[A-Z]{2})?$/', $locale)
                || !is_array($translation) || !isset($translation['title'])
                || !is_string($translation['title']) || trim($translation['title']) === ''
                || (isset($translation['description']) && !is_string($translation['description']))
            ) {
                throw new InvalidArgumentException('Invalid catalog translation.');
            }
        }

        if ($kind === 'event'
            && (!isset($data['category'])
                || !is_string($data['category'])
                || $data['category'] === '')
        ) {
            throw new InvalidArgumentException('Event category is required.');
        }

        if (isset($data['tags']) && (!is_array($data['tags']) || count($data['tags']) > 30)) {
            throw new InvalidArgumentException('Invalid catalog tags.');
        }

        foreach ($data['tags'] ?? [] as $tag) {
            if (!is_string($tag) || trim($tag) === '' || mb_strlen($tag) > 100) {
                throw new InvalidArgumentException('Invalid catalog tag.');
            }
        }

        foreach ($data['references'] ?? [] as $reference) {
            if (!is_array($reference) || !isset($reference['type'], $reference['value'])
                || !is_string($reference['type']) || !is_string($reference['value'])
                || $reference['type'] === '' || $reference['value'] === ''
            ) {
                throw new InvalidArgumentException('Invalid catalog reference.');
            }
        }

        foreach ($data['suggestions'] ?? [] as $suggestion) {
            if (!is_array($suggestion)
                || !isset($suggestion['type'], $suggestion['referenceType'], $suggestion['referenceValue'])
                || !is_string($suggestion['type']) || !is_string($suggestion['referenceType'])
                || !is_string($suggestion['referenceValue'])
            ) {
                throw new InvalidArgumentException('Invalid catalog suggestion.');
            }
        }

        if (isset($data['semanticVersion'])
            && (!is_string($data['semanticVersion'])
                || !preg_match('/^[0-9]+\\.[0-9]+\\.[0-9]+(?:-[0-9A-Za-z.-]+)?$/', $data['semanticVersion']))
        ) {
            throw new InvalidArgumentException('Invalid catalog semantic version.');
        }

        if ($kind !== 'template' && isset($data['eventRefs'])) {
            throw new InvalidArgumentException('Only templates may contain event definitions.');
        }

        foreach ($data['eventRefs'] ?? [] as $eventReference) {
            if (!is_array($eventReference)
                || !isset($eventReference['stableUuid'], $eventReference['version'])
                || !is_string($eventReference['stableUuid'])
                || !preg_match('/^[0-9a-fA-F-]{36}$/', $eventReference['stableUuid'])
                || !is_int($eventReference['version'])
                || $eventReference['version'] < 1
            ) {
                throw new InvalidArgumentException('Invalid template event definition.');
            }
        }
    }

    public static function revision(array $headers): int
    {
        $value = $headers['if-match'] ?? null;
        if (!is_string($value) || !preg_match('/^"?([1-9][0-9]*)"?$/', $value, $matches)) {
            throw new InvalidArgumentException('If-Match revision is required.');
        }

        return (int) $matches[1];
    }
}
