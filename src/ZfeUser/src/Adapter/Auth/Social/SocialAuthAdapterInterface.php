<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ZfeUser\Adapter\Auth\Social;

use Zend\Authentication\Adapter\AdapterInterface;
use ZfeUser\Model\User;

/**
 *
 * @author LaptopRK
 */
interface SocialAuthAdapterInterface extends AdapterInterface
{

    public function getHandler();

    public function getSocialLoginLink();

    public function getSocialLogOutLink();

    public function createUser(User $newUser, $data): User;
    
    public function setAccessToken(string $accessToken);
    
    public function fetchAccessToken(): string;
}
