<?php
namespace MonarcBO;

return array(
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'MonarcBO\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),

            'monarc' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/api/admin/users[/:id]',
                    'constraints' => array(
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'MonarcBO\Controller\ApiAdminUsers',
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
);
