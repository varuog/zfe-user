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
use ZfeUser\Adapter\Auth\AbstractAuthAdapter;

/**
 * Description of FacebookAuthAdapter
 *
 * @author gourav sarkar
 */
class MongoDbAuthAdapter extends AbstractAuthAdapter
{


    public function __construct(UserServiceOptions $options, DocumentManager $persistantManager, TranslatorInterface $translator)
    {
        parent::__construct($options, $persistantManager, $translator);
    }

    /**
     *
     * @return Result
     */
    public function authenticate(): Result
    {

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
}
