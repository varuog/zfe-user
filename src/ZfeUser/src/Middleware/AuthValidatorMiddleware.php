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
use WoohooLabs\Yin\JsonApi\JsonApi;
use ZfeUser\Service\UserService;
use WoohooLabs\Yin\JsonApi\Exception\DefaultExceptionFactory;
use WoohooLabs\Yin\JsonApi\Request\Request as JsonApiRequest;
use Zend\Diactoros\Response;
use WoohooLabs\Yin\JsonApi\Schema\JsonApiObject;
use WoohooLabs\Yin\JsonApi\Schema\Error;

/**
 * Description of JsonApiResponseMiddleware
 * @todo incomplete implementation
 * @author gourav sarkar
 */
class AuthValidatorMiddleware implements MiddlewareInterface
{

    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate): ResponseInterface
    {
        $authStringParts = [];
        $defaultExpFactory = new DefaultExceptionFactory();

        $jsonapiRequest = new JsonApiRequest($request, $defaultExpFactory);
        $jsonApi = new JsonApi($jsonapiRequest, new Response(), $defaultExpFactory, null);


        $authString = $request->getHeader('Authorization');
        if (!empty($authString)) {
            $bearerPosition=strpos($authString[0], 'Bearer ') + strlen('Bearer');
            $authToken = trim(substr($authString[0], $bearerPosition, strlen($authString[0])));
        }


        $currentUser = $this->userService->isValidAuthToken($authToken);

        if ($currentUser != null) {
            return $delegate->process($request);
        }



        $errorDoc = new ErrorDocument();
        $errorDoc->setJsonApi(new JsonApiObject("1.0"));

        $error = new Error();
        $error->setTitle('Unathorised access');

        return $jsonApi->respond()->forbidden($errorDoc);
    }
}
