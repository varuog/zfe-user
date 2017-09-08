<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ZfeUser\Adapter\Auth;

use Zend\Authentication\Adapter\AdapterInterface;
use ZfeUser\Options\UserServiceOptions;
use Doctrine\ODM\MongoDB\DocumentManager;
use Zend\I18n\Translator\TranslatorInterface;
use ZfeUser\Model\User;
use Firebase\JWT\JWT;

/**
 * Description of AbstractAuthAdapter
 *
 * @author LaptopRK
 */
abstract class AbstractAuthAdapter implements AdapterInterface
{

    protected $options;
    protected $authUser;
    protected $persistantManager;
    protected $translator;

    protected $credential;
    protected $identity;

    public function setAuthUser(User $user)
    {
        $this->authUser = $user;
    }

    public function __construct(UserServiceOptions $options, DocumentManager $persistantManager, TranslatorInterface $translator)
    {
        $this->options = $options;
        $this->persistantManager = $persistantManager;
        $this->translator = $translator;


        $this->credential = $this->options->getCredentialField();
        $this->identity = $this->options->getIdentityField();
    }

    /**
     * @todo reduce access modifier
     * @param User $user
     * @return type
     */
    public function generateJwtToken(User $user)
    {
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
    public function generateAuthToken(User $user)
    {

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
