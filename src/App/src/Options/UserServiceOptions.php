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
    private $credentialField;
    private $identityField;
    private $passwordFieldField;
    private $notifyNewRegistration;
    private $responderEmail;
    private $responderName;
    private $enableUserApproval;
    private $enableEmailVerification;

    public function getNotifyNewRegistration() {
        return $this->notifyNewRegistration;
    }

    public function getResponderEmail() {
        return $this->responderEmail;
    }

    public function getResponderName() {
        return $this->responderName;
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

    public function getEnableUserApproval() {
        return $this->enableUserApproval;
    }

    public function setEnableUserApproval($enableUserApproval) {
        $this->enableUserApproval = $enableUserApproval;
        return $this;
    }

    public function getEnableEmailVerification() {
        return $this->enableEmailVerification;
    }

    public function setEnableEmailVerification($enableEmailVerification) {
        $this->enableEmailVerification = $enableEmailVerification;
        return $this;
    }

    public function getCredentialField() {
        return $this->credentialField;
    }

    public function getPasswordFieldField() {
        return $this->passwordFieldField;
    }

    public function setCredentialField($credentialField) {
        $this->credentialField = $credentialField;
        return $this;
    }

    public function setPasswordFieldField($passwordFieldField) {
        $this->passwordFieldField = $passwordFieldField;
        return $this;
    }
    public function getIdentityField() {
        return $this->identityField;
    }

    public function setIdentityField($identityField) {
        $this->identityField = $identityField;
        return $this;
    }


}
