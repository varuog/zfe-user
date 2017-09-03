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
/**
 * Description of FacebookAuthAdapter
 *
 * @author gourav sarkar
 */
class FacebookAuthAdapter implements AdapterInterface {

    private $option;
    private $authUser;
    private $persistantManager;
    private $translator;
    private $urlHelper;
    private $serverHelper;
    private $fbHandler;

    public function __construct(UserServiceOptions $option, DocumentManager $persistantManager, TranslatorInterface $translator, UrlHelper $urlHelper, ServerUrlHelper $serverHelper) {
        $this->option = $option;
        $this->persistantManager = $persistantManager;
        $this->translator = $translator;
        $this->urlHelper = $urlHelper;
        $this->serverHelper= $serverHelper;
        
        
        
        $fbSocialOption = $this->option->getSocial()['facebook'];
        $this->fbHandler= new Facebook(['app_id' => $fbSocialOption['appID'],
            'app_secret' => $fbSocialOption['appSecret'],
            'default_graph_version' => $fbSocialOption['version'],
        ]);
    }

    public function setAuthUser(User $user) {
        $this->authUser = $user;
    }
    
    public function getHandler() : Facebook
    {
        return $this->fbHandler;
    }

    /**
     * @todo fix result type in return
     * @return Result
     */
    public function authenticate(): Result {

        $helper =   $this->fbHandler->getRedirectLoginHelper();
        $redirectUrl= $this->serverHelper->generate($this->urlHelper->generate('user-social-login'));
        
        try {
            $accessToken = $helper->getAccessToken();
            if ($helper->getError()) {
                $oAuth2Client =   $this->fbHandler->getOAuth2Client();
                $tokenMetaData = $oAuth2Client->debugToken($accessToken);
                $tokenMetadata->validateAppId($fbSocialOption['appID']);
                $tokenMetadata->validateExpiration();
                $longLiveAccessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);

                $response =   $this->fbHandler->get('/me', $longLiveAccessToken);
                $responseData = $response->getGraphUser();

                $loggedUser = $this->persistantManager
                        ->getRepository(get_class($this->authUser))
                        ->findOneBy([$this->identity => call_user_func([$this->authUser, "get{$this->identity}"])]);


                if ($loggedUser instanceof User) {
                    $this->generateAuthToken($this->authUser);

                    return new Result(Result::SUCCESS, $loggedUser, [$this->translator->translate('success-login', 'zfe-user')]);
                } else {
                    return new Result(Result::FAILURE_IDENTITY_NOT_FOUND, null, [$this->translator->translate('error-no-user-found', 'zfe-user')]);
                }

                return new Result(Result::FAILURE_UNCATEGORIZED, null, [$this->translator->translate('error-unknown-auth', 'zfe-user')]);
            }
        } catch (Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            //echo 'Graph returned an error: ' . $e->getMessage();
            return new Result(Result::FAILURE_UNCATEGORIZED, null, [$this->translator->translate('error-unknown-auth', 'zfe-user')]);
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            //echo 'Facebook SDK returned an error: ' . $e->getMessage();
            return new Result(Result::FAILURE_UNCATEGORIZED, null, [$this->translator->translate('error-unknown-auth', 'zfe-user')]);
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            return new Result(Result::FAILURE_UNCATEGORIZED, null, [$this->translator->translate('error-unknown-auth', 'zfe-user')]);
        }

        return new Result(Result::FAILURE_UNCATEGORIZED, null, [$this->translator->translate('error-unknown-auth', 'zfe-user')]);
    }

}
