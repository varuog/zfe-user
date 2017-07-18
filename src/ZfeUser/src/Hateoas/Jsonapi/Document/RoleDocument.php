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
class RoleDocument extends AbstractSingleResourceDocument {

    public function __construct( Transformer\RoleTransformer $roleTransformer) {
        parent::__construct($roleTransformer);
    }

    public function getJsonApi() {
         return new JsonApiObject("1.0");
    }

    public function getLinks() {
        
    }

    public function getMeta(): array {
        return [];
    }

}
