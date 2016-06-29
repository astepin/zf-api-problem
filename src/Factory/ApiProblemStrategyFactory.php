<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZF\ApiProblem\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use ZF\ApiProblem\View\ApiProblemStrategy;

class ApiProblemStrategyFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     * @return ApiProblemStrategy
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new ApiProblemStrategy($container->get('ZF\ApiProblem\ApiProblemRenderer'));
    }
}
