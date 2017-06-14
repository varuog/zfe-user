<?php

return [
    'zfe-user' => [
        'credentialField' => 'password',
        'identityField' => 'email',
        'resetTokenValidity' => 300,
        'responseType' => \WoohooLabs\Yin\JsonApi\JsonApi::class,
        //'responseType' => Zend\Diactoros\Response\JsonResponse::class,
        'notifyNewRegistration' => true,
        'responderEmail' => 'sample@email.com',
        'responderName' => 'somename',
        'enableUserApproval' => true,
        'enableEmailVerification' => true,
        'enableEmailNotification' => true,
        'enableNotifyDeactivation' => true,
        'enableNotifyActivation' => true
    ],
];
