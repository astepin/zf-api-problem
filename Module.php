<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZF\ApiProblem;

use Zend\Mvc\Application;
use Zend\Mvc\ResponseSender\SendResponseEvent;
use Zend\Mvc\MvcEvent;
use ZF\ApiProblem\Listener\ApiProblemListener;
use ZF\ApiProblem\View\ApiProblemStrategy;

/**
 * ZF2 module
 */
class Module
{
    /**
     * Retrieve autoloader configuration
     *
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return [
            'Zend\Loader\StandardAutoloader' => ['namespaces' => [
                __NAMESPACE__ => __DIR__ . '/src/',
            ]]
        ];
    }

    /**
     * Retrieve module configuration
     *
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * Listener for bootstrap event
     *
     * Attaches a render event.
     *
     * @param  \Zend\Mvc\MvcEvent $e
     */
    public function onBootstrap($e)
    {
        /** @var Application $app */
        $app            = $e->getTarget();
        $serviceManager = $app->getServiceManager();
        $eventManager   = $app->getEventManager();

        /** @var ApiProblemListener $apiProblemListener */
        $apiProblemListener = $serviceManager->get('ZF\ApiProblem\ApiProblemListener');
        $apiProblemListener->attach($eventManager);
        $eventManager->attach(MvcEvent::EVENT_RENDER, [$this, 'onRender'], 100);

        $sendResponseListener = $serviceManager->get('SendResponseListener');
        $sendResponseListener->getEventManager()->attach(
            SendResponseEvent::EVENT_SEND_RESPONSE,
            $serviceManager->get('ZF\ApiProblem\Listener\SendApiProblemResponseListener'),
            -500
        );
    }

    /**
     * Listener for the render event
     *
     * Attaches a rendering/response strategy to the View.
     *
     * @param  \Zend\Mvc\MvcEvent $e
     */
    public function onRender($e)
    {
        /** @var Application $app */
        $app      = $e->getTarget();
        $services = $app->getServiceManager();

        if ($services->has('View')) {
            $view   = $services->get('View');
            $events = $view->getEventManager();

            // register at high priority, to "beat" normal json strategy registered
            // via view manager, as well as HAL strategy.
            /** @var ApiProblemStrategy $apiProblemStrategy */
            $apiProblemStrategy = $services->get('ZF\ApiProblem\ApiProblemStrategy');
            $apiProblemStrategy->attach($events, 400);
        }
    }
}
