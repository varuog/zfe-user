<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ZfeUser\Service;

use Doctrine\ODM\MongoDB\DocumentManager;
use ZfeUser\Model\User;
use ZfeUser\Options\UserServiceOptions;
use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\Mail\Transport\TransportInterface;
use Zend\Mail\Message;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Permissions\Rbac\AssertionInterface;
use ZfeUser\Model\Role;
use Zend\Permissions\Rbac\Rbac;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Description of RoleService
 *
 * @author Gourav Sarkar
 */
class RoleService {

    private $persistantManager;
    private $options;
    private $authUser;
    private $translator;
    private $mailer;
    private $mailerTemplate;
    private $events;
    private $rbac;

    //private $serverOptions;


    public function __construct(
    DocumentManager $mongoManager, TranslatorInterface $translator, TransportInterface $mailer, TemplateRendererInterface $mailTemplate, UserServiceOptions $options
    ) {
        $this->persistantManager = $mongoManager;
        $this->options = $options;
        $this->translator = $translator;
        $this->mailer = $mailer;
        $this->mailerTemplate = $mailTemplate;
        $this->rbac = new Rbac();
    }

    public function fetchRoles(Collection $roles) : Collection{
        $roleList = new ArrayCollection();
        foreach ($roles as $role) {
            $roleIdList[] = $role->getName();
        }
        $roles = $this->persistantManager->createQueryBuilder(Role::class)
                ->field('name')
                ->in($roleIdList)
                ->getQuery()
                ->execute();

        foreach ($roles as $role) {
            $roleList->add($role);
        }


        return $roleList;
    }

    public function fetchRoleNames(Collection $roles) {
        $roleNames = [];
        $fetchedRoles = $this->fetchRoles($roles);

        foreach ($fetchedRoles as $role) {
            $roleNames[] = $role->getName();
        }
        return $roleNames;
    }

    /**
     *
     * @param Role $role
     */
    public function add(Role $role) {
        $ancestorNames = [];
        /**
         * Check if role has parent
         */
        /*
        $parentRole = $role->getParent();
        $parentRole = $this->fetchRoles([$parentRole])[0];
        $ancestors = $parentRole->getAncestors();
        foreach ($ancestors as $ancetor) {
            $ancestorNames[] = $ancetor->getName();
        }
         * 
         */
        //Populate ancestor names
        //$role->setAncestors(array_merge($ancestorNames, $parentRole->getName()));

        $this->persistantManager->getSchemaManager()->ensureIndexes();
        $this->persistantManager->persist($role);
        $this->persistantManager->flush($role, ['safe' => true]);
    }

    /**
     * 
     * @param type $role
     * @param type $permission
     * @param type $assert
     */
    public function isGranted(Collection $roles, $permission, $assert = null) {
        $hasPermission=FALSE;
        /*
         * Fetch all the ancestors role and current role and add it to rbac
         */
        foreach ($roles as $subrole) {
            $this->rbac->addRole($subrole);
            $hasPermission=$this->rbac->isGranted($subrole, $permission, $assert);
            
        }
        
        return $hasPermission;
        
    }

}
