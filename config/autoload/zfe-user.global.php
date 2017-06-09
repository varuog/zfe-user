<?php

use Zend\ConfigAggregator\ConfigAggregator;

return [
    'zfe-user' => [
        'credentialEmailEnabled' => true,
        'credentialUserNameEnabled' => true,
        'resetTokenValidity' => 300,
        'responseType' => Zend\Diactoros\Response\JsonResponse::class,
        'notifyNewRegistration' => true,
        'notifyRecipientEmail' => 'sample@email.com',
        'notifyRecipientName' => 'somename',
    ],
];
