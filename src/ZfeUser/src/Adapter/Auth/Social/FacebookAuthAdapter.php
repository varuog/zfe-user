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

/**
 * Description of FacebookAuthAdapter
 * @todo introduce zend-session. instead of calling session_start directly
 * @author gourav sarkar
 */
class FacebookAuthAdapter extends AbstractAuthAdapter
{

    private $urlHelper;
    private $serverHelper;
    private $fbHandler;

    public function __construct(UserServiceOptions $options, DocumentManager $persistantManager, TranslatorInterface $translator, UrlHelper $urlHelper, ServerUrlHelper $serverHelper)
    {
        parent::__construct($options, $persistantManager, $translator);
        $this->urlHelper = $urlHelper;
        $this->serverHelper = $serverHelper;

        session_start();

        $fbSocialOption = $this->options->getSocial()['facebook'];
        $this->fbHandler = new Facebook(['app_id' => $fbSocialOption['appID'],
            'app_secret' => $fbSocialOption['appSecret'],
            'default_graph_version' => $fbSocialOption['version'],
            'scope' => $fbSocialOption['scope'],
            'persistent_data_handler' => 'session',
        ]);
    }

    public function getHandler(): Facebook
    {
        return $this->fbHandler;
    }

    /**
     * @todo fix result type in return
     * @return Result
     */
    public function authenticate(): Result
    {

        $helper = $this->fbHandler->getRedirectLoginHelper();
        $redirectUrl = $this->serverHelper->generate($this->urlHelper->generate('user-social-login'));
        $fbSocialOption = $this->options->getSocial()['facebook'];

        try {
            $accessToken = $helper->getAccessToken();
            if (!$helper->getError())
            {
                $oAuth2Client = $this->fbHandler->getOAuth2Client();
                $tokenMetaData = $oAuth2Client->debugToken($accessToken);
                $tokenMetaData->validateAppId($fbSocialOption['appID']);
                $tokenMetaData->validateExpiration();
                $longLiveAccessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);

                $response = $this->fbHandler->get('/me', $longLiveAccessToken);
                $responseData = $response->getGraphUser();

                $loggedUser = $this->persistantManager
                        ->getRepository(get_class($this->authUser))
                        ->findOneBy(['email' => $responseData->getEmail()]);


                if ($loggedUser instanceof User)
                {
                    $this->generateAuthToken($this->authUser);

                    return new Result(Result::SUCCESS, $loggedUser, [$this->translator->translate('success-login', 'zfe-user')]);
                } else
                {
                   
                    $newUser = new User();
                    $newUser->setId(UuidGenerator::generateV4());
                    $newUser->setEmail($responseData->getEmail());
                    $newUser->setFullName(sprintf('%s %s %s'
                                    , $responseData->getFirstName()
                                    , $responseData->getMiddleName()
                                    , $responseData->getLastName()
                    ));

                    $newUser->setUsername(explode('@', $responseData->getEmail())[0]);
                    $newUser->setSlug(explode('@', $responseData->getEmail())[0]);
                    $newUser->setPassword(bin2hex(random_bytes(10)));
                    $newUser->addRole(new Role('user'));
                    return new Result(Result::FAILURE_IDENTITY_NOT_FOUND, $newUser, [$this->translator->translate('error-no-user-found', 'zfe-user')]);
                }

                return new Result(Result::FAILURE_UNCATEGORIZED, null, [$this->translator->translate('error-unknown-auth', 'zfe-user')]);
            }
        } catch (Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            //echo 'Graph returned an error: ' . $e->getMessage();
            //$this->translator->translate('error-unknown-auth', 'zfe-user')
            return new Result(Result::FAILURE_UNCATEGORIZED, null, [$e->getMessage()]);
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            //echo 'Facebook SDK returned an error: ' . $e->getMessage();
            return new Result(Result::FAILURE_UNCATEGORIZED, null, [$e->getMessage()]);
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            return new Result(Result::FAILURE_UNCATEGORIZED, null, [$e->getMessage()]);
        }

        return new Result(Result::FAILURE_UNCATEGORIZED, null, [$e->getMessage()]);
    }

}
