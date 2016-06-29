<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZF\ApiProblem\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use ZF\ApiProblem\View\ApiProblemRenderer;

class ApiProblemRendererFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     * @return ApiProblemRenderer
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config            = $container->get('Config');
        $displayExceptions = isset($config['view_manager'])
            && isset($config['view_manager']['display_exceptions'])
            && $config['view_manager']['display_exceptions'];

        $renderer = new ApiProblemRenderer();
        $renderer->setDisplayExceptions($displayExceptions);

        return $renderer;
    }
}
