<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ZfeUser\Middleware;

use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Interop\Http\ServerMiddleware\DelegateInterface;
use \Psr\Http\Message\ResponseInterface;
use WoohooLabs\Yin\JsonApi\Document\ErrorDocument;
use ZfeUser\Service\RoleService;
use WoohooLabs\Yin\JsonApi\Schema\JsonApiObject;
use WoohooLabs\Yin\JsonApi\Schema\Error;
use ZfeUser\Middleware\JsonApiDispatcherMiddleware;
use ZfeUser\Model\User;

/**
 * check if user has permission or not to access this route
 * @author gourav sarkar
 */
class AuthorizationMiddleware implements MiddlewareInterface
{

    private $roleService;
    private $router;

    public function __construct(RoleService $roleService, \Zend\Expressive\Router\RouterInterface $router)
    {
        $this->roleService = $roleService;
        $this->router = $router;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate): ResponseInterface
    {
        $authStringParts = [];
        $jsonApi = $request->getAttribute(JsonApiDispatcherMiddleware::JSON_API_PROC);
        $currentUser = $request->getAttribute(AuthValidatorMiddleware::CURRENT_USER, new User());

        /**
         * Check request validation
         */
        $routeName = $this->router->match($request)->getMatchedRoute()->getName();
        $roles=$currentUser->getRoles();
        if ($this->roleService->isGranted($roles, $routeName)) {
            return $delegate->process($request);
        }



        $errorDoc = new ErrorDocument();
        $errorDoc->setJsonApi(new JsonApiObject("1.0"));

        $error = new Error();
        $error->setTitle('Unathorised access');

        return $jsonApi->respond()->forbidden($errorDoc);
    }
}
