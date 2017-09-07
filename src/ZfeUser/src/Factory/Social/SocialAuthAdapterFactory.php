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
use ZfeUser\Adapter\Auth\Social\TwitterAuthAdapter;

/**
 * Description of AbstractSocialAuthAdapterFactory
 * @internal can be used to extend and different providers using delegator factories
 * @author LaptopRK
 */
class SocialAuthAdapterFactory
{

    const SOCIAL_PROVIDER_FACEBOOK = 'facebook';
    const SOCIAL_PROVIDER_TWITTER = 'twitter';
    const SOCIAL_PROVIDER_GOOGLE = 'google';

    private $fbAuthAdapter;
    private $twitterAdapter;

    public function __construct(FacebookAuthAdapter $fbAuthAdapter, TwitterAuthAdapter $twitterAdapter)
    {
        $this->fbAuthAdapter = $fbAuthAdapter;
        $this->twitterAdapter= $twitterAdapter;
    }

    public function build($providerName): SocialAuthAdapterInterface
    {
        switch ($providerName) {
            case self::SOCIAL_PROVIDER_FACEBOOK:
                return $this->fbAuthAdapter;
            case self::SOCIAL_PROVIDER_TWITTER:
                return $this->twitterAdapter;
        }
    }

}
