<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ZfeUser\Hateoas\Jsonapi\Transformer;

use WoohooLabs\Yin\JsonApi\Transformer\AbstractResourceTransformer;
use ZfeUser\Model;

/**
 * Description of User
 *
 * @author Gourav Sarkar
 */
class UserTransformer extends AbstractResourceTransformer
{

    private $roleTransformer;

    public function __construct(RoleTransformer $roleTransformer)
    {
        $this->roleTransformer = $roleTransformer;
    }

    /**
     *
     * @param Model\User $domainObject
     * @todo approveTime need to to be fixed for null values. check default
     *  value for same attribute in user model
     * @return array
     */
    public function getAttributes($domainObject): array
    {
        return [
            "userName" => function (Model\User $domainObject) {
                return $domainObject->getUsername();
            },
            "fullName" => function (Model\User $domainObject) {
                return $domainObject->getFullName();
            },
            "email" => function (Model\User $domainObject) {
                return $domainObject->getEmail();
            },
            "slug" => function (Model\User $domainObject) {
                return $domainObject->getSlug();
            },
            "approveTime" => function (Model\User $domainObject) {
                if ($domainObject->getApproveTime() != null) {
                    return $this->toIso8601DateTime($domainObject->getApproveTime());
                }
            },
            "approved" => function (Model\User $domainObject) {
                return $this->toBool($domainObject->getApproved());
            },
            "emailVerified" => function (Model\User $domainObject) {
                return $this->toBool($domainObject->getEmailVerified());
            },
        ];
    }

    /**
     *
     * @param Model\User $domainObject
     * @return array
     */
    public function getDefaultIncludedRelationships($domainObject): array
    {
        return [
        ];
    }

    /**
     *
     * @param Model\User $domainObject
     */
    public function getId($domainObject): string
    {
        return $domainObject->getId();
    }

    /**
     *
     * @param Model\User $domainObject
     */
    public function getLinks($domainObject)
    {
    }

    /**
     *
     * @param Model\User $domainObject
     */
    public function getMeta($domainObject): array
    {
        return [];
    }

    /**
     *
     * @param Model\User $domainObject
     */
    public function getRelationships($domainObject): array
    {
        return [];
    }

    /**
     *
     * @param Model\User $domainObject
     */
    public function getType($domainObject): string
    {
        $parts = explode('\\', get_class($domainObject));
        return end($parts);
    }
}
