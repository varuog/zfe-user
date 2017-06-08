<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Options;

use Zend\Stdlib\AbstractOptions;
use Zend\View\Renderer\RendererInterface;

/**
 * Description of UserServiceOptions
 *
 * @author Win10Laptop-Kausik
 */
class UserServiceOptions extends AbstractOptions {

    private $resetTokenValidity;
    private $responseType;

    public function __construct(array $options = null) {
        parent::__construct($options);
    }

    public function getResetTokenValidity() {
        return $this->resetTokenValidity;
    }

    public function setResetTokenValidity($resetTokenValidity) {
        $this->resetTokenValidity = $resetTokenValidity;
        return $this;
    }

    public function getResponseType() {
        return $this->responseType;
    }

    public function setResponseType($responseType) {
        $this->responseType = $responseType;
        return $this;
    }

}
