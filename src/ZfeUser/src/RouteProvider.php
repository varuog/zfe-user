<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ZfeUser;

use ZfeUser\Action;
use Psr\Container\ContainerInterface;
use Zend\Expressive\Application;
use Zend\Expressive\Helper\ServerUrlMiddleware;
use Zend\Expressive\Helper\UrlHelperMiddleware;
use Zend\Expressive\Middleware\ImplicitHeadMiddleware;
use Zend\Expressive\Middleware\ImplicitOptionsMiddleware;
use Zend\Expressive\Middleware\NotFoundHandler;
use Zend\Stratigility\Middleware\ErrorHandler;

/**
 * Description of RoutesDelegator
 * specify roots here
 * @author Gourav Sarkar
 */
class RouteProvider {

    /**
     * @param ContainerInterface $container
     * @param string $serviceName Name of the service being created.
     * @param callable $callback Creates and returns the service.
     * @return Application
     */
    public function __invoke(ContainerInterface $container, $serviceName, callable $callback) {
        /** @var $app Application */
        $app = $callback();

        /**
         * Pipelines
         */
        $app->pipe(\Zend\Expressive\Helper\BodyParams\BodyParamsMiddleware::class);
        $app->pipe(Middleware\JsonApiDispatcherMiddleware::class);

        // Setup routes:
        $app->post('/auth/register', [Action\User\UserRegisterAction::class], 'user.register');
        $app->post('/auth/login', [Action\User\UserLoginAction::class], 'user.login');
        $app->get('/user/:slug'
                , [Middleware\AuthValidatorMiddleware::class, Action\User\UserFetchAction::class]
                , 'user.fetch');
        $app->patch('/user/:slug/assign-role', [Action\User\UserAssignRoleAction::class], 'user.assign-role');
        $app->patch('/user/:slug/revoke-role', [Action\User\UserRevokeRoleAction::class], 'user.revoke-role');
        $app->patch('/user/:slug/activation', [Action\User\UserActivationAction::class], 'user.activation');

        $app->patch('/user/:slug/trash', [Action\User\UserFetchAction::class], 'user.trash');


        /**
         * Role Routes
         */
        $app->post('/role/add', [Action\Role\RoleAddAction::class], 'role.add');
        $app->post('/role/[:role]', [Action\Role\RoleFetchAction::class], 'role.fetch');

        return $app;
    }

}
