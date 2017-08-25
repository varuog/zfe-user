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

/**
 * Description of MongoDocumentManagerFactory
 * @todo Database name should be overridable
 * @author Gourav Sarkar
 */
class MongoDocumentManagerFactory
{

    public function __invoke(ContainerInterface $container)
    {
        $connection = new \Doctrine\MongoDB\Connection();
        $config = new \Doctrine\ODM\MongoDB\Configuration();

        $config->setProxyDir('data/proxies');
        $config->setProxyNamespace('data/Proxies');
        $config->setHydratorDir('data/hydrators');
        $config->setHydratorNamespace('Hydrators');
        $config->setDefaultDB('user');
        $modelPath = realpath('data/document');

        $driver = new YamlDriver([$modelPath]);
        //$driver =AnnotationDriver::create([$modelPath]);
        $config->setMetadataDriverImpl($driver);
        //AnnotationDriver::registerAnnotationClasses();


        $dm = DocumentManager::create($connection, $config);
        //$dm->getSchemaManager()->ensureDocumentIndexes(\ZfeUser\Model\User::class);


        return $dm;
    }
}
