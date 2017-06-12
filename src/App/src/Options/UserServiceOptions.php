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
    private $credentialEmailEnabled;
    private $credentialUserNameEnabled;
    private $notifyNewRegistration;
    private $responderEmail;
    private $responderName;

    public function getCredentialEmailEnabled() {
        return $this->credentialEmailEnabled;
    }

    public function getCredentialUserNameEnabled() {
        return $this->credentialUserNameEnabled;
    }

    public function getNotifyNewRegistration() {
        return $this->notifyNewRegistration;
    }

    public function getResponderEmail() {
        return $this->responderEmail;
    }

    public function getResponderName() {
        return $this->responderName;
    }

    public function setCredentialEmailEnabled($credentialEmailEnabled) {
        $this->credentialEmailEnabled = $credentialEmailEnabled;
        return $this;
    }

    public function setCredentialUserNameEnabled($credentialUserNameEnabled) {
        $this->credentialUserNameEnabled = $credentialUserNameEnabled;
        return $this;
    }

    public function setNotifyNewRegistration($notifyNewRegistration) {
        $this->notifyNewRegistration = $notifyNewRegistration;
        return $this;
    }

    public function setResponderEmail($notifyRecipientEmail) {
        $this->responderEmail = $notifyRecipientEmail;
        return $this;
    }

    public function setResponderName($notifyRecipientName) {
        $this->responderName = $notifyRecipientName;
        return $this;
    }

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
