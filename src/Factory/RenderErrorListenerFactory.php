<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZF\ApiProblem\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use ZF\ApiProblem\Listener\RenderErrorListener;

class RenderErrorListenerFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     * @return RenderErrorListener
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config            = $container->get('Config');
        $displayExceptions = false;

        if (isset($config['view_manager'])
            && isset($config['view_manager']['display_exceptions'])
        ) {
            $displayExceptions = (bool) $config['view_manager']['display_exceptions'];
        }

        $listener = new RenderErrorListener();
        $listener->setDisplayExceptions($displayExceptions);

        return $listener;
    }
}
