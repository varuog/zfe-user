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
 *
 * @author Gourav Sarkar
 */
class AuthValidatorMiddlewareFactory
{

    public function __invoke(ContainerInterface $container)
    {
        $userService=$container->get(\ZfeUser\Service\UserService::class);

        return new \ZfeUser\Middleware\AuthValidatorMiddleware($userService);
    }
}
