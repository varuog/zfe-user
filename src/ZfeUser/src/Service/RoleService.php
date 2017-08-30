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
use Zend\Permissions\Rbac\Role;
use ZfeUser\Model\Role as ModelRole;

/**
 * Description of RoleService
 *
 * @author Gourav Sarkar
 */
class RoleService
{

    private $persistantManager;
    private $options;
    private $authUser;
    private $translator;
    private $mailer;
    private $mailerTemplate;
    private $events;

    //private $serverOptions;


    public function __construct(
    DocumentManager $mongoManager, TranslatorInterface $translator, TransportInterface $mailer, TemplateRendererInterface $mailTemplate, UserServiceOptions $options
    )
    {
        $this->persistantManager = $mongoManager;
        $this->options = $options;
        $this->translator = $translator;
        $this->mailer = $mailer;
        $this->mailerTemplate = $mailTemplate;
    }

    public function fetchRoles(array $roles)
    {
        $roleList=[];
        foreach ($roles as $role) {
            $roleIdList[] = $role->getName();
        }
        $roles = $this->persistantManager->createQueryBuilder(ModelRole::class)
                ->field('name')
                ->in($roleIdList)
                ->getQuery()
                ->execute();

        foreach ($roles as $role)
        {
            $roleList[]=$role;
        }
        

        return $roleList;
    }
    
    public function fetchRoleNames(array $roles)
    {
        $roleNames=[];
        $fetchedRoles=$this->fetchRoles($roles);

        foreach($fetchedRoles as $role)
        {
            $roleNames[]=$role->getName();
        }
        return $roleNames;
    }

    /**
     *
     * @param Role $role
     */
    public function add(Role $role)
    {
        $this->persistantManager->getSchemaManager()->ensureIndexes();
        $this->persistantManager->persist($role);
        $this->persistantManager->flush($role, ['safe' => true]);
    }

}
