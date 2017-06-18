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

/**
 * Description of MongoDocumentManagerFactory
 *
 * @author Win10Laptop-Kausik
 */
class MongoDocumentManagerFactory {

    public function __invoke(ContainerInterface $container) {
        $connection = new \Doctrine\MongoDB\Connection();
        $config = new \Doctrine\ODM\MongoDB\Configuration();

        $config->setProxyDir('data/proxies');
        $config->setProxyNamespace('data/Proxies');
        $config->setHydratorDir('data/hydrators');
        $config->setHydratorNamespace('Hydrators');
        $config->setDefaultDB('user');


        $config->setMetadataDriverImpl(AnnotationDriver::create(['data/document']));
        AnnotationDriver::registerAnnotationClasses();


        $dm = DocumentManager::create($connection, $config);
        $dm->getSchemaManager()->ensureIndexes();

        return $dm;
    }

}
