<?php
namespace MonarcBO;

return array(
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/',
                    'defaults' => array(
                        'controller' => 'MonarcBO\Controller\Index',
                        'action' => 'index',
                    ),
                ),
            ),

            'monarc_api_admin_users' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/api/admin/users[/:id]',
                    'constraints' => array(
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'MonarcBO\Controller\ApiAdminUsers',
                    ),
                ),
            ),

            'monarc_api_admin_servers' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/api/admin/servers[/:id]',
                    'constraints' => array(
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'MonarcBO\Controller\ApiAdminServers',
                    ),
                ),
            ),

            'monarc_api_clients' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/api/clients[/:id]',
                    'constraints' => array(
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'MonarcBO\Controller\ApiClients',
                    ),
                ),
            ),

            'monarc_api_admin_roles' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/api/admin/roles[/:id]',
                    'constraints' => array(
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'MonarcBO\Controller\ApiAdminRoles',
                    ),
                ),
            ),

            'monarc_api_admin_passwords' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/api/admin/passwords',
                    'constraints' => array(
                    ),
                    'defaults' => array(
                        'controller' => 'MonarcBO\Controller\ApiAdminPasswords',
                    ),
                ),
            ),

            'monarc_api_admin_password' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/api/admin/password/:id',
                    'constraints' => array(
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'MonarcBO\Controller\ApiAdminPassword',
                    ),
                ),
            ),

            'monarc_api_models' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/api/models[/:id]',
                    'constraints' => array(
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'MonarcBO\Controller\ApiModels',
                    ),
                ),
            ),

            'monarc_api_guides' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/api/guides[/:id]',
                    'constraints' => array(
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'MonarcBO\Controller\ApiGuides',
                    ),
                ),
            ),

            'monarc_api_guides_items' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/api/guides-items[/:id]',
                    'constraints' => array(
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'MonarcBO\Controller\ApiGuidesItems',
                    ),
                ),
            ),

            'monarc_api_guides_types' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/api/guides-types[/:id]',
                    'constraints' => array(
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'MonarcBO\Controller\ApiGuidesTypes',
                    ),
                ),
            ),

            'monarc_api_scales' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/api/anr/:anrId/scales[/:id]',
                    'constraints' => array(
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'MonarcBO\Controller\ApiScales',
                    ),
                ),
            ),

            'monarc_api_scales_types' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/api/anr/:anrId/scales-types[/:id]',
                    'constraints' => array(
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'MonarcBO\Controller\ApiScalesTypes',
                    ),
                ),
            ),

            'monarc_api_scales_comments' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/api/anr/:anrId/scales/:type/comments[/:id]',
                    'constraints' => array(
                        'anrId' => '[0-9]+',
                        'type' => '[a-z]+',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'MonarcBO\Controller\ApiScalesComments',
                    ),
                ),
            ),

            'monarc_api_measures' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/api/measures[/:id]',
                    'constraints' => array(
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'MonarcBO\Controller\ApiMeasures',
                    ),
                ),
            ),

            'monarc_api_assets' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/api/assets[/:id]',
                    'constraints' => array(
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'MonarcBO\Controller\ApiAssets',
                    ),
                ),
            ),

            'monarc_api_amvs' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/api/amvs[/:id]',
                    'constraints' => array(
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'MonarcBO\Controller\ApiAmvs',
                    ),
                ),
            ),

            'monarc_api_vulnerabilities' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/api/vulnerabilities[/:id]',
                    'constraints' => array(
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'MonarcBO\Controller\ApiVulnerabilities',
                    ),
                ),
            ),

            'monarc_api_rolf_categories' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/api/rolf-categories[/:id]',
                    'constraints' => array(
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'MonarcBO\Controller\ApiRolfCategories',
                    ),
                ),
            ),

            'monarc_api_rolf_risks' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/api/rolf-risks[/:id]',
                    'constraints' => array(
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'MonarcBO\Controller\ApiRolfRisks',
                    ),
                ),
            ),

            'monarc_api_rolf_tags' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/api/rolf-tags[/:id]',
                    'constraints' => array(
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'MonarcBO\Controller\ApiRolfTags',
                    ),
                ),
            ),

            'monarc_api_themes' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/api/themes[/:id]',
                    'constraints' => array(
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'MonarcBO\Controller\ApiThemes',
                    ),
                ),
            ),

            'monarc_api_admin_historicals' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/api/admin/historical[/:id]',
                    'constraints' => array(
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'MonarcBO\Controller\ApiAdminHistoricals',
                    ),
                ),
            ),

            'monarc_api_admin_users_roles' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/api/users-roles[/:id]',
                    'constraints' => array(
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'MonarcBO\Controller\ApiAdminUsersRoles',
                    ),
                ),
            ),

            'monarc_api_objects' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/api/objects[/:id]',
                    'constraints' => array(
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'MonarcBO\Controller\ApiObjects',
                    ),
                ),
            ),

            'monarc_api_objects_duplication' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/api/objects-duplication',
                    'constraints' => array(),
                    'defaults' => array(
                        'controller' => 'MonarcBO\Controller\ApiObjectsDuplication',
                    ),
                ),
            ),

            'monarc_api_objects_categories' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/api/objects-categories[/:id]',
                    'constraints' => array(
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'MonarcBO\Controller\ApiObjectsCategories',
                    ),
                ),
            ),

            'monarc_api_objects_risks' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/api/objects-risks[/:id]',
                    'constraints' => array(
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'MonarcBO\Controller\ApiObjectsRisks',
                    ),
                ),
            ),

            'monarc_api_threats' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/api/threats[/:id]',
                    'constraints' => array(
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'MonarcBO\Controller\ApiThreats',
                    ),
                ),
            ),

            'monarc_api_config' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/api/config',
                    'defaults' => array(
                        'controller' => 'MonarcBO\Controller\ApiConfig',
                    ),
                ),
            ),

            'monarc_api_doc_models' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/api/docmodels[/:id]',
                    'constraints' => array(
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'MonarcBO\Controller\ApiDocModels',
                    ),
                ),
            ),
        ),
    ),

    'controllers' => array(
        'invokables' => array(
            '\MonarcBO\Controller\Index' => '\MonarcBO\Controller\IndexController',
        ),
        'factories' => array(
            '\MonarcBO\Controller\ApiAdminHistoricals'      => '\MonarcBO\Controller\ApiAdminHistoricalsControllerFactory',
            '\MonarcBO\Controller\ApiAdminPassword'         => '\MonarcBO\Controller\ApiAdminPasswordControllerFactory',
            '\MonarcBO\Controller\ApiAdminPasswords'        => '\MonarcBO\Controller\ApiAdminPasswordsControllerFactory',
            '\MonarcBO\Controller\ApiAdminRoles'            => '\MonarcBO\Controller\ApiAdminRolesControllerFactory',
            '\MonarcBO\Controller\ApiAdminServers'          => '\MonarcBO\Controller\ApiAdminServersControllerFactory',
            '\MonarcBO\Controller\ApiAdminUsers'            => '\MonarcBO\Controller\ApiAdminUsersControllerFactory',
            '\MonarcBO\Controller\ApiAdminUsersRoles'       => '\MonarcBO\Controller\ApiAdminUsersRolesControllerFactory',
            '\MonarcBO\Controller\ApiAmvs'                  => '\MonarcBO\Controller\ApiAmvsControllerFactory',
            '\MonarcBO\Controller\ApiAssets'                => '\MonarcBO\Controller\ApiAssetsControllerFactory',
            '\MonarcBO\Controller\ApiClients'               => '\MonarcBO\Controller\ApiClientsControllerFactory',
            '\MonarcBO\Controller\ApiConfig'                => '\MonarcBO\Controller\ApiConfigControllerFactory',
            '\MonarcBO\Controller\ApiGuides'                => '\MonarcBO\Controller\ApiGuidesControllerFactory',
            '\MonarcBO\Controller\ApiGuidesItems'           => '\MonarcBO\Controller\ApiGuidesItemsControllerFactory',
            '\MonarcBO\Controller\ApiGuidesTypes'           => '\MonarcBO\Controller\ApiGuidesTypesControllerFactory',
            '\MonarcBO\Controller\ApiMeasures'              => '\MonarcBO\Controller\ApiMeasuresControllerFactory',
            '\MonarcBO\Controller\ApiModels'                => '\MonarcBO\Controller\ApiModelsControllerFactory',
            '\MonarcBO\Controller\ApiObjects'               => '\MonarcBO\Controller\ApiObjectsControllerFactory',
            '\MonarcBO\Controller\ApiObjectsDuplication'    => '\MonarcBO\Controller\ApiObjectsDuplicationControllerFactory',
            '\MonarcBO\Controller\ApiObjectsCategories'     => '\MonarcBO\Controller\ApiObjectsCategoriesControllerFactory',
            '\MonarcBO\Controller\ApiObjectsRisks'          => '\MonarcBO\Controller\ApiObjectsRisksControllerFactory',
            '\MonarcBO\Controller\ApiScales'                => '\MonarcBO\Controller\ApiScalesControllerFactory',
            '\MonarcBO\Controller\ApiScalesTypes'           => '\MonarcBO\Controller\ApiScalesTypesControllerFactory',
            '\MonarcBO\Controller\ApiScalesComments'        => '\MonarcBO\Controller\ApiScalesCommentsControllerFactory',
            '\MonarcBO\Controller\ApiRolfCategories'        => '\MonarcBO\Controller\ApiRolfCategoriesControllerFactory',
            '\MonarcBO\Controller\ApiRolfRisks'             => '\MonarcBO\Controller\ApiRolfRisksControllerFactory',
            '\MonarcBO\Controller\ApiRolfTags'              => '\MonarcBO\Controller\ApiRolfTagsControllerFactory',
            '\MonarcBO\Controller\ApiThemes'                => '\MonarcBO\Controller\ApiThemesControllerFactory',
            '\MonarcBO\Controller\ApiThreats'               => '\MonarcBO\Controller\ApiThreatsControllerFactory',
            '\MonarcBO\Controller\ApiVulnerabilities'       => '\MonarcBO\Controller\ApiVulnerabilitiesControllerFactory',
            '\MonarcBO\Controller\ApiDocModels'             => '\MonarcBO\Controller\ApiDocModelsControllerFactory',
        ),
    ),

    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'strategies' => array(
            'viewJsonStrategy'
        ),
        'template_map' => array(
            'monarc-bo/index/index' => __DIR__ . '/../view/layout/layout.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),

    'doctrine' => array(
        'driver' => array(
            'Monarc_cli_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(
                    __DIR__ . '/../src/MonarcBO/Model/Entity',
                ),
            ),
            'orm_cli' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\DriverChain',
                'drivers' => array(
                    'MonarcBO\Model\Entity' => 'Monarc_cli_driver',
                ),
            ),
        ),
    ),


    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),
    'roles' => array(
        // Super Admin : Gestion des droits des utilisateurs uniquement (Carnet d’adresses)
        'superadmin'=> array(
            'monarc_api_admin_users_roles',
        ),
        // Admin DB : Gestion des bases de connaissances (paramètres généraux)
        'dbadmin'=> array(
            'monarc_api_amvs',
            'monarc_api_assets',
            'monarc_api_measures',
            'monarc_api_models',
            'monarc_api_objects',
            'monarc_api_objects_categories',
            'monarc_api_objects_duplication',
            'monarc_api_objects_risks',
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
        ),
        // Admin système : Gestion des logs et tout ce qui est non applicatif (Administration)
        'sysadmin'=> array(
            'monarc_api_admin_historicals',
            'monarc_api_admin_servers',
            'monarc_api_admin_users_roles',
        ),
        // Admin comptes : Création des comptes et authentification client
        'accadmin'=> array(
            'monarc_api_admin_password',
            'monarc_api_admin_users',
            'monarc_api_admin_users_roles',
            'monarc_api_clients',
        ),
    )
);
