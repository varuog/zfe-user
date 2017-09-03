<?php

namespace ZfeUser\Action;

use Interop\Container\ContainerInterface;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class HomePageFactory {

    public function __invoke(ContainerInterface $container) {
        $urlHelper = $container->get(\Zend\Expressive\Helper\UrlHelper::class);
        $serverHelper = $container->get(\Zend\Expressive\Helper\ServerUrlHelper::class);
        $router = $container->get(RouterInterface::class);
        $template = $container->has(TemplateRendererInterface::class) ? $container->get(TemplateRendererInterface::class) : null;
        $fbAuthAdapter = $container->get(\ZfeUser\Adapter\Auth\Social\FacebookAuthAdapter::class);
        return new HomePageAction($router, $fbAuthAdapter, $urlHelper, $serverHelper, $template);
    }

}
