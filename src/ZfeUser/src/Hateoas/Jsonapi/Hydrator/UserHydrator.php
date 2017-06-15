<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ZfeUser\Hateoas\Jsonapi\Hydrator;

use WoohooLabs\Yin\JsonApi\Hydrator\AbstractHydrator;
use WoohooLabs\Yin\JsonApi\Request\RequestInterface;
use WoohooLabs\Yin\JsonApi\Exception\ExceptionFactoryInterface;
use ZfeUser\Model\User;

/**
 * Description of UserHydrator
 *
 * @author Gourav Sarkar
 */
class UserHydrator extends AbstractHydrator {

    protected function generateId(): string {
        return Uuid::generate();
    }

    protected function getAcceptedTypes(): array {
        return [User::class];
    }

    /**
     * 
     * @param User $domainObject
     */
    protected function getAttributeHydrator($domainObject): array {
        return [
            "userName" => function (User $domainObject, $attribute, $data, $attributeName) {
                $domainObject->setUsername($attribute);
            },
            "email" => function (User $domainObject, $attribute, $data, $attributeName) {
                $domainObject->setEmail($attribute);
            },
            "fullName" => function (User $domainObject, $attribute, $data, $attributeName) {
                $domainObject->setFullName($attribute);
            },
            "password" => function (User $domainObject, $attribute, $data, $attributeName) {
                $domainObject->setPassword($attribute);
            }
        ];
    }

    /**
     * 
     * @param User $domainObject
     */
    protected function getRelationshipHydrator($domainObject): array {
        return [];
    }

    /**
     * 
     * @param User $domainObject
     * @param string $id
     */
    protected function setId($domainObject,String $id) {
        $domainObject->setId($id);
    }

    /**
     * 
     * @param string $clientGeneratedId
     * @param \WoohooLabs\Yin\JsonApi\Request\RequestInterface $request
     * @param \WoohooLabs\Yin\JsonApi\Exception\ExceptionFactoryInterface $exceptionFactory
     */
    protected function validateClientGeneratedId(string $clientGeneratedId, RequestInterface $request, ExceptionFactoryInterface $exceptionFactory): void {
        
    }

    /**
     * @todo validate request to filter out sensitive but conditional data
     * @param \WoohooLabs\Yin\JsonApi\Request\RequestInterface $request
     */
    protected function validateRequest(RequestInterface $request): void {
    }

}
