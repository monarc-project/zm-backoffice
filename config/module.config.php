<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2024 Luxembourg House of Cybersecurity LHC.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

use Doctrine\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Interop\Container\Containerinterface;
use Laminas\Mvc\Middleware\PipeSpec;
use Laminas\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory;
use Monarc\BackOffice\Controller;
use Monarc\BackOffice\Middleware\AnrValidationMiddleware;
use Monarc\BackOffice\Table\ClientModelTable;
use Monarc\BackOffice\Table\ClientTable;
use Monarc\BackOffice\Table\ServerTable;
use Monarc\BackOffice\Service\ClientService;
use Monarc\BackOffice\Service\ServerService;
use Monarc\BackOffice\Validator\InputValidator\Asset\PostAssetDataInputValidator;
use Monarc\BackOffice\Validator\InputValidator\Client\PostClientInputValidator;
use Monarc\BackOffice\Validator\InputValidator\Server\PostServerDataInputValidator;
use Monarc\BackOffice\Validator\InputValidator\Threat\PostThreatDataInputValidator;
use Monarc\BackOffice\Validator\InputValidator\Vulnerability\PostVulnerabilityDataInputValidator;
use Laminas\Di\Container\AutowireFactory;
use Monarc\Core\Table\AssetTable;
use Monarc\Core\Table\Factory\ClientEntityManagerFactory;
use Monarc\Core\Table\ThreatTable;
use Monarc\Core\Table\VulnerabilityTable;
use Monarc\Core\Validator\InputValidator\InputValidationTranslator;

