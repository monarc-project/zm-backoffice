<?php

use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Monarc\BackOffice\Controller;
use Monarc\BackOffice\Model\DbCli;
use Monarc\BackOffice\Model\Entity\Client;
use Monarc\BackOffice\Model\Entity\Server;
use Monarc\BackOffice\Model\Table\ClientTable;
use Monarc\BackOffice\Model\Table\ServerTable;
use Monarc\BackOffice\Service\ClientService;
use Monarc\BackOffice\Service\ClientServiceFactory;
use Monarc\BackOffice\Service\Model\DbCliFactory;
use Monarc\BackOffice\Service\Model\Entity\ClientServiceModelEntity;
use Monarc\BackOffice\Service\Model\Entity\ServerServiceModelEntity;
use Monarc\BackOffice\Service\ServerService;
use Monarc\BackOffice\Service\ServerServiceFactory;
use Monarc\BackOffice\Validator\UniqueClientProxyAlias;
use Zend\Di\Container\AutowireFactory;

return [
    'router' => [
        'routes' => [
            'monarc_api_admin_historicals' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/api/admin/historical[/:id]',
                    'constraints' => [
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\ApiAdminHistoricalsController::class,
                    ],
                ],
            ],

            'monarc_api_admin_passwords' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/api/admin/passwords',
                    'constraints' => [],
                    'defaults' => [
                        'controller' => Controller\ApiAdminPasswordsController::class,
                    ],
                ],
            ],

            'monarc_api_admin_roles' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/api/admin/roles[/:id]',
                    'constraints' => [
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\ApiAdminRolesController::class,
                    ],
                ],
            ],

            'monarc_api_admin_servers' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/api/admin/servers[/:id]',
                    'constraints' => [
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\ApiAdminServersController::class,
                    ],
                ],
            ],

            'monarc_api_admin_servers_get' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/api/admin/serversget[/:id]',
                    'constraints' => [
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\ApiAdminServersGetController::class,
                    ],
                ],
            ],

            'monarc_api_admin_users' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/api/admin/users[/:id]',
                    'constraints' => [
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\ApiAdminUsersController::class,
                    ],
                ],
            ],

            'monarc_api_admin_users_roles' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/api/users-roles[/:id]',
                    'constraints' => [
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\ApiAdminUsersRolesController::class,
                    ],
                ],
            ],

            'monarc_api_amvs' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/api/amvs[/:id]',
                    'constraints' => [
                        'id' => '[a-f0-9-]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\ApiAmvsController::class,
                    ],
                ],
            ],

            'monarc_api_assets' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/api/assets[/:id]',
                    'constraints' => [
                        'id' => '[a-f0-9-]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\ApiAssetsController::class,
                    ],
                ],
            ],

            'monarc_api_clients' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/api/clients[/:id]',
                    'constraints' => [
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\ApiClientsController::class,
                    ],
                ],
            ],

            'monarc_api_config' => [
                'type' => 'literal',
                'options' => [
                    'route' => '/api/config',
                    'defaults' => [
                        'controller' => Controller\ApiConfigController::class,
                    ],
                ],
            ],

            'monarc_api_doc_models' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/api/deliveriesmodels[/:id]',
                    'constraints' => [
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\ApiDeliveriesModelsController::class,
                    ],
                ],
            ],

            'monarc_api_questions' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/api/questions[/:id]',
                    'constraints' => [
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\ApiQuestionsController::class,
                    ],
                ],
            ],

            'monarc_api_questions_choices' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/api/questions-choices[/:id]',
                    'constraints' => [
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\ApiQuestionsChoicesController::class,
                    ],
                ],
            ],

            'monarc_api_guides' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/api/guides[/:id]',
                    'constraints' => [
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\ApiGuidesController::class,
                    ],
                ],
            ],

            'monarc_api_guides_items' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/api/guides-items[/:id]',
                    'constraints' => [
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\ApiGuidesItemsController::class,
                    ],
                ],
            ],

            'monarc_api_guides_types' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/api/guides-types[/:id]',
                    'constraints' => [
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\ApiGuidesTypesController::class,
                    ],
                ],
            ],

            'monarc_api_referentials' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/api/referentials[/:id]',
                    'constraints' => [
                        'id' => '[a-f0-9-]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\ApiReferentialsController::class,
                    ],
                ],
            ],

            'monarc_api_measures' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/api/measures[/:id]',
                    'constraints' => [
                        'id' => '[a-f0-9-]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\ApiMeasuresController::class,
                    ],
                ],
            ],

            'monarc_api_measuremeasure' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/api/measuresmeasures[/:id]',
                    'constraints' => [
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\ApiMeasureMeasureController::class,
                    ],
                ],
            ],

            'monarc_api_objects_categories' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/api/objects-categories[/:id]',
                    'constraints' => [
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\ApiObjectsCategoriesController::class,
                    ],
                ],
            ],

            'monarc_api_models' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/api/models[/:id]',
                    'constraints' => [
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\ApiModelsController::class,
                    ],
                ],
            ],

            'monarc_api_objects' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/api/objects[/:id]',
                    'constraints' => [
                        'id' => '[a-f0-9-]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\ApiObjectsController::class,
                    ],
                ],
            ],

            'monarc_api_objects_duplication' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/api/objects-duplication',
                    'constraints' => [],
                    'defaults' => [
                        'controller' => Controller\ApiObjectsDuplicationController::class,
                    ],
                ],
            ],

            'monarc_api_objects_export' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/api/objects-export',
                    'constraints' => [],
                    'defaults' => [
                        'controller' => Controller\ApiObjectsExportController::class,
                    ],
                ],
            ],

            'monarc_api_objects_objects' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/api/objects-objects[/:id]',
                    'constraints' => [],
                    'defaults' => [
                        'controller' => Controller\ApiObjectsObjectsController::class,
                    ],
                ],
            ],

            'monarc_api_rolf_categories' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/api/rolf-categories[/:id]',
                    'constraints' => [
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\ApiRolfCategoriesController::class,
                    ],
                ],
            ],

            'monarc_api_rolf_risks' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/api/rolf-risks[/:id]',
                    'constraints' => [
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\ApiRolfRisksController::class,
                    ],
                ],
            ],

            'monarc_api_rolf_tags' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/api/rolf-tags[/:id]',
                    'constraints' => [
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\ApiRolfTagsController::class,
                    ],
                ],
            ],

            'monarc_api_themes' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/api/themes[/:id]',
                    'constraints' => [
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\ApiThemesController::class,
                    ],
                ],
            ],


            'monarc_api_soacategory' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/api/soacategory[/:id]',
                    'constraints' => [
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\ApiSoaCategoryController::class,
                    ],
                ],
            ],

            'monarc_api_threats' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/api/threats[/:id]',
                    'constraints' => [
                        'id' => '[a-f0-9-]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\ApiThreatsController::class,
                    ],
                ],
            ],

            'monarc_api_user_password' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/api/user/password/:id',
                    'constraints' => [
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\ApiUserPasswordController::class,
                    ],
                ],
            ],

            'monarc_api_user_profile' => [
                'type' => 'literal',
                'options' => [
                    'route' => '/api/user/profile',
                    'defaults' => [
                        'controller' => Controller\ApiUserProfileController::class,
                    ],
                ],
            ],

            'monarc_api_vulnerabilities' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/api/vulnerabilities[/:id]',
                    'constraints' => [
                        'id' => '[a-f0-9-]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\ApiVulnerabilitiesController::class,
                    ],
                ],
            ],
        ],
    ],

    'controllers' => [
        'invokables' => [],
        'factories' => [
            Controller\ApiAdminHistoricalsController::class => AutowireFactory::class,
            Controller\ApiUserPasswordController::class => AutowireFactory::class,
            Controller\ApiAdminPasswordsController::class => AutowireFactory::class,
            Controller\ApiAdminRolesController::class => AutowireFactory::class,
            Controller\ApiAdminServersController::class => AutowireFactory::class,
            Controller\ApiAdminServersGetController::class => AutowireFactory::class,
            Controller\ApiAdminUsersController::class => AutowireFactory::class,
            Controller\ApiAdminUsersRolesController::class => AutowireFactory::class,
            Controller\ApiAmvsController::class => AutowireFactory::class,
            Controller\ApiAssetsController::class => AutowireFactory::class,
            Controller\ApiClientsController::class => AutowireFactory::class,
            Controller\ApiConfigController::class => AutowireFactory::class,
            Controller\ApiQuestionsController::class => AutowireFactory::class,
            Controller\ApiQuestionsChoicesController::class => AutowireFactory::class,
            Controller\ApiGuidesController::class => AutowireFactory::class,
            Controller\ApiGuidesItemsController::class => AutowireFactory::class,
            Controller\ApiGuidesTypesController::class => AutowireFactory::class,
            Controller\ApiReferentialsController::class => AutowireFactory::class,
            Controller\ApiMeasuresController::class => AutowireFactory::class,
            Controller\ApiMeasureMeasureController::class => AutowireFactory::class,
            Controller\ApiObjectsController::class => AutowireFactory::class,
            Controller\ApiObjectsDuplicationController::class => AutowireFactory::class,
            Controller\ApiObjectsExportController::class => AutowireFactory::class,
            Controller\ApiObjectsObjectsController::class => AutowireFactory::class,
            Controller\ApiObjectsCategoriesController::class => AutowireFactory::class,
            Controller\ApiRolfRisksController::class => AutowireFactory::class,
            Controller\ApiRolfTagsController::class => AutowireFactory::class,
            Controller\ApiThemesController::class => AutowireFactory::class,
            Controller\ApiSoaCategoryController::class => AutowireFactory::class,
            Controller\ApiThreatsController::class => AutowireFactory::class,
            Controller\ApiVulnerabilitiesController::class => AutowireFactory::class,
            Controller\ApiDeliveriesModelsController::class => AutowireFactory::class,
            Controller\ApiUserProfileController::class => AutowireFactory::class,
        ],
    ],

    'service_manager' => [
        'invokables' => [
            UniqueClientProxyAlias::class => UniqueClientProxyAlias::class,
        ],
        'factories' => [
            DbCli::class => DbCliFactory::class,

            ServerTable::class => AutowireFactory::class,
            ClientTable::class => AutowireFactory::class,

            // TODO: remove the factories and refactor the services, instantiate entities from the services directly.
            ServerService::class => ServerServiceFactory::class,
            ClientService::class => ClientServiceFactory::class,
            Server::class => ServerServiceModelEntity::class,
            Client::class => ClientServiceModelEntity::class,
        ],
    ],

    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'strategies' => [
            'ViewJsonStrategy'
        ],
        'template_map' => [
            'monarc-bo/index/index' => __DIR__ . '/../view/layout/layout.phtml',
            'error/404' => __DIR__ . '/../view/layout/layout.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],

    'doctrine' => [
        'driver' => [
            'Monarc_cli_driver' => [
                'class' => AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [
                    __DIR__ . '/../src/Model/Entity',
                    __DIR__ . '/../../core/src/Model/Entity',
                    __DIR__ . '/../../backoffice/src/Model/Entity',
                ],
            ],
            'orm_cli' => [
                'class' => MappingDriverChain::class,
                'drivers' => [
                    'Monarc\BackOffice\Model\Entity' => 'Monarc_cli_driver',
                ],
            ],
        ],
    ],

    'roles' => [
        // Super Admin : Gestion des droits des utilisateurs uniquement (Carnet d’adresses)
        'superadmin' => [
            'monarc_api_guides',
            'monarc_api_guides_items',
            'monarc_api_guides_types',
            'monarc_api_themes',
            'monarc_api_soacategory',
            'monarc_api_models',
            'monarc_api_admin_users',
            'monarc_api_admin_users_roles',
            'monarc_api_user_profile',
        ],
        // Admin DB : Gestion des bases de connaissances (paramètres généraux)
        'dbadmin' => [
            'monarc_api_amvs',
            'monarc_api_assets',
            'monarc_api_anr',
            'monarc_api_anr_risks',
            'monarc_api_anr_risks_op',
            'monarc_api_anr_export',
            'monarc_api_anr_instances',
            'monarc_api_anr_instances_export',
            'monarc_api_anr_instances_risks',
            'monarc_api_anr_instances_risksop',
            'monarc_api_anr_instances_consequences',
            'monarc_api_anr_library',
            'monarc_api_anr_library_category',
            'monarc_api_anr_objects',
            'monarc_api_referentials',
            'monarc_api_measures',
            'monarc_api_measuremeasure',
            'monarc_api_questions',
            'monarc_api_questions_choices',
            'monarc_api_models',
            'monarc_api_models_duplication',
            'monarc_api_objects',
            'monarc_api_objects_categories',
            'monarc_api_objects_duplication',
            'monarc_api_objects_export',
            'monarc_api_objects_objects',
            'monarc_api_rolf_categories',
            'monarc_api_rolf_risks',
            'monarc_api_rolf_tags',
            'monarc_api_scales',
            'monarc_api_scales_comments',
            'monarc_api_scales_types',
            'monarc_api_threats',
            'monarc_api_vulnerabilities',
            'monarc_api_admin_users_roles',
            'monarc_api_doc_models',
            'monarc_api_model_objects',
            'monarc_api_guides',
            'monarc_api_guides_items',
            'monarc_api_guides_types',
            'monarc_api_themes',
            'monarc_api_soacategory',
            'monarc_api_models',
            'monarc_api_admin_users_roles',
            'monarc_api_user_profile',
            'monarc_api_anr_objects_parents',
        ],
        // Admin système : Gestion des logs et tout ce qui est non applicatif (Administration)
        'sysadmin' => [
            'monarc_api_admin_historicals',
            'monarc_api_admin_servers',
            'monarc_api_guides',
            'monarc_api_guides_items',
            'monarc_api_guides_types',
            'monarc_api_themes',
            'monarc_api_soacategory',
            'monarc_api_models',
            'monarc_api_models_duplication',
            'monarc_api_admin_users_roles',
            'monarc_api_user_profile',
        ],
        // Admin comptes : Création des comptes et authentification client
        'accadmin' => [
            'monarc_api_user_password',
            'monarc_api_clients',
            'monarc_api_admin_servers_get',
            'monarc_api_guides',
            'monarc_api_guides_items',
            'monarc_api_guides_types',
            'monarc_api_themes',
            'monarc_api_soacategory',
            'monarc_api_models',
            'monarc_api_admin_users_roles',
            'monarc_api_user_profile',
        ],
    ],
    'activeLanguages' => ['fr', 'en', 'es', 'ne'],
];
