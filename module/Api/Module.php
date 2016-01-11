<?php

namespace Api;

use Api\Entity\User;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Api\Service\UserService;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\JsonModel;

class Module implements AutoloaderProviderInterface, ServiceProviderInterface, ConfigProviderInterface
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'onDispatchError'));
        $eventManager->attach(MvcEvent::EVENT_RENDER_ERROR, array($this, 'onRenderError'));
    }

    public function onDispatchError(MvcEvent $e)
    {
        return $this->getJsonModelError($e);
    }

    public function onRenderError($e)
    {
        return $this->getJsonModelError($e);
    }

    public function getJsonModelError(MvcEvent $e)
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
                'message' => $exception->getMessage(),
            );
        }
        $errorJson = array(
            'status' => false,
            'message' => 'An error occurred during execution; please try again later.',
            'data' => $error,
            'exception' => $exceptionJson,
        );
        if ($error == 'error-router-no-match') {
            $errorJson['message'] = 'Resource not found.';
        }
        $model = new JsonModel(array('errors' => array($errorJson)));
        $e->setResult($model);
        return $model;
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig()
    {
        return array(
            'invokables' => array(
                'api_user_service' => UserService::class,

            ),
            'factories' => array(
                'api_user_entity' => function ($sm) {
                    $entity = new User();
                    $entity->setDbAdaptor($sm->get('Zend\Db\Adapter\Adapter'));
                    return $entity;
                },
            ),
        );
    }


}