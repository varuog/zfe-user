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

/**
 * Description of JsonApiResponseMiddleware
 * @todo incomplete implementation
 * @author gourav sarkar
 */
class AuthValidatorMiddleware implements MiddlewareInterface {
 
    private $userService;
    public function __construct(UserService $userService) {
        $this->userService= $userService;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate): ResponseInterface {

        $document=$request->getAttribute(static::ATTR_RESPONSE_DOCUMENT);
        $statusCode=$request->getAttribute(static::ATTR_RESPONSE_STATUS_CODE);
        $data=$request->getAttribute(static::ATTR_RESPONSE_DATA);
        
        if ($document instanceof ErrorDocument) {
            /** @var ErrorDocument $document; */
            $document->setJsonApi(new JsonApiObject("1.0"));
            return $this->jsonApi->respond()->genericError($document, $data, $statusCode);
            
        } else {
           
        }
    }

}
