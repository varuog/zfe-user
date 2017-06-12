<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Factory\Delegator;

use Psr\Container\ContainerInterface;
use Zend\I18n\Translator\TranslatorInterface;
use  Zend\I18n\Translator\Translator;

class TranslatorDelegatorFactory {

    /**
     * @param ContainerInterface $container
     * @param string $name
     * @param callable $callback
     * @return UserRepository
     */
    public function __invoke(ContainerInterface $container, $name, callable $callback) {
        /* @var $translator Translator  */
        $translator = $callback();

        $type = 'phparray';
        $userTranslatorResource = 'data/language/User-en-US.php';
        $mailTranslatorResource = 'data/language/Mailer-en-US.php';
        $textDomain = 'zfe-user';
        $locale = 'en-US';
        
        $translator->setLocale($locale);
        $translator->addTranslationFile($type, $userTranslatorResource, $textDomain, $locale);
        $translator->addTranslationFile($type, $mailTranslatorResource, $textDomain, $locale);
        return $translator;
    }

}
