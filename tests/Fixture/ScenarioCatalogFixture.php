<?php declare(strict_types=1);

/** Synthetic, deterministic fixture only; it is not an approved production template. */
return [
    'event' => [
        'provenance' => 'test-fixture',
        'category' => 'service-disruption',
        'translations' => [
            'en' => ['title' => 'Generic service interruption', 'description' => 'A generic test event.'],
            'fr' => ['title' => 'Interruption générique de service', 'description' => 'Un événement de test générique.'],
        ],
    ],
    'template' => [
        'provenance' => 'test-fixture',
        'translations' => [
            'en' => ['title' => 'Generic interruption scenario', 'description' => 'A generic test template.'],
            'fr' => ['title' => 'Scénario générique d’interruption', 'description' => 'Un modèle de test générique.'],
        ],
    ],
];
