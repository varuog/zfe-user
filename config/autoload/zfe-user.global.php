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
                'appID'=> 485738178452691,
                'authUri' => 'https://www.facebook.com/v2.10/dialog/oauth',
                'redirectUrl' =>'',
                'appSecret' => '653d9dcbfc0b0150592348c38eace83c',
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
