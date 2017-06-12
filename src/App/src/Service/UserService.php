<?php

namespace App\Service;

use Doctrine\ODM\MongoDB\DocumentManager;
use App\Model\User;
use App\Options\UserServiceOptions;
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
    
    
    public function __construct(DocumentManager $mongoManager, TranslatorInterface $translator, TransportInterface $mailer, TemplateRendererInterface $mailTemplate, UserServiceOptions $options) {
        $this->persistantManager = $mongoManager;
        $this->options = $options;
        $this->translator = $translator;
        $this->mailer = $mailer;
        $this->mailerTemplate = $mailTemplate;
        
        
        $this->credential= $this->options->getCredentialField();
        $this->identity= $this->options->getIdentityField();
    }

    public function register(User $user) {

        //Calculate approval
        ($this->options->getEnableUserApproval()) ? $user->setApproved(false) : $user->setApproved(true);
        $user->setApproveTime(time());


        $user->hashPassword();
        $this->persistantManager->persist($user);
        $this->persistantManager->flush();

        /*
         * Send Mail
         */
        $mail = new Message();
        $data = ['layout' => 'mail-template'];
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

    public function setAuthUser(User $user) {
        $this->authUser = $user;
    }

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

    public function changePassword(User $user) {

        $loggedUser = $this->persistantManager->getRepository(get_class($user))
                ->findOneBy([$this->identity => call_user_func([$user, "get{$this->identity}"])]);
        //$loggedUser = $this->persistantManager->createQueryBuilder(get_class($user))->field('email')->;
        $isExpiredToken = time() < $loggedUser->getResetTokenTime() + $this->options->getResetTokenValidity();
        if ($loggedUser instanceof User && !$isExpiredToken) {

            $this->persistantManager->createQueryBuilder(get_class($user))
                    ->field("resetToken")
                    ->equals($user->getResetToken())
                    ->findAndUpdate()
                    ->field('resetToken')
                    ->set(null)
                    ->field('resetTokenTime')
                    ->set(null)
                    ->field($this->credential)
                    ->set(call_user_func([$user, "get{$this->credential}"]))
                    ->getQuery()
                    ->execute();
        }

        return false;
    }

    public function changeEmail(User $user) {
        $loggedUser = $this->persistantManager->getRepository(get_class($user))
                ->findOneBy([$this->identity => call_user_func([$user, "get{$this->identity}"])]);
        //$loggedUser = $this->persistantManager->createQueryBuilder(get_class($user))->field('email')->;
        $isExpiredToken = time() < $loggedUser->getResetTokenTime() + $this->options->getResetTokenValidity();
        if ($loggedUser instanceof User && !$isExpiredToken) {

            $this->persistantManager->createQueryBuilder(get_class($user))
                    ->field("resetToken")
                    ->equals($user->getResetToken())
                    ->findAndUpdate()
                    ->field('resetToken')
                    ->set(null)
                    ->field('resetTokenTime')
                    ->set(null)
                    ->field($this->identity)
                    ->set(call_user_func([$user, "get{$this->identity}"]))
                    ->getQuery()
                    ->execute();

            /*
             * Send Mail
             */
            $mail = new Message();
            $data = ['layout' => 'mail-template'];
            $mail->addTo($loggedUser->getFullName(), $loggedUser->getEmail());
            $mail->setSubject($this->translator->translate('subject-notify-user', 'zfe-user'));
            $mail->setBody($this->mailerTemplate->render('mail::new-registration', $data));

            $this->mailer->send($mail);
        }

        return false;
    }

    public function generateAuthToken(User $user) {
        $user->generateToken();

        $this->persistantManager->createQueryBuilder(get_class($user))
                ->field($this->identity)
                ->equals(call_user_func([$user, "get{$this->identity}"]))
                ->findAndUpdate()
                ->field('authToken')
                ->set($user->getAuthToken())
                ->field('authTokenTime')
                ->set(time())
                ->getQuery()
                ->execute();
    }

    public function generateResetToken(User $user) {
        $user->generateToken();

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
        $data = ['layout' => 'mail-template'];
        $mail->addTo($user->getEmail(), $user->getFullName());
        $mail->addFrom($this->options->getResponderEmail(), $this->options->getResponderName());
        $mail->setSubject($this->translator->translate('subject-generate-reset-token', 'zfe-user'));
        $mail->setBody($this->mailerTemplate->render('mail::generate-reset-token', $data));
    }

    public function isValidAuthToken(string $authToken): bool {
        //$this->persistantManager->getRepository(get)
    }

    public function getOptions(): UserServiceOptions {
        return $this->options;
    }

    public function getEventManager(): \Zend\EventManager\EventManagerInterface {
        if (!$this->events) {
            $this->setEventManager(new EventManager());
        }
        return $this->events;
    }

    public function setEventManager(\Zend\EventManager\EventManagerInterface $eventManager): void {
        $this->events = $eventManager;
    }

}
