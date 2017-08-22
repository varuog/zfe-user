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
        'enableNotifyActivation' => true,
        'accessTokenTtl' => 86400 * 365,
        'publicProfile' => true,
        'authSecret' => 'fbH)\Jh9J`gQkn!y',
        'tokenRevokable' => false,
        'social' => [
            'facebook' => [
                'authUri' => 'https://www.facebook.com/v2.10/dialog/oauth',
                'redirectUrl' =>'',
                'appSecret' => '',
                'scopes' => ''
            ]
        ]
    ],
    
    /**
     * Not documented
     */
    'db' => [
        'database' => 'User'
    ]
];
