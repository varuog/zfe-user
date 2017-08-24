<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ZfeUser\Adapter;

use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;

/**
 * Description of FacebookAuthAdapter
 *
 * @author Win10Laptop-Kausik
 */
class FacebookAuthAdapter implements AdapterInterface
{

    /**
     * Authenticate on success if user does not exist register
     */
    public function authenticate(): Result
    {
    }
}