return [
    'router' => [
        'routes' => [
            'monarc_api_models_duplication' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/api/models-duplication[/:id]',
                    'constraints' => [
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\ApiModelsDuplicationController::class,
                    ],
                ],
            ],

            'monarc_api_admin_historicals' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/api/admin/historical[/:id]',
                    'constraints' => [
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\ApiAdminHistoricalController::class,
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

            'monarc_api_user_activate_2fa' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/api/user/activate2FA/:id',
                    'constraints' => [
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\ApiUserTwoFAController::class,
                    ],
                ],
            ],

            'monarc_api_user_recovery_codes' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/api/user/recoveryCodes/:id',
                    'constraints' => [
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\ApiUserRecoveryCodesController::class,
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
                        'controller' => PipeSpec::class,
                        'middleware' => new PipeSpec(Controller\ApiDeliveriesModelsController::class),
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
                        'controller' => PipeSpec::class,
                        'middleware' => new PipeSpec(Controller\ApiQuestionsController::class),
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
                        'controller' => PipeSpec::class,
                        'middleware' => new PipeSpec(Controller\ApiQuestionsChoicesController::class),
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
                        'controller' => PipeSpec::class,
                        'middleware' => new PipeSpec(Controller\ApiGuidesController::class),
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
                        'controller' => PipeSpec::class,
                        'middleware' => new PipeSpec(Controller\ApiGuidesItemsController::class),
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
                        'controller' => Controller\ApiMeasuresLinksController::class,
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

            'monarc_api_objects' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/api/objects[/:id]',
                    'constraints' => [
                        'id' => '[a-f0-9-]*',
                    ],
                    'defaults' => [
                        'controller' => PipeSpec::class,
                        'middleware' => new PipeSpec(
                            AnrValidationMiddleware::class,
                            Controller\ApiObjectsController::class,
                        ),
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

            'kb_objects_duplication' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/api/objects-duplication',
                    'defaults' => [
                        'controller' => PipeSpec::class,
                        'middleware' => new PipeSpec(
                            Controller\ApiObjectsDuplicationController::class,
                        ),
                    ],
                ],
            ],

            'monarc_api_anr_thresholds' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/api/anr[/:anrid]',
                    'constraints' => [
                        'anrid' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => PipeSpec::class,
                        'middleware' => new PipeSpec(
                            AnrValidationMiddleware::class,
                            Controller\ApiAnrController::class,
                        ),
                    ],
                ],
            ],

            'monarc_api_anr' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/api/anr/:anrid/',
                    'constraints' => [
                        'anrid' => '[0-9]+',
                    ],
                ],
                'may_terminate' => false,
                'child_routes' => [
                    'risks' => [
                        'type' => 'segment',
                        'options' => [
                            'route' => 'risks[/:id]',
                            'constraints' => [
                                'id' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
                                    AnrValidationMiddleware::class,
                                    Controller\ApiAnrRisksController::class
                                ),
                            ],
                        ],
                    ],

                    'risks_op' => [
                        'type' => 'segment',
                        'options' => [
                            'route' => 'risksop[/:id]',
                            'constraints' => [
                                'id' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
                                    AnrValidationMiddleware::class,
                                    Controller\ApiAnrRisksOpController::class
                                ),
                            ],
                        ],
                    ],

                    'instances_risks' => [
                        'type' => 'segment',
                        'options' => [
                            'route' => 'instances-risks[/:id]',
                            'constraints' => [
                                'id' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
                                    AnrValidationMiddleware::class,
                                    Controller\ApiAnrInstancesRisksController::class
                                ),
                            ],
                        ],
                    ],

                    'instances_risksop' => [
                        'type' => 'segment',
                        'options' => [
                            'route' => 'instances-oprisks[/:id]',
                            'constraints' => [
                                'id' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
                                    AnrValidationMiddleware::class,
                                    Controller\ApiAnrInstancesRisksOpController::class
                                ),
                            ],
                        ],
                    ],

                    'objects' => [
                        'type' => 'segment',
                        'options' => [
                            'route' => 'objects[/:id]',
                            'constraints' => [
                                'id' => '[a-f0-9-]*',
                            ],
                            'defaults' => [
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
                                    AnrValidationMiddleware::class,
                                    Controller\ApiObjectsController::class
                                ),
                            ],
                        ],
                    ],

                    'library' => [
                        'type' => 'segment',
                        'options' => [
                            'route' => 'library[/:id]',
                            'constraints' => [
                                'id' => '[a-f0-9-]*',
                            ],
                            'defaults' => [
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
                                    AnrValidationMiddleware::class,
                                    Controller\ApiAnrLibraryController::class
                                ),
                            ],
                        ],
                    ],

                    'objects_parents' => [
                        'type' => 'segment',
                        'options' => [
                            'route' => 'objects/:id/parents',
                            'constraints' => [
                                'id' => '[a-f0-9-]*',
                            ],
                            'defaults' => [
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
                                    AnrValidationMiddleware::class,
                                    Controller\ApiObjectsController::class
                                ),
                                'action' => 'parents',
                            ],
                        ],
                    ],

                    'objects_duplication' => [
                        'type' => 'segment',
                        'options' => [
                            'route' => 'objects-duplication',
                            'defaults' => [
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
                                    AnrValidationMiddleware::class,
                                    Controller\ApiObjectsDuplicationController::class
                                ),
                            ],
                        ],
                    ],

                    'instances' => [
                        'type' => 'segment',
                        'options' => [
                            'route' => 'instances[/:id]',
                            'constraints' => [
                                'id' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
                                    AnrValidationMiddleware::class,
                                    Controller\ApiAnrInstancesController::class
                                ),
                            ],
                        ],
                    ],

                    'instances_consequences' => [
                        'type' => 'segment',
                        'options' => [
                            'route' => 'instances-consequences[/:id]',
                            'constraints' => [
                                'id' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
                                    AnrValidationMiddleware::class,
                                    Controller\ApiAnrInstancesConsequencesController::class
                                ),
                            ],
                        ],
                    ],

                    'scales' => [
                        'type' => 'segment',
                        'options' => [
                            'route' => 'scales[/:id]',
                            'constraints' => [
                                'id' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
                                    AnrValidationMiddleware::class,
                                    Controller\ApiAnrScalesController::class
                                ),
                            ],
                        ],
                    ],

                    'scales_comments' => [
                        'type' => 'segment',
                        'options' => [
                            'route' => 'scales/:scaleId/comments[/:id]',
                            'constraints' => [
                                'scaleId' => '[0-9]+',
                                'id' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
                                    AnrValidationMiddleware::class,
                                    Controller\ApiAnrScalesCommentsController::class
                                ),
                            ],
                        ],
                    ],

                    'scales_types' => [
                        'type' => 'segment',
                        'options' => [
                            'route' => 'scales-types[/:id]',
                            'constraints' => [
                                'id' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
                                    AnrValidationMiddleware::class,
                                    Controller\ApiAnrScalesTypesController::class
                                ),
                            ],
                        ],
                    ],

                    'operational_scales' => [
                        'type' => 'segment',
                        'options' => [
                            'route' => 'operational-scales[/:id]',
                            'constraints' => [
                                'id' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
                                    AnrValidationMiddleware::class,
                                    Controller\ApiOperationalRisksScalesController::class
                                ),
                            ],
                        ],
                    ],

                    'operational_scales_comment' => [
                        'type' => 'segment',
                        'options' => [
                            'route' => 'operational-scales/:scaleid/comments[/:id]',
                            'constraints' => [
                                'id' => '[0-9]+',
                                'scaleid' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
                                    AnrValidationMiddleware::class,
                                    Controller\ApiOperationalRisksScalesCommentsController::class
                                ),
                            ],
                        ],
                    ],

                    'soa_scale_comment' => [
                        'type' => 'segment',
                        'options' => [
                            'route' => 'soa-scale-comment[/:id]',
                            'constraints' => [
                                'id' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
                                    AnrValidationMiddleware::class,
                                    Controller\ApiSoaScaleCommentController::class
                                ),
                            ],
                        ],
                    ],

                    'anr_instance_metadata_field' => [
                        'type' => 'segment',
                        'options' => [
                            'route' => 'anr-instances-metadata-fields[/:id]',
                            'constraints' => [
                                'id' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
                                    AnrValidationMiddleware::class,
                                    Controller\ApiAnrInstancesMetadataFieldsController::class
                                ),
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],

    'controllers' => [
        'invokables' => [],
        'factories' => [
            Controller\ApiModelsDuplicationController::class => AutowireFactory::class,
            Controller\ApiAdminHistoricalController::class => AutowireFactory::class,
            Controller\ApiAdminPasswordsController::class => AutowireFactory::class,
            Controller\ApiAdminServersController::class => AutowireFactory::class,
            Controller\ApiAdminUsersController::class => AutowireFactory::class,
            Controller\ApiAdminUsersRolesController::class => AutowireFactory::class,
            Controller\ApiAnrLibraryController::class => AutowireFactory::class,
            Controller\ApiAnrInstancesMetadataFieldsController::class => AutowireFactory::class,
            Controller\ApiAmvsController::class => AutowireFactory::class,
            Controller\ApiAssetsController::class => AutowireFactory::class,
            Controller\ApiClientsController::class => AutowireFactory::class,
            Controller\ApiConfigController::class => AutowireFactory::class,
            Controller\ApiDeliveriesModelsController::class => AutowireFactory::class,
            Controller\ApiGuidesController::class => AutowireFactory::class,
            Controller\ApiGuidesItemsController::class => AutowireFactory::class,
            Controller\ApiGuidesTypesController::class => AutowireFactory::class,
            Controller\ApiAnrInstancesController::class => AutowireFactory::class,
            Controller\ApiAnrInstancesConsequencesController::class => AutowireFactory::class,
            Controller\ApiMeasuresController::class => AutowireFactory::class,
            Controller\ApiMeasuresLinksController::class => AutowireFactory::class,
            Controller\ApiModelsController::class => AutowireFactory::class,
            Controller\ApiObjectsController::class => AutowireFactory::class,
            Controller\ApiObjectsDuplicationController::class => AutowireFactory::class,
            Controller\ApiObjectsExportController::class => AutowireFactory::class,
            Controller\ApiObjectsObjectsController::class => AutowireFactory::class,
            Controller\ApiObjectsCategoriesController::class => AutowireFactory::class,
            Controller\ApiAnrScalesController::class => AutowireFactory::class,
            Controller\ApiAnrScalesTypesController::class => AutowireFactory::class,
            Controller\ApiAnrScalesCommentsController::class => AutowireFactory::class,
            Controller\ApiOperationalRisksScalesController::class => AutowireFactory::class,
            Controller\ApiOperationalRisksScalesCommentsController::class => AutowireFactory::class,
            Controller\ApiQuestionsController::class => AutowireFactory::class,
            Controller\ApiQuestionsChoicesController::class => AutowireFactory::class,
            Controller\ApiReferentialsController::class => AutowireFactory::class,
            Controller\ApiRolfRisksController::class => AutowireFactory::class,
            Controller\ApiRolfTagsController::class => AutowireFactory::class,
            Controller\ApiAnrRisksController::class => AutowireFactory::class,
            Controller\ApiAnrInstancesRisksController::class => AutowireFactory::class,
            Controller\ApiAnrRisksOpController::class => AutowireFactory::class,
            Controller\ApiAnrInstancesRisksOpController::class => AutowireFactory::class,
            Controller\ApiSoaCategoryController::class => AutowireFactory::class,
            Controller\ApiSoaScaleCommentController::class => AutowireFactory::class,
            Controller\ApiThemesController::class => AutowireFactory::class,
            Controller\ApiThreatsController::class => AutowireFactory::class,
            Controller\ApiUserPasswordController::class => AutowireFactory::class,
            Controller\ApiUserTwoFAController::class => AutowireFactory::class,
            Controller\ApiUserRecoveryCodesController::class => AutowireFactory::class,
            Controller\ApiUserProfileController::class => AutowireFactory::class,
            Controller\ApiVulnerabilitiesController::class => AutowireFactory::class,
        ],
    ],

    'service_manager' => [
        'invokables' => [],
        'factories' => [
            ServerTable::class => ClientEntityManagerFactory::class,
            ClientTable::class => ClientEntityManagerFactory::class,
            ClientModelTable::class => ClientEntityManagerFactory::class,

            ServerService::class => AutowireFactory::class,
            ClientService::class => ReflectionBasedAbstractFactory::class,

            /* Validators */
            PostAssetDataInputValidator::class => static function (Containerinterface $container, $serviceName) {
                return new PostAssetDataInputValidator(
                    $container->get('config'),
                    $container->get(InputValidationTranslator::class),
                    $container->get(AssetTable::class)
                );
            },
            PostThreatDataInputValidator::class => static function (Containerinterface $container, $serviceName) {
                return new PostThreatDataInputValidator(
                    $container->get('config'),
                    $container->get(InputValidationTranslator::class),
                    $container->get(ThreatTable::class)
                );
            },
            PostVulnerabilityDataInputValidator::class => static function (
                Containerinterface $container,
                $serviceName
            ) {
                return new PostVulnerabilityDataInputValidator(
                    $container->get('config'),
                    $container->get(InputValidationTranslator::class),
                    $container->get(VulnerabilityTable::class)
                );
            },
            PostServerDataInputValidator::class => ReflectionBasedAbstractFactory::class,
            PostClientInputValidator::class => ReflectionBasedAbstractFactory::class,
        ],
    ],

    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'strategies' => [
            'ViewJsonStrategy',
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
                    __DIR__ . '/../../backoffice/src/Entity',
                ],
            ],
            'orm_cli' => [
                'class' => MappingDriverChain::class,
                'drivers' => [
                    'Monarc\BackOffice\Entity' => 'Monarc_cli_driver',
                ],
            ],
        ],
    ],

    'roles' => [
        // Super Admin : Gestion des droits des utilisateurs uniquement (Carnet d’adresses)
        'superadmin' => [
            'monarc_api_user_password',
            'monarc_api_admin_user_reset_password',
            'monarc_api_guides',
            'monarc_api_guides_items',
            'monarc_api_guides_types',
            'monarc_api_themes',
            'monarc_api_soacategory',
            'monarc_api_models',
            'monarc_api_admin_users',
            'monarc_api_admin_users_roles',
            'monarc_api_user_profile',
            'monarc_api_user_activate_2fa',
            'monarc_api_user_recovery_codes',
        ],
        // Admin DB : Gestion des bases de connaissances (paramètres généraux)
        'dbadmin' => [
            'monarc_api_user_password',
            'monarc_api_amvs',
            'monarc_api_assets',
            'monarc_api_anr_thresholds',
            'monarc_api_anr/risks',
            'monarc_api_anr/risks_op',
            'monarc_api_anr/export',
            'monarc_api_anr/instances',
            'monarc_api_anr/instances_export',
            'monarc_api_anr/instances_risks',
            'monarc_api_anr/instances_risksop',
            'monarc_api_anr/instances_consequences',
            'monarc_api_anr/anr_instance_metadata_field',
            'monarc_api_anr/library',
            'monarc_api_anr/library_category',
            'monarc_api_anr/objects',
            'monarc_api_referentials',
            'monarc_api_measures',
            'monarc_api_measuremeasure',
            'monarc_api_questions',
            'monarc_api_questions_choices',
            'monarc_api_models',
            'monarc_api_models_duplication',
            'monarc_api_objects',
            'monarc_api_objects_categories',
            'kb_objects_duplication',
            'monarc_api_anr/objects_duplication',
            'monarc_api_objects_export',
            'monarc_api_objects_objects',
            'monarc_api_rolf_categories',
            'monarc_api_rolf_risks',
            'monarc_api_rolf_tags',
            'monarc_api_anr/scales',
            'monarc_api_anr/operational_scales',
            'monarc_api_anr/operational_scales_comment',
            'monarc_api_anr/scales_comments',
            'monarc_api_anr/scales_types',
            'monarc_api_anr/metadatas_on_instances',
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
            'monarc_api_user_profile',
            'monarc_api_anr/objects_parents',
            'monarc_api_anr/soa_scale_comment',
            'monarc_api_user_activate_2fa',
            'monarc_api_user_recovery_codes',
        ],
        // Admin système : Gestion des logs et tout ce qui est non applicatif (Administration)
        'sysadmin' => [
            'monarc_api_user_password',
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
            'monarc_api_user_activate_2fa',
            'monarc_api_user_recovery_codes',
        ],
        // Admin comptes : Création des comptes et authentification client
        'accadmin' => [
            'monarc_api_admin_user_reset_password',
            'monarc_api_user_activate_2fa',
            'monarc_api_user_recovery_codes',
            'monarc_api_user_password',
            'monarc_api_clients',
            // There are additional validations of the role in the controller.
            'monarc_api_admin_servers',
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
    'activeLanguages' => ['fr', 'en', 'de', 'nl'],
];
