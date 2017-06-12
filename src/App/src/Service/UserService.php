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

    public function __construct(DocumentManager $mongoManager, TranslatorInterface $translator, TransportInterface $mailer, TemplateRendererInterface $mailTemplate, UserServiceOptions $options) {
        $this->persistantManager = $mongoManager;
        $this->options = $options;
        $this->translator = $translator;
        $this->mailer = $mailer;
        $this->mailerTemplate = $mailTemplate;
    }

    public function register(User $user) {
        $user->hashPassword();
        $this->persistantManager->persist($user);
        $this->persistantManager->flush();
    }

    public function setAuthUser(User $user) {
        $this->authUser = $user;
    }

    public function authenticate(): Result {

        $loggedUser = $this->persistantManager->getRepository(get_class($this->authUser))->findOneBy(['email' => $this->authUser->getEmail()]);
        //$loggedUser = $this->persistantManager->createQueryBuilder(get_class($user))->field('email')->;

        if ($loggedUser instanceof User) {
            if (password_verify($this->authUser->getPassword(), $loggedUser->getPassword())) {
                $this->generateAuthToken($this->authUser);

                /*
                 * Send Mail
                 */
                $mail = new Message();
                //$data = ['layout' => 'mail-template'];
                $data=[];
                $mail->addTo($loggedUser->getEmail(), $loggedUser->getFullName());
                $mail->addFrom($this->options->getResponderEmail(), $this->options->getResponderName());
                $mail->setSubject($this->translator->translate('subject-notify-user', 'zfe-user'));
                
                //$this->mailerTemplate->addPath('tst', 'foo');
                //$path=$this->mailerTemplate->getPaths();
                
                //$mail->setBody($this->mailerTemplate->render('mail::new-registration', $data));
                $mail->setBody("TEST");
                $this->mailer->send($mail);

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
                ->findOneBy(['email' => $user->getEmail()]);
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
                    ->field('password')
                    ->set($user->getPassword())
                    ->getQuery()
                    ->execute();
        }

        return false;
    }

    public function changeEmail(User $user) {
        $loggedUser = $this->persistantManager->getRepository(get_class($user))
                ->findOneBy(['email' => $user->getEmail()]);
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
                    ->field('email')
                    ->set($user->getPassword())
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
                ->field("email")
                ->equals($user->getEmail())
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
                ->field("email")
                ->equals($user->getEmail())
                ->findAndUpdate()
                ->field('resetToken')
                ->set($user->getResetToken())
                ->field('resetTokenTime')
                ->set(time())
                ->getQuery()
                ->execute();
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
