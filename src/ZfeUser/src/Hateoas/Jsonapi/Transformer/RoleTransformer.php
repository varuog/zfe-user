<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ZfeUser\Hateoas\Jsonapi\Transformer;

use WoohooLabs\Yin\JsonApi\Transformer\AbstractResourceTransformer;
use Zend\Permissions\Rbac\Role;
use WoohooLabs\Yin\JsonApi\Schema\Relationship\ToOneRelationship;
use WoohooLabs\Yin\JsonApi\Schema\Link;
use WoohooLabs\Yin\JsonApi\Schema\Links;
use Zend\Expressive\Helper\UrlHelper;

/**
 * Description of User
 *
 * @author Gourav Sarkar
 */
class RoleTransformer extends AbstractResourceTransformer {

    private $uriHelper;
    
     public function __construct(UrlHelper $urihelper)
    {
        $this->uriHelper= $urihelper;
    }
    /**
     *
     * @param Model\Role $domainObject
     * @todo approveTime need to to be fixed for null values. check default
     *  value for same attribute in user model
     * @return array
     */
    public function getAttributes($domainObject): array
    {
        return [
            "name" => function (Role $domainObject) {
                return $domainObject->getName();
            },
            "permissions" => function (Role $domainObject) {
                return $domainObject->getPermissions();
            },
        ];
    }

    /**
     *
     * @param Model\Role $domainObject
     * @return array
     */
    public function getDefaultIncludedRelationships($domainObject): array
    {
        return [];
    }

    /**
     *
     * @param Model\Role $domainObject
     */
    public function getId($domainObject): string {
        return $domainObject->getName();
    }

    /**
     *
     * @param Model\Role $domainObject
     */
    public function getLinks($domainObject) {
        
    }

    /**
     *
     * @param Model\Role $domainObject
     */
    public function getMeta($domainObject): array {
        return [];
    }

    /**
     *
     * @param Model\Role $domainObject
     */
    public function getRelationships($domainObject): array {
        return [
            "parent" => function ($domainObject) {
                return ToOneRelationship::create()
                                ->setLinks(
                                        Links::createWithoutBaseUri()->setSelf(new Link($this->uriHelper->generate('role.fetch', ['role' => $domainObject->getParent()->getName()])))
                                )
                                ->setData($domainObject->getParent(), $this);
            }
        ];
    }

    /**
     *
     * @param Model\Role $domainObject
     */
    public function getType($domainObject): string {
        $parts = explode('\\', get_class($domainObject));
        return end($parts);
    }

}
