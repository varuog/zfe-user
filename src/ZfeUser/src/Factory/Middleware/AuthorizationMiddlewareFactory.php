<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ZfeUser\Factory\Middleware;

use Interop\Container\ContainerInterface;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Doctrine\ODM\MongoDB\DocumentManager;

/**
 * Description of MongoDocumentManagerFactory
 * @todo Consider removing factory, instead use abstractFactories
 * @author Gourav Sarkar
 */
class AuthorizationMiddlewareFactory
{

    public function __invoke(ContainerInterface $container)
    {
        $roleService = $container->get(\ZfeUser\Service\RoleService::class);
        $router = $container->get(\Zend\Expressive\Router\RouterInterface::class);
        return new \ZfeUser\Middleware\AuthorizationMiddleware($roleService, $router);
    }

}
