<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ZfeUser\Adapter\Auth;

use Facebook\Facebook;
use ZfeUser\Options\UserServiceOptions;
use Zend\Authentication\Result;
use Doctrine\ODM\MongoDB\DocumentManager;
use Zend\Authentication\Adapter\AdapterInterface;
use ZfeUser\Model\User;
use Firebase\JWT\JWT;
use Zend\I18n\Translator\TranslatorInterface;

/**
 * Description of FacebookAuthAdapter
 *
 * @author gourav sarkar
 */
class MongoDbAuthAdapter implements AdapterInterface {

    private $options;
    private $authUser;
    private $persistantManager;
    private $credential;
    private $identity;
    private $translator;

    public function __construct(UserServiceOptions $options, DocumentManager $persistantManager, TranslatorInterface $translator) {
        $this->options = $options;
        $this->persistantManager = $persistantManager;

        $this->credential = $this->options->getCredentialField();
        $this->identity = $this->options->getIdentityField();
        $this->translator= $translator;
    }

    public function setAuthUser(User $user) {
        $this->authUser = $user;
    }

    /**
     *
     * @return Result
     */
    public function authenticate(): Result {

        $loggedUser = $this->persistantManager
                ->getRepository(get_class($this->authUser))
                ->findOneBy([$this->identity => call_user_func([$this->authUser, "get{$this->identity}"])]);
        //$loggedUser = $this->persistantManager->createQueryBuilder(get_class($user))->field('email')->;

        if ($loggedUser instanceof User) {
            if (password_verify($this->authUser->getPassword(), $loggedUser->getPassword())) {
                $this->generateAuthToken($this->authUser);

                return new Result(Result::SUCCESS, $loggedUser, [$this->translator->translate('success-login', 'zfe-user')]);
            } else {
                return new Result(Result::FAILURE_CREDENTIAL_INVALID, null, [$this->translator->translate('error-credentail-invalid', 'zfe-user')]);
            }
        } else {
            return new Result(Result::FAILURE_IDENTITY_NOT_FOUND, null, [$this->translator->translate('error-no-user-found', 'zfe-user')]);
        }

        return new Result(Result::FAILURE_UNCATEGORIZED, null, [$this->translator->translate('error-unknown-auth', 'zfe-user')]);
    }

    
    /**
     * 
     * @param User $user
     * @return type
     */
    public function generateJwtToken(User $user) {
        $identityField = $this->options->getIdentityField();
        $token = [
            "aud" => "http://example.com",
            "iat" => time(),
            "nbf" => 1357000000,
            'iss' => 'appname',
            'exp' => time() + 86400,
            'id' => $user->getId(),
            'identifier' => call_user_func([$user, "get{$identityField}"]),
        ];


        $user->addAuthenticationToken(JWT::encode($token, $this->options->getAuthSecret()));

        return $token;
    }

    /**
     * @todo refreashToken generate should be moved to User model
     * @param User $user
     */
    public function generateAuthToken(User $user) {

        $this->persistantManager->getSchemaManager()->ensureIndexes();

        $user = $this->persistantManager
                ->getRepository(get_class($user))
                ->findOneBy([$this->identity => call_user_func([$user, "get{$this->identity}"])]);

        $this->generateJwtToken($user);

        $this->persistantManager->getSchemaManager()->ensureIndexes();

        $this->persistantManager->persist($user);
        $this->persistantManager->flush($user, ['safe' => true]);
        /*
         *
         *
          $user = $this->persistantManager->createQueryBuilder( get_class( $user ) )
          ->update()
          ->field( 'authenticationInfo' )
          ->pushAll()
          ->field( $this->identity )
          ->equals( call_user_func( [ $user, "get{$this->identity}" ] ) )

          //->field('authenticationInfo.refreshToken')
          //->set($user->getRefreashToken())
          ->getQuery()
          ->execute();
         *
         */

        return $user;
    }

}
