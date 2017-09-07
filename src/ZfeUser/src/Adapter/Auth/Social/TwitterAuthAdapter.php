<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ZfeUser\Adapter\Auth\Social;

use Facebook\Facebook;
use ZfeUser\Options\UserServiceOptions;
use Doctrine\ODM\MongoDB\DocumentManager;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\Authentication\Adapter\AdapterInterface;
use ZfeUser\Model\User;
use Zend\Authentication\Result;
use Zend\Expressive\Helper\UrlHelper;
use Zend\Expressive\Helper\ServerUrlHelper;
use ZfeUser\Model\Role;
use ZfeUser\Adapter\Auth\AbstractAuthAdapter;
use Doctrine\ODM\MongoDB\Id\UuidGenerator;
use ZfeUser\Model\Social;
use Abraham\TwitterOAuth\TwitterOAuth;
use ZfeUser\Factory\Social\SocialAuthAdapterFactory;
use Abraham\TwitterOAuth\TwitterOAuthException;

/**
 * Description of FacebookAuthAdapter
 * @todo introduce zend-session. instead of calling session_start directly
 * @author gourav sarkar
 */
class TwitterAuthAdapter extends AbstractAuthAdapter implements SocialAuthAdapterInterface
{

    private $urlHelper;
    private $serverHelper;
    private $twitterHandler;
    private $oauthToken;
    private $oauthSecret;

    public function __construct(UserServiceOptions $options, DocumentManager $persistantManager, TranslatorInterface $translator, UrlHelper $urlHelper, ServerUrlHelper $serverHelper)
    {
        parent::__construct($options, $persistantManager, $translator);
        $this->urlHelper = $urlHelper;
        $this->serverHelper = $serverHelper;

        $fbSocialOption = $this->options->getSocial()['twitter'];
        $this->twitterHandler = new TwitterOAuth($fbSocialOption['appID'], $fbSocialOption['appSecret']);
    }

    public function getHandler(): Facebook
    {
        return $this->twitterHandler;
    }

    /**
     * @todo make email field mandatory, resend request
     * @todo fix result type in return
     * @return Result
     */
    public function authenticate(): Result
    {
        $twitterSocialOption = $this->options->getSocial()['twitter'];
        $callbackurl = $this->serverHelper->generate($this->urlHelper->generate('user-social-login'
                        , ['provider' => SocialAuthAdapterFactory::SOCIAL_PROVIDER_TWITTER]));
        try {
            $callbackurl = $this->serverHelper->generate($this->urlHelper->generate('user-social-login'
                            , ['provider' => SocialAuthAdapterFactory::SOCIAL_PROVIDER_TWITTER]));
            $request_token = $this->twitterHandler->oauth('oauth/request_token', array('oauth_callback' => $callbackurl));
            var_dump($request_token);
            $access_token = $this->twitterHandler->oauth("oauth/access_token", ["oauth_verifier" => $_REQUEST['oauth_verifier']]);
            $this->twitterHandler->setOauthToken($request_token['oauth_token'], $request_token['oauth_token_secret']);
            $access_token = $this->twitterHandler->oauth("oauth/access_token", ["oauth_verifier" => $_REQUEST['oauth_verifier']]);
            var_dump($access_token);
            $this->twitterHandler->setOauthToken($access_token['oauth_token'], $access_token['oauth_token_secret']);

            $d = $this->twitterHandler->oauth('account/verify_credentials');
            var_dump($d);
        } catch (TwitterOAuthException $exp) {
            var_dump($exp->getMessage());
        }

        return new Result(Result::FAILURE_UNCATEGORIZED, null, ['Unknow Error']);
    }

    /**
     * 
     * @return type
     */
    public function getSocialLoginLink()
    {
        $callbackurl = $this->serverHelper->generate($this->urlHelper->generate('user-social-login'
                        , ['provider' => SocialAuthAdapterFactory::SOCIAL_PROVIDER_TWITTER]));
        $request_token = $this->twitterHandler->oauth('oauth/request_token', array('oauth_callback' => $callbackurl));
        return $this->twitterHandler->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));
    }

    public function getSocialLogOutLink()
    {
        
    }

    public function createUser($responseData): User
    {
        $newUser = new User();
        $newUser->setId(UuidGenerator::generateV4());
        $newUser->setEmail($responseData->getEmail());
        $newUser->setFullName($responseData->getName());

        $newUser->setUsername(explode('@', $responseData->getEmail())[0]);
        $newUser->setSlug(explode('@', $responseData->getEmail())[0]);
        $newUser->setPassword(bin2hex(random_bytes(10)));
        $newUser->addRole(new Role('user'));
        $newUser->addSocial($social);

        return $newUser;
    }

}
