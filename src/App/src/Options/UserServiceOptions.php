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
    private $notifyRecipientEmail;
    private $notifyRecipientName;

    public function getCredentialEmailEnabled() {
        return $this->credentialEmailEnabled;
    }

    public function getCredentialUserNameEnabled() {
        return $this->credentialUserNameEnabled;
    }

    public function getNotifyNewRegistration() {
        return $this->notifyNewRegistration;
    }

    public function getNotifyRecipientEmail() {
        return $this->notifyRecipientEmail;
    }

    public function getNotifyRecipientName() {
        return $this->notifyRecipientName;
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

    public function setNotifyRecipientEmail($notifyRecipientEmail) {
        $this->notifyRecipientEmail = $notifyRecipientEmail;
        return $this;
    }

    public function setNotifyRecipientName($notifyRecipientName) {
        $this->notifyRecipientName = $notifyRecipientName;
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
