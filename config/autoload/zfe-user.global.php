<?php

use Zend\ConfigAggregator\ConfigAggregator;

return [

    'zfe-user' => [
        'resetTokenValidity' => 300,
        'responseType' => Zend\Diactoros\Response\HtmlResponse::class,
    ],
];
