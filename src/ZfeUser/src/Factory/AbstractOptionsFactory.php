<?php

namespace ZfeUser\Factory;

use Interop\Container\ContainerInterface;
use ReflectionClass;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

class AbstractOptionsFactory implements AbstractFactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {

        // Return the requested class and inject its dependencies
        $config= $container->get('config');

        return new $requestedName($config['zfe-user']);
    }

    public function canCreate(ContainerInterface $container, $requestedName)
    {
        // Only accept Action classes
        if (substr($requestedName, -7) == 'Options') {
            return true;
        }

        return false;
    }
}
