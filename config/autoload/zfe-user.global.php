<?php

return [
    'zfe-user' => [
        'credentialField' => 'password',
        'identityField' => 'email',
        'resetTokenValidity' => 300,
        'responseType' => \WoohooLabs\Yin\JsonApi\JsonApi::class,
        //'responseType' => Zend\Diactoros\Response\JsonResponse::class,
        'notifyNewRegistration' => false,
        'responderEmail' => 'sample@email.com',
        'responderName' => 'somename',
        'enableUserApproval' => true,
        'enableEmailVerification' => false,
        'enableEmailNotification' => false,
        'enableNotifyDeactivation' => false,
        'enableNotifyActivation' => false,
        'accessTokenTtl' => 86400 * 365,
        'publicProfile' => true,
        'authSecret' => 'fbH)\Jh9J`gQkn!y',
        'tokenRevokable' => false,
        'social' => [
            'facebook' => [
                'appID' => '485738178452691',
                'appSecret' => '653d9dcbfc0b0150592348c38eace83c',
                'scope' => ['email', 'public_profile'],
                'fields' => ['email', 'name'],
                'version' => 'v2.10'
            ],
            'twitter' => [
                'appID' => 'wwJk03f6MkyH1qsaVZIG7xAAa',
                'appSecret' => 'izmcp1U7HO0w4mXkXsDqfBqZTuiwBf9ad07LRvS0esoVX1mIE1',
                'scope' => ['email', 'public_profile'],
                'fields' => ['email', 'name'],
                'version' => 'v2.10'
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
