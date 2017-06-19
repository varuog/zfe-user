<?php

namespace ZfeUser\Service;

use Doctrine\ODM\MongoDB\DocumentManager;
use ZfeUser\Model\User;
use ZfeUser\Options\UserServiceOptions;
use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\Mail\Transport\TransportInterface;
use Zend\Mail\Message;
use Zend\Expressive\Template\TemplateRendererInterface;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UserService
 *
 * @author Win10Laptop-Kausik
 */
class UserService implements AdapterInterface, EventManagerAwareInterface {

    private $persistantManager;
    private $options;
    private $authUser;
    private $translator;
    private $mailer;
    private $mailerTemplate;
    private $events;
    private $credential;
    private $identity;

    /**
     * 
     * @param DocumentManager $mongoManager
     * @param TranslatorInterface $translator
     * @param TransportInterface $mailer
     * @param TemplateRendererInterface $mailTemplate
     * @param UserServiceOptions $options
     */
    public function __construct(DocumentManager $mongoManager, TranslatorInterface $translator, TransportInterface $mailer, TemplateRendererInterface $mailTemplate, UserServiceOptions $options) {
        $this->persistantManager = $mongoManager;
        $this->options = $options;
        $this->translator = $translator;
        $this->mailer = $mailer;
        $this->mailerTemplate = $mailTemplate;


        $this->credential = $this->options->getCredentialField();
        $this->identity = $this->options->getIdentityField();
    }

    public function fetch(User $user) {
        return $this->persistantManager->find(get_class($user), $user->getId());
    }

    /**
     * Destroy auth token
     * @param User $user
     */
    public function logout(User $user) {
        return $this->persistantManager->createQueryBuilder(get_class($user))
                        ->field('authToken')
                        ->exists(true)
                        ->findAndUpdate()
                        ->field('authToken')
                        ->set(null)
                        ->getQuery()
                        ->execute();
    }

    /**
     * 
     * @param User $user
     */
    public function register(User $user) {

        //Calculate approval
        ($this->options->getEnableUserApproval()) ? $user->setApproved(false) : $user->setApproved(true);
        $user->setApproveTime(new \DateTime());


        $user->hashPassword();
        $this->persistantManager->persist($user);
        $this->persistantManager->flush($user, ['safe' => true]);
        //$this->persistantManager->getSchemaManager()->ensureIndexes();

        /*
         * Send Mail
         */
        if ($this->options->getEnableEmailNotification()) {
            $mail = new Message();
            $data = ['layout' => 'layout::mail-template'];
            $mail->addTo($user->getEmail(), $user->getFullName());
            $mail->addFrom($this->options->getResponderEmail(), $this->options->getResponderName());
            $mail->setSubject($this->translator->translate('subject-notify-user', 'zfe-user'));
            $mail->setBody($this->mailerTemplate->render('mail::new-registration-notify-user', $data));
            $this->mailer->send($mail);

            $mail = new Message();
            $mail->addTo($user->getEmail(), $user->getFullName());
            $mail->addFrom($this->options->getResponderEmail(), $this->options->getResponderName());
            $mail->setSubject($this->translator->translate('subject-notify-admin', 'zfe-user'));
            $mail->setBody($this->mailerTemplate->render('mail::new-registration-notify-admin', $data));

            $this->mailer->send($mail);
        }
    }

    /**
     * 
     * @param User $user
     */
    public function setAuthUser(User $user) {
        $this->authUser = $user;
    }

