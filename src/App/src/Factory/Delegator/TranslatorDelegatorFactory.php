<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Factory\Delegator;

use Psr\Container\ContainerInterface;

class TranslatorDelegatorFactory
{
    /**
     * @param ContainerInterface $container
     * @param string $name
     * @param callable $callback
     * @return UserRepository
     */
    public function __invoke(ContainerInterface $container, $name, callable $callback)
    {
        $repository = $callback();
        return $repository;
    }
}