<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ZfeUser\Factory\Hateoas;

use Interop\Container\ContainerInterface;
use WoohooLabs\Yin\JsonApi\Exception\DefaultExceptionFactory;
use WoohooLabs\Yin\JsonApi\JsonApi;
use WoohooLabs\Yin\JsonApi\Request\Request;
use Zend\Http\Response;

/**
 * Description of MongoDocumentManagerFactory
 * @todo Database name should be overridable
 * @author Gourav Sarkar
 */
class JsonApiFactory
{

    public function __invoke(ContainerInterface $container)
    {
        $request = $container->get('Request');
        $defaultExpFactory = new DefaultExceptionFactory();
        $jsonapi = new JsonApi(new Request($request, $defaultExpFactory), new Response(), $defaultExpFactory);


        return $jsonapi;
    }

}
