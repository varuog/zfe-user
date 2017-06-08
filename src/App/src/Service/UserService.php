<?php

namespace App\Service;

use Doctrine\ODM\MongoDB\DocumentManager;
use App\Model\User;
use App\Options\UserServiceOptions;

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
class UserService {
   

        private $persistantManager;
    private $options;

    public function __construct(DocumentManager $mongoManager, UserServiceOptions $options) {
        $this->persistantManager = $mongoManager;
        $this->options = $options;
    }

    public function register(User $user) {
        $user->hashPassword();
        $this->persistantManager->persist($user);
        $this->persistantManager->flush();
    }

    public function auth(User $user) {

        $loggedUser = $this->persistantManager->getRepository(get_class($user))->findOneBy(['email' => $user->getEmail()]);
        //$loggedUser = $this->persistantManager->createQueryBuilder(get_class($user))->field('email')->;

        if (password_verify($user->getPassword(), $loggedUser->getPassword())) {
            $this->generateAuthToken($user);
            return $loggedUser;
        }
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

     public function getOptions() : UserServiceOptions{
        return $this->options;
    }
}
