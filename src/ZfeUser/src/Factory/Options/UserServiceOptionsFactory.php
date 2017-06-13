<?php

namespace ZfeUser\Factory;

use Interop\Container\ContainerInterface;
use ReflectionClass;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

class UserServiceOptionsFactory {

    public function __invoke(ContainerInterface $container) {
        $config = $container->get('config');

        return new $requestedName($config['zfe-user']);
    }

}
