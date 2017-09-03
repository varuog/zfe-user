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
        //$app->pipe(Middleware\AuthValidatorMiddleware::class);

        // Setup routes:
        $app->post('/api/auth/register', [Action\Api\User\UserRegisterAction::class], 'user-register');
        $app->post('/api/auth/login', [Action\Api\User\UserLoginAction::class], 'user-login');
        $app->get('/api/auth/social/login', [Action\Api\User\UserSocialLoginAction::class], 'user-social-login');
        
        $app->get('/api/user/:slug'
                , [Middleware\AuthValidatorMiddleware::class, Middleware\AuthorizationMiddleware::class, Action\Api\User\UserFetchAction::class]
                , 'user.fetch');
        $app->patch('/api/user/:slug/assign-role', [Action\Api\User\UserAssignRoleAction::class], 'user-assign-role');
        $app->patch('/api/user/:slug/revoke-role', [Action\Api\User\UserRevokeRoleAction::class], 'user-revoke-role');
        $app->patch('/api/user/:slug/activation', [Action\Api\User\UserActivationAction::class], 'user-activation');

        $app->patch('/api/user/:slug/trash', [Action\Api\User\UserFetchAction::class], 'user-trash');


        /**
         * Role Routes
         */
        $app->post('/api/role/add', [Action\Api\Role\RoleAddAction::class], 'role-add');
        $app->post('/api/role/[:role]', [Action\Api\Role\RoleFetchAction::class], 'role-fetch');

        return $app;
    }

}
