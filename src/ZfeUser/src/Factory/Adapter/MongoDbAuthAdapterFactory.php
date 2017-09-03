<?php

namespace ZfeUser\Factory\Adapter;

use Interop\Container\ContainerInterface;
use ReflectionClass;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;
use ZfeUser\Adapter\Auth\MongoDbAuthAdapter;

class MongoDbAuthAdapterFactory
{

    public function __invoke(ContainerInterface $container)
    {
        $options = $container->get(\ZfeUser\Options\UserServiceOptions::class);
        $persistManager= $container->get(\Doctrine\ODM\MongoDB\DocumentManager::class);
        $translator= $container->get(\Zend\I18n\Translator\TranslatorInterface::class);

        return new MongoDbAuthAdapter($options, $persistManager, $translator);
    }
}
