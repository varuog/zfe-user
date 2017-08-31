<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ZfeUser\Middleware;

use Interop\Container\ContainerInterface;
use WoohooLabs\Yin\JsonApi\Exception\DefaultExceptionFactory;
use WoohooLabs\Yin\JsonApi\JsonApi;
use WoohooLabs\Yin\JsonApi\Request\Request;
use Zend\Diactoros\Response;
use WoohooLabs\Yin\JsonApi\Request\Request as JsonApiRequest;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Interop\Http\ServerMiddleware\DelegateInterface;

/**
 * Description of MongoDocumentManagerFactory
 * @todo Database name should be overridable
 * @author Gourav Sarkar
 */
class JsonApiDispatcherMiddleware implements MiddlewareInterface
{
    const JSON_API_PROC='JSON_API_PROC';
    
    public function process(ServerRequestInterface $request, DelegateInterface $delegate): ResponseInterface
    {
        
       $defaultExpFactory   = new DefaultExceptionFactory();
        $jsonApiRequest      = new JsonApiRequest($request, $defaultExpFactory);
        $jsonApi             = new \WoohooLabs\Yin\JsonApi\JsonApi($jsonApiRequest, new \Zend\Diactoros\Response(), $defaultExpFactory, null);
        
        $jsonApi->setRequest($jsonApiRequest);
        $request=$request->withAttribute(JsonApiDispatcherMiddleware::JSON_API_PROC, $jsonApi);
        
        
        $actionHandler=$delegate->process($request);
        
        return $actionHandler;
    }

}
