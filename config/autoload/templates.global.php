<?php

use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Expressive\ZendView\HelperPluginManagerFactory;
use Zend\Expressive\ZendView\ZendViewRendererFactory;
use Zend\View\HelperPluginManager;
return [
    'dependencies' => [
        'factories' => [
            TemplateRendererInterface::class => ZendViewRendererFactory::class,
            HelperPluginManager::class => HelperPluginManagerFactory::class,
            App\Service\MailerTemplateInterface::class => ZendViewRendererFactory::class
        ],
    ],
    'templates' => [
        'layout' => 'layout::default',
        'paths' => [
            'mail' => ['/templates/mail'],
        ]
    ],
    'view_helpers' => [
    // zend-servicemanager-style configuration for adding view helpers:
    // - 'aliases'
    // - 'invokables'
    // - 'factories'
    // - 'abstract_factories'
    // - etc.
    ],
];
