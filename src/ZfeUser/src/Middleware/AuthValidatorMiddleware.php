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
use ZfeUser\Service\UserService;
use WoohooLabs\Yin\JsonApi\Schema\JsonApiObject;
use WoohooLabs\Yin\JsonApi\Schema\Error;
use ZfeUser\Middleware\JsonApiDispatcherMiddleware;
use WoohooLabs\Yin\JsonApi\JsonApi;

/**
 * Description of JsonApiResponseMiddleware
 * @todo incomplete implementation
 * @author gourav sarkar
 */
class AuthValidatorMiddleware implements MiddlewareInterface
{

    const CURRENT_USER='CURRENT_USER';
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate): ResponseInterface
    {
        $authStringParts = [];
        /** @var $jsonapi JsonApi  */
         $jsonApi = $request->getAttribute(JsonApiDispatcherMiddleware::JSON_API_PROC);
                 
        $authString = $request->getHeader('Authorization');
        if (!empty($authString)) {
            $bearerPosition=strpos($authString[0], 'Bearer ') + strlen('Bearer');
            $authToken = trim(substr($authString[0], $bearerPosition, strlen($authString[0])));
        }


        $currentUserToken = $this->userService->isValidAuthToken($authToken);

        if ($currentUserToken != null) {
            $currentUser= new \ZfeUser\Model\User();
            call_user_func([$currentUser, 'set' . $this->userService->getOptions()->getIdentityField()], $currentUserToken->identifier);
            $currentUser=$this->userService->fetchByIdentifier($currentUser);
            
            $request=$request->withAttribute(AuthValidatorMiddleware::CURRENT_USER, $currentUser);
            return $delegate->process($request);
        }



        $errorDoc = new ErrorDocument();
        $errorDoc->setJsonApi(new JsonApiObject("1.0"));

        $error = new Error();
        $error->setTitle('Request access is not authenrticated');
        
        return $jsonApi->respond()->forbidden($errorDoc);
    }
}
