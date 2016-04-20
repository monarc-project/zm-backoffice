<?php
namespace MonarcBO;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Permissions\Rbac\Rbac;
use Zend\Permissions\Rbac\Role;
use Zend\View\Model\JsonModel;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $this->initRbac($e);

        $eventManager->attach(MvcEvent::EVENT_ROUTE, array($this, 'checkRbac'), 0);
        $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'onDispatchError'), 0);
        $eventManager->attach(MvcEvent::EVENT_RENDER_ERROR, array($this, 'onRenderError'), 0);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getServiceConfig()
    {
        return array(
            'invokables' => array(
                '\MonarcBO\Model\Entity\Server' => '\MonarcBO\Model\Entity\Server',
            ),
            'factories' => array(
                '\MonarcBO\Model\Db' => function($serviceManager){
                    return new \MonarcCore\Model\Db($serviceManager->get('doctrine.entitymanager.orm_default'));
                },

                // Servers table
                '\MonarcBO\Model\Table\ServerTable' => function($sm){
                    return new Model\Table\ServerTable($sm->get('\MonarcBO\Model\Db'));
                },
                '\MonarcBO\Service\ServerService' => '\MonarcBO\Service\ServerServiceFactory',
            ),
        );
    }
    public function getControllerConfig()
    {
        return array(
            'invokables' => array(
                '\MonarcBO\Controller\Index' => '\MonarcBO\Controller\IndexController',
            ),
            'factories' => array(
                '\MonarcBO\Controller\ApiAdminUsers' => '\MonarcBO\Controller\ApiAdminUsersControllerFactory',
                '\MonarcBO\Controller\ApiAdminServers' => '\MonarcBO\Controller\ApiAdminServersControllerFactory',
            ),
        );
    }

    public function onDispatchError($e)
    {
        return $this->getJsonModelError($e);
    }

    public function onRenderError($e)
    {
        return $this->getJsonModelError($e);
    }

    public function getJsonModelError($e)
    {
        $error = $e->getError();
        if (!$error) {
            return;
        }
        $response = $e->getResponse();
        $exception = $e->getParam('exception');
        $exceptionJson = array();
        if ($exception) {
            $exceptionJson = array(
                'class' => get_class($exception),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'message' => $exception->getMessage(),
                'stacktrace' => $exception->getTraceAsString()
            );
        }
        $errorJson = array(
            'message'   => 'An error occurred during execution; please try again later.',
            'error'     => $error,
            'exception' => $exceptionJson,
        );
        if ($error == 'error-router-no-match') {
            $errorJson['message'] = 'Resource not found.';
        }
        $model = new JsonModel(array('errors' => array($errorJson)));
        $e->setResult($model);
        return $model;
    }

    /**
     * init Rbac
     *
     * @param MvcEvent $e
     */
    public function initRbac(MvcEvent $e)
    {
        $sm = $e->getApplication()->getServiceManager();
        $config = $sm->get('Config');

        $roles = $config['roles'];

        $rbac = new Rbac();
        foreach ($roles as $role => $permissions) {

            $role = new Role($role);
            foreach ($permissions as $permission) {
                if (! $role->hasPermission($permission)) {
                    $role->addPermission($permission);
                }
            }

            $rbac->addRole($role);
        }

        //setting to view
        $e->getViewModel()->rbac = $rbac;

    }

    /**
     * Check Rbac
     * 
     * @param MvcEvent $e
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function checkRbac(MvcEvent $e) {
        $route = $e->getRouteMatch()->getMatchedRouteName();
        $userRoles = ['sysadmin', 'accadmin'];

        $isGranted = false;
        foreach($userRoles as $userRole) {
            if (!$e->getViewModel()->rbac->isGranted($userRole, $route)) {
                $isGranted = true;
            }
        }


        if (! $isGranted) {
            $response = $e->getResponse();
            $response->setStatusCode(401);

            return $response;
        }
    }

}
