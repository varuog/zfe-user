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
use ZfeUser\Model\Role;

/**
 * Description of UserHydrator
 *
 * @author Gourav Sarkar
 */
class RoleHydrator extends AbstractHydrator
{

    protected function generateId(): string
    {
        //$f = \Doctrine\ODM\MongoDB\Id\UuidGenerator::generateV4();
        return \Doctrine\ODM\MongoDB\Id\UuidGenerator::generateV4();
    }

    protected function getAcceptedTypes(): array
    {
        $parts = explode('\\', Role::class);
        return [end($parts), Role::class];
    }

    /**
     *
     * @param Role $domainObject
     */
    protected function getAttributeHydrator($domainObject): array
    {
        return [
        ];
    }

    /**
     *
     * @param Role $domainObject
     */
    protected function getRelationshipHydrator($domainObject): array
    {
        return [];
    }

    /**
     *
     * @param Role $domainObject
     * @param string $id
     */
    protected function setId($domainObject, String $id)
    {
        $domainObject->setName($id);
    }

    /**
     *
     * @param string $clientGeneratedId
     * @param \WoohooLabs\Yin\JsonApi\Request\RequestInterface $request
     * @param \WoohooLabs\Yin\JsonApi\Exception\ExceptionFactoryInterface $exceptionFactory
     */
    protected function validateClientGeneratedId(
    string $clientGeneratedId, RequestInterface $request, ExceptionFactoryInterface $exceptionFactory
    ): void
    {
        
    }

    /**
     * @todo validate request to filter out sensitive but conditional data
     * @param \WoohooLabs\Yin\JsonApi\Request\RequestInterface $request
     */
    protected function validateRequest(RequestInterface $request): void
    {
        
    }

}
