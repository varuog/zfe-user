<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ZfeUser\Options;

use Zend\Stdlib\AbstractOptions;
use Zend\View\Renderer\RendererInterface;

/**
 * Description of UserServiceOptions
 *
 * @author Gourav Sarkar
 */
class UserServiceOptions extends AbstractOptions
{



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
    private $enableEmailNotification;
    private $enableNotifyDeactivation;
    private $enableNotifyActivation;
    private $accessTokenTtl;
    private $publicProfile;
    private $authSecret;
    private $tokenRevokable=false;
    private $social=[];

    public function __construct(array $options = null)
    {
        parent::__construct($options);
    }

    public function getAccessTokenTtl()
    {
        return $this->accessTokenTtl;
    }

    public function setAccessTokenTtl($accessTokenTtl)
    {
        $this->accessTokenTtl = $accessTokenTtl;
        return $this;
    }

    public function getNotifyNewRegistration()
    {
        return $this->notifyNewRegistration;
    }

    public function getResponderEmail()
    {
        return $this->responderEmail;
    }

    public function getResponderName()
    {
        return $this->responderName;
    }

    public function setNotifyNewRegistration($notifyNewRegistration)
    {
        $this->notifyNewRegistration = $notifyNewRegistration;
        return $this;
    }

    public function setResponderEmail($notifyRecipientEmail)
    {
        $this->responderEmail = $notifyRecipientEmail;
        return $this;
    }

    public function setResponderName($notifyRecipientName)
    {
        $this->responderName = $notifyRecipientName;
        return $this;
    }

    public function getResetTokenValidity()
    {
        return $this->resetTokenValidity;
    }

    public function setResetTokenValidity($resetTokenValidity)
    {
        $this->resetTokenValidity = $resetTokenValidity;
        return $this;
    }

    public function getResponseType()
    {
        return $this->responseType;
    }

    public function setResponseType($responseType)
    {
        $this->responseType = $responseType;
        return $this;
    }

    public function getEnableUserApproval()
    {
        return $this->enableUserApproval;
    }

    public function setEnableUserApproval($enableUserApproval)
    {
        $this->enableUserApproval = $enableUserApproval;
        return $this;
    }

    public function getEnableEmailVerification()
    {
        return $this->enableEmailVerification;
    }

    public function setEnableEmailVerification($enableEmailVerification)
    {
        $this->enableEmailVerification = $enableEmailVerification;
        return $this;
    }

    public function getCredentialField()
    {
        return $this->credentialField;
    }

    public function getPasswordFieldField()
    {
        return $this->passwordFieldField;
    }

    public function setCredentialField($credentialField)
    {
        $this->credentialField = $credentialField;
        return $this;
    }

    public function setPasswordFieldField($passwordFieldField)
    {
        $this->passwordFieldField = $passwordFieldField;
        return $this;
    }

    public function getIdentityField()
    {
        return $this->identityField;
    }

    public function setIdentityField($identityField)
    {
        $this->identityField = $identityField;
        return $this;
    }

    public function getEnableEmailNotification()
    {
        return $this->enableEmailNotification;
    }

    public function getEnableNotifyDeactivation()
    {
        return $this->enableNotifyDeactivation;
    }

    public function setEnableEmailNotification($enableEmailNotification)
    {
        $this->enableEmailNotification = $enableEmailNotification;
        return $this;
    }

    public function setEnableNotifyDeactivation($enableNotifyDeactivation)
    {
        $this->enableNotifyDeactivation = $enableNotifyDeactivation;
        return $this;
    }

    public function getEnableNotifyActivation()
    {
        return $this->enableNotifyActivation;
    }

    public function setEnableNotifyActivation($enableNotifyActivation)
    {
        $this->enableNotifyActivation = $enableNotifyActivation;
        return $this;
    }

    public function getPublicProfile()
    {
        return $this->publicProfile;
    }

    public function setPublicProfile($publicProfile)
    {
        $this->publicProfile = $publicProfile;
        return $this;
    }

    public function getAuthSecret()
    {
        return $this->authSecret;
    }

    public function setAuthSecret($authSecret)
    {
        $this->authSecret = $authSecret;
        return $this;
    }

    public function isTokenRevokable()
    {
        return $this->tokenRevokable;
    }

    public function setTokenRevokable($tokenRevokable)
    {
        $this->tokenRevokable = $tokenRevokable;
        return $this;
    }

    public function getSocial()
    {
        return $this->social;
    }

    public function setSocial($social)
    {
        $this->social = $social;
        return $this;
    }
}
