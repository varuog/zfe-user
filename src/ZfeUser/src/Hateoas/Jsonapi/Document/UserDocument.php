<?php

namespace ZfeUser\Hateoas\Jsonapi\Document;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use ZfeUser\hateoas\jsonapi\Transformer;
use WoohooLabs\Yin\JsonApi\Schema\JsonApiObject;
use WoohooLabs\Yin\JsonApi\Document\AbstractSingleResourceDocument;

/**
 * Description of User
 *
 * @author Gourav Sarkar
 */
class UserDocument extends AbstractSingleResourceDocument
{

    private $accessToken;

    public function __construct(Transformer\UserTransformer $userTransformer)
    {
        parent::__construct($userTransformer);
    }

    public function getJsonApi()
    {
        return new JsonApiObject("1.0");
    }

    public function getLinks()
    {
        
    }

    public function getMeta(): array
    {
        $meta=[];
        $accessToken= $this->getAccessToken;
        $metaAccessToken=isset($accessToken) ?  ['accessToken' => $accessToken] : [];
        return array_merge($meta, $metaAccessToken);
    }

    public function getAccessToken()
    {
        return $this->accessToken;
    }

    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
        return $this;
    }

}
