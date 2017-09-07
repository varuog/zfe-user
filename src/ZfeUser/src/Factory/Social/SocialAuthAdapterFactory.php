<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ZfeUser\Factory\Social;
use ZfeUser\Model\Social;
use ZfeUser\Adapter\Auth\Social\FacebookAuthAdapter;
use ZfeUser\Adapter\Auth\Social\SocialAuthAdapterInterface;
/**
 * Description of AbstractSocialAuthAdapterFactory
 *
 * @author LaptopRK
 */
class SocialAuthAdapterFactory
{
    private $fbAuthAdapter;
    
    public function __construct(FacebookAuthAdapter $fbAuthAdapter)
    {
        $this->fbAuthAdapter= $fbAuthAdapter;
    }
    
    public function build($providerName) : SocialAuthAdapterInterface
    {
        switch ($providerName)
        {
            case Social::SOCIAL_PROVIDER_FACEBOOK:
                return $this->fbAuthAdapter;
        }
    }
}
