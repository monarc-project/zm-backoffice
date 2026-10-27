<?php declare(strict_types=1);

/** Synthetic, deterministic fixture only; it is not an approved production template. */
return [
    'event' => [
        'category' => 'service-disruption',
        'translations' => [
            'en' => ['title' => 'Generic service interruption', 'description' => 'A generic test event.'],
            'fr' => [
                'title' => 'Interruption générique de service',
                'description' => 'Un événement de test générique.',
            ],
        ],
    ],
    'events' => [
        [
            'stableUuid' => '00000000-0000-4000-8000-000000000101',
            'version' => 1,
            'category' => 'service-disruption',
            'translations' => ['en' => ['title' => 'Supplier service outage', 'description' => 'Synthetic event.']],
        ],
        [
            'stableUuid' => '00000000-0000-4000-8000-000000000102',
            'version' => 1,
            'category' => 'service-disruption',
            'translations' => ['en' => ['title' => 'Payroll processing delay', 'description' => 'Synthetic event.']],
        ],
    ],
    'template' => [
        'translations' => [
            'en' => ['title' => 'Generic interruption scenario', 'description' => 'A generic test template.'],
            'fr' => ['title' => 'Scénario générique d’interruption', 'description' => 'Un modèle de test générique.'],
        ],
        'riskScenarioModels' => [
            [
                'title' => 'Supplier dependency interruption',
                'eventLinks' => [
                    ['stableUuid' => '00000000-0000-4000-8000-000000000101', 'version' => 1],
                ],
            ],
            [
                'title' => 'Payroll service continuity',
                'eventLinks' => [
                    ['stableUuid' => '00000000-0000-4000-8000-000000000102', 'version' => 1],
                ],
            ],
        ],
    ],
];
