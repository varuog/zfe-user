<?php

use Zend\ConfigAggregator\ConfigAggregator;

return [
    'zfe-user' => [
        'credentialField' => 'password',
        'identityField' => 'email',
        'resetTokenValidity' => 300,
        'responseType' => Zend\Diactoros\Response\JsonResponse::class,
        'notifyNewRegistration' => true,
        'responderEmail' => 'sample@email.com',
        'responderName' => 'somename',
        'enableUserApproval' => true,
        'enableEmailVerification' => true
    ],
];
