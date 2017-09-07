<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ZfeUser\Factory\Social;
use Interop\Container\ContainerInterface;
/**
 * Description of AbstractSocialAuthAdapterFactory
 *
 * @author LaptopRK
 */
class SocialAuthAdapterFactoryFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $fbAuthAdapter= $container->get(\ZfeUser\Adapter\Auth\Social\FacebookAuthAdapter::class);
        $twitterAuthAdapter= $container->get(\ZfeUser\Adapter\Auth\Social\TwitterAuthAdapter::class);
        return new SocialAuthAdapterFactory($fbAuthAdapter, $twitterAuthAdapter);
    }
}
