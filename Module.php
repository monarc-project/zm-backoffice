<?php
namespace MonarcBO;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use \Zend\Mvc\Controller\ControllerManager;
use Zend\View\Model\JsonModel;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

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
                '\MonarcBO\Model\Entity\User' => '\MonarcBO\Model\Entity\User',
            ),
            'factories' => array(
                '\MonarcBO\Model\Db' => function($serviceManager){
                    return new \MonarcCore\Model\Db($serviceManager->get('doctrine.entitymanager.orm_default'));
                },
                '\MonarcBO\Model\Table\UserTable' => function($sm){
                    return new Model\Table\UserTable($sm->get('\MonarcBO\Model\Db'));
                },
                '\MonarcBO\Service\UserService' => '\MonarcBO\Service\UserServiceFactory',
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

}
