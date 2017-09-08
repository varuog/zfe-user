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
    private $accessToken;
    private $accessTokenSecret;

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

        try {
            $this->fetchAccessToken();
            $this->twitterHandler->setOauthToken($this->accessToken, $this->accessTokenSecret);
            $requestData = $this->twitterHandler->get('account/verify_credentials', ['include_email' => 'true']);
            //var_dump($requestData);

            /*
             * Should be moved to separate method
             */
            $loggedUser = $this->persistantManager
                    ->getRepository(get_class($this->authUser))
                    ->findOneBy(['email' => $requestData->email]);


            $social = new Social($requestData->id_str, SocialAuthAdapterFactory::SOCIAL_PROVIDER_TWITTER, $this->accessToken, $this->accessTokenSecret);

            if ($loggedUser instanceof User) {
                $this->generateAuthToken($loggedUser);

                $loggedUser->addSocial($social);
                $this->persistantManager->getSchemaManager()->ensureIndexes();
                $this->persistantManager->persist($loggedUser);
                $this->persistantManager->flush();



                return new Result(Result::SUCCESS, $loggedUser, [$this->translator->translate('success-login', 'zfe-user')]);
            } else {
                $newUser = new User();
                $newUser->addSocial($social);
                $this->createUser($newUser, $requestData);
                return new Result(Result::FAILURE_IDENTITY_NOT_FOUND, $newUser, [$this->translator->translate('error-no-user-found', 'zfe-user')]);
            }
        } catch (TwitterOAuthException $exp) {
            var_dump($exp);
        }

        return new Result(Result::FAILURE_UNCATEGORIZED, null, ['Unknow Error']);
    }

    /**
     *
     * @return type
     */
    public function getSocialLoginLink()
    {
        $callbackurl = $this->serverHelper->generate($this->urlHelper->generate('user-social-login', ['provider' => SocialAuthAdapterFactory::SOCIAL_PROVIDER_TWITTER]));
        $request_token = $this->twitterHandler->oauth('oauth/request_token', ['oauth_callback' => $callbackurl]);

        $_SESSION['oauth_token'] = $request_token['oauth_token'];
        $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

        return $this->twitterHandler->url('oauth/authorize', ['oauth_token' => $request_token['oauth_token']]);
    }

    public function getSocialLogOutLink()
    {
    }

    public function createUser(User $newUser, $responseData): User
    {
        $newUser->setId(UuidGenerator::generateV4());
        $newUser->setEmail($responseData->email);
        $newUser->setFullName($responseData->name);

        $newUser->setUsername($responseData->screeen_name);
        $newUser->setSlug($responseData->screeen_name);
        $newUser->setPassword(bin2hex(random_bytes(10)));
        $newUser->addRole(new Role('user'));

        return $newUser;
    }

    public function fetchAccessToken(): string
    {


        $this->twitterHandler->setOauthToken($_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
        $access_token = $this->twitterHandler->oauth("oauth/access_token", ["oauth_verifier" => $_REQUEST['oauth_verifier']]);
        $this->accessToken = $access_token['oauth_token'];
        $this->accessTokenSecret = $access_token['oauth_token_secret'];

        return $this->accessToken;
    }

    public function setAccessToken(string $accessToken)
    {
        $this->accessToken = $accessToken;
    }
}
