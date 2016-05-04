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
                    'route' => '/api/admin/passwords[/:id]',
                    'constraints' => array(
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'MonarcBO\Controller\ApiAdminPasswords',
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
            'Monarc_bo_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/MonarcBO/Model/Entity'),
            ),
            'orm_default' => array(
                'drivers' => array(
                    'MonarcBO\Model\Entity' => 'Monarc_bo_driver',
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
        'superadmin'=> array(
        ),
        'dbadmin'=> array(
            'monarc_api_models',
            'monarc_api_assets',
            'monarc_api_vulnerabilities',
        ),
        'sysadmin'=> array(
            'monarc_api_admin_servers',
        ),
        'accadmin'=> array(
            'monarc_api_admin_users',
            'monarc_api_clients'
        ),
    )
);
