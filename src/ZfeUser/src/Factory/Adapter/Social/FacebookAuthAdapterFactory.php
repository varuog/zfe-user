<?php

namespace ZfeUser\Factory\Adapter\Social;

use Interop\Container\ContainerInterface;
use ReflectionClass;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;
use ZfeUser\Adapter\Auth\Social\FacebookAuthAdapter;

class FacebookAuthAdapterFactory {

    public function __invoke(ContainerInterface $container) {
        $options = $container->get(\ZfeUser\Options\UserServiceOptions::class);
        $persistManager = $container->get(\Doctrine\ODM\MongoDB\DocumentManager::class);
        $translator = $container->get(\Zend\I18n\Translator\TranslatorInterface::class);
        $urlHelper = $container->get(\Zend\Expressive\Helper\UrlHelper::class);
        $serverHelper = $container->get(\Zend\Expressive\Helper\ServerUrlHelper::class);


        return new FacebookAuthAdapter($options, $persistManager, $translator, $urlHelper, $serverHelper);
    }

}
