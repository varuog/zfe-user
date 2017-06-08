<?php

use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Router\ZendRouter;

return [
    'dependencies' => [
        'invokables' => [
            RouterInterface::class => ZendRouter::class,
        ],
        'abstract_factories' => [
            App\Factory\AbstractActionFactory::class,
        ],
    ],
];