    /**
     * @todo reset token issue
     * @param User $user
     */
    public function isValidAuthToken(User $user) {
        /** @var User $newuser */
        $newuser = $this->persistantManager->getRepository(get_class($user))
                ->findOneBy([$this->identity => call_user_func([$this->authUser, "get{$this->identity}"])
            , 'authToken' => $user->getAuthToken()
                ]
        );

        //expired token
        if ($user->getAuthTokenTime() + $this->options->getAccessTokenTtl() < time()) {
            //Update auth token if it matches
            if (is_null($newuser) || $newuser->getRefreashToken() == $user->getRefreashToken()) {
                return $this->generateAuthToken($user);
            }
        }

        return $newuser;
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

                return new Result(Result::SUCCESS
                        , $loggedUser
                        , [$this->translator->translate('success-login', 'zfe-user')]);
            } else {
                return new Result(Result::FAILURE_CREDENTIAL_INVALID, null
                        , [$this->translator->translate('error-credentail-invalid')]);
            }
        } else {

            return new Result(Result::FAILURE_IDENTITY_NOT_FOUND, null
                    , [$this->translator->translate('error-no-user-found')]);
        }

        return new Result(Result::FAILURE_UNCATEGORIZED, null
                , [$this->translator->translate('error-unknown-auth')]
        );
    }

    /**
     * 
     * @param User $user
     * @return boolean
     */
    public function changePassword(User $user) {

        $loggedUser = $this->persistantManager->getRepository(get_class($user))
                ->findOneBy([$this->identity => call_user_func([$user, "get{$this->identity}"])]);
        //$loggedUser = $this->persistantManager->createQueryBuilder(get_class($user))->field('email')->;
        $isExpiredToken = time() < $loggedUser->getResetTokenTime() + $this->options->getResetTokenValidity();
        if ($loggedUser instanceof User && !$isExpiredToken) {
            $loggedUser->getResetToken();
            $this->persistantManager->createQueryBuilder(get_class($user))
                    ->field("resetToken")
                    ->equals($loggedUser->getResetToken())
                    ->findAndUpdate()
                    ->field('resetToken')
                    ->set(null)
                    ->field('resetTokenTime')
                    ->set(null)
                    ->field('emailVerified')
                    ->set(true)
                    ->field($this->credential)
                    ->set(call_user_func([$loggedUser, "get{$this->credential}"]))
                    ->getQuery()
                    ->execute();
        }

        return false;
    }

    /**
     * @param User $user
     * @return boolean
     */
    public function changeEmail(User $user) {
        $loggedUser = $this->persistantManager->getRepository(get_class($user))
                ->findOneBy([$this->identity => call_user_func([$user, "get{$this->identity}"])]);
        //$loggedUser = $this->persistantManager->createQueryBuilder(get_class($user))->field('email')->;
        $isExpiredToken = time() < $loggedUser->getResetTokenTime() + $this->options->getResetTokenValidity();
        if ($loggedUser instanceof User && !$isExpiredToken) {

            $loggedUser->getResetToken();

            $this->persistantManager->createQueryBuilder(get_class($user))
                    ->field("resetToken")
                    ->equals($loggedUser->getResetToken())
                    ->findAndUpdate()
                    ->field('resetToken')
                    ->set(null)
                    ->field('resetTokenTime')
                    ->set(null)
                    ->field('emailVerified')
                    ->set(true)
                    ->field($this->identity)
                    ->set(call_user_func([$loggedUser, "get{$this->identity}"]))
                    ->getQuery()
                    ->execute();
        }

        return false;
    }

    /**
     * @todo refreashToken generate should be moved to User model
     * @param User $user
     */
    public function generateAuthToken(User $user) {
        $user->generateAuthToken();

        return $this->persistantManager->createQueryBuilder(get_class($user))
                        ->field($this->identity)
                        ->equals(call_user_func([$user, "get{$this->identity}"]))
                        ->findAndUpdate()
                        ->returnNew()
                        ->field('authToken')
                        ->set($user->getAuthToken())
                        ->field('authTokenTime')
                        ->set(time())
                        ->field('refreshToken')
                        ->set($user->getRefreashToken())
                        ->getQuery()
                        ->execute();
    }

    /**
     * 
     * @param User $user
     * @param type $resetField
     */
    public function generateResetToken(User $user, $resetField = '') {
        $user->generateResetToken();

        $this->persistantManager->createQueryBuilder(get_class($user))
                ->field($this->identity)
                ->equals(call_user_func([$user, "get{$this->identity}"]))
                ->findAndUpdate()
                ->field('resetToken')
                ->set($user->getResetToken())
                ->field('resetTokenTime')
                ->set(time())
                ->getQuery()
                ->execute();

        $mail = new Message();
        $data = ['layout' => 'layout::mail-template'];
        $mail->addTo($user->getEmail(), $user->getFullName());
        $mail->addFrom($this->options->getResponderEmail(), $this->options->getResponderName());

        $subjectKey = (isset($resetField)) ? "subject-generate-reset-token-{$resetField}" : 'subject-generate-reset-token';
        $bodyKey = (isset($resetField)) ? "mail::generate-reset-token-{$resetField}" : 'mail::generate-reset-token';
        $mail->setSubject($this->translator->translate($subjectKey, 'zfe-user'));
        $mail->setBody($this->mailerTemplate->render($bodyKey, $data));

        $this->mailer->send($mail);
    }

    /**
     * 
     * @return UserServiceOptions
     */
    public function getOptions(): UserServiceOptions {
        return $this->options;
    }

    /**
     * 
     * @return \Zend\EventManager\EventManagerInterface
     */
    public function getEventManager(): \Zend\EventManager\EventManagerInterface {
        if (!$this->events) {
            $this->setEventManager(new EventManager());
        }
        return $this->events;
    }

    /**
     * 
     * @param \Zend\EventManager\EventManagerInterface $eventManager
     */
    public function setEventManager(\Zend\EventManager\EventManagerInterface $eventManager): void {
        $this->events = $eventManager;
    }

    /**
     * 
     * @param User $user
     */
    public function activateUser(User $user) {
        $updateApprove = $this->persistantManager->createQueryBuilder(get_class($user))
                ->field($this->identity)
                ->equals(call_user_func([$user, "get{$this->identity}"]))
                ->findAndUpdate()
                ->returnNew()
                ->field('approved')
                ->set($user->getApproved());

        /*
         * Only update time if activation is true
         */
        if ($user->getApproved()) {
            $updateApprove = $updateApprove->field('approvedTime')
                    ->set(time());
        }
        /* @var $updatedUser User */
        $updatedUser = $updateApprove->getQuery()
                ->execute();


        if (($this->options->getEnableNotifyDeactivation() && !$updatedUser->getApproved()) || $updatedUser->getApproved() && $this->options->getEnableNotifyActivation()) {
            $mail = new Message();
            $data = ['layout' => 'layout::mail-template'];
            $mail->addTo($user->getEmail(), $user->getFullName());
            $mail->addFrom($this->options->getResponderEmail(), $this->options->getResponderName());

            $subjectKey = ($updatedUser->getApproved()) ? 'subject-notify-user-activation' : 'subject-notify-user-deactivation';
            $bodyKey = ($updatedUser->getApproved()) ? 'mail::notify-user-activation' : 'mail::notify-user-deactivation';

            $mail->setSubject($this->translator->translate($subjectKey, 'zfe-user'));
            $mail->setBody($this->mailerTemplate->render($bodyKey, $data));

            $this->mailer->send($mail);
        }
    }

}
