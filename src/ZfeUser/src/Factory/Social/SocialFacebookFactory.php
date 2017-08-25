<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ZfeUser\Factory\MongoDB;

use Interop\Container\ContainerInterface;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Driver\YamlDriver;
use Facebook\Facebook;

/**
 * Description of MongoDocumentManagerFactory
 * @todo Database name should be overridable
 * @author Gourav Sarkar
 */
class SocialFacebookFactory
{

    public function __invoke(ContainerInterface $container)
    {
        $connection = new \Doctrine\MongoDB\Connection();
        $config = new \Doctrine\ODM\MongoDB\Configuration();
        $fb = new Facebook([
            'app_id' =>  $config['social']['facebook']['appID'],
            'app_secret' => $config['social']['facebook']['appSecret'],
            'default_graph_version' => 'v2.10',
                //'default_access_token' => '{access-token}', // optional
        ]);
        return $fb;
    }

}
