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
        if(!$e->getRequest() instanceof \Zend\Console\Request){
            $eventManager = $e->getApplication()->getEventManager();
            $moduleRouteListener = new ModuleRouteListener();
            $moduleRouteListener->attach($eventManager);
            $this->initRbac($e);

            $eventManager->attach(MvcEvent::EVENT_ROUTE, array($this, 'checkRbac'), 0);
            $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'onDispatchError'), 0);
            $eventManager->attach(MvcEvent::EVENT_RENDER_ERROR, array($this, 'onRenderError'), 0);
        }
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
                //'\MonarcBO\Model\Entity\Server' => '\MonarcBO\Model\Entity\Server',
                //'\MonarcBO\Model\Entity\Client' => '\MonarcBO\Model\Entity\Client',
            ),
            'factories' => array(
               '\MonarcCli\Model\Db' => function($sm){
                    try{
                        $sm->get('doctrine.entitymanager.orm_cli')->getConnection()->connect();
                        return new \MonarcCore\Model\Db($sm->get('doctrine.entitymanager.orm_cli'));
                    }catch(\Exception $e){
                        return new \MonarcCore\Model\Db($sm->get('doctrine.entitymanager.orm_default'));
                    }
                },

                // Servers table
                '\MonarcBO\Model\Table\ServerTable' => function($sm){
                    return new Model\Table\ServerTable($sm->get('\MonarcCli\Model\Db'));
                },
                '\MonarcBO\Model\Entity\Server' => function($sm){
                    $s = new Model\Entity\Server();
                    $s->setDbAdapter($sm->get('\MonarcCli\Model\Db'));
                    return $s;
                },
                '\MonarcBO\Service\ServerService' => '\MonarcBO\Service\ServerServiceFactory',

                // Clients table
                '\MonarcBO\Model\Table\ClientTable' => function($sm){
                    return new Model\Table\ClientTable($sm->get('\MonarcCli\Model\Db'));
                },
                '\MonarcBO\Model\Entity\Client' => function($sm){
                    $c = new Model\Entity\Client();
                    $c->setDbAdapter($sm->get('\MonarcCli\Model\Db'));
                    return $c;
                },
                '\MonarcBO\Service\ClientService' => '\MonarcBO\Service\ClientServiceFactory',
            ),
        );
    }

    public function getValidatorConfig(){
        return array(
            'invokables' => array(
                '\MonarcBO\Validator\UniqueClientProxyAlias' => '\MonarcBO\Validator\UniqueClientProxyAlias',
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

            if ($exception->getCode() >= 400 && $exception->getCode() < 600) {
                $e->getResponse()->setStatusCode($exception->getCode());
            }
        }
        $errorJson = array(
            'message'   => $exception ? $exception->getMessage() : 'An error occurred during execution; please try again later.',
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

        $globalPermissions = isset($config['permissions'])?$config['permissions']:array();

        $rolesPermissions = isset($config['roles'])?$config['roles']:array();

        $rbac = new Rbac();
        foreach ($rolesPermissions as $role => $permissions) {

            $role = new Role($role);

            //global permissions
            foreach($globalPermissions as $globalPermission) {
                if (! $role->hasPermission($globalPermission)) {
                    $role->addPermission($globalPermission);
                }
            }

            //role permissions
            foreach ($permissions as $permission) {
                if (! $role->hasPermission($permission)) {
                    $role->addPermission($permission);
                }
            }

            $rbac->addRole($role);
        }

        //add role for guest (user not logged)
        $role = new Role('guest');
        foreach($globalPermissions as $globalPermission) {
            if (! $role->hasPermission($globalPermission)) {
                $role->addPermission($globalPermission);
            }
        }
        $rbac->addRole($role);

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

        //retrieve connected user
        $sm = $e->getApplication()->getServiceManager();
        $connectedUserService = $sm->get('\MonarcCore\Service\ConnectedUserService');
        $connectedUser = $connectedUserService->getConnectedUser();

        //retrieve user roles
        $userRoleService = $sm->get('\MonarcCore\Service\UserRoleService');
        $userRoles = $userRoleService->getList(1, 25, null, $connectedUser['id']);

        $roles = [];
        foreach($userRoles as $userRole) {
            $roles[] = $userRole['role'];
        }

        if (empty($roles)) {
            $roles[] = 'guest';
        }

        $isGranted = false;
        foreach($roles as $role) {
            if ($e->getViewModel()->rbac->isGranted($role, $route)) {
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
