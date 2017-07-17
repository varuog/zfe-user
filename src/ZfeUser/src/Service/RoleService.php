<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ZfeUser\Service;

/**
 * Description of RoleService
 *
 * @author Win10Laptop-Kausik
 */
class RoleService {
	private $persistantManager;
	private $options;
	private $authUser;
	private $translator;
	private $mailer;
	private $mailerTemplate;
	private $events;
	//private $serverOptions;
	
	
	public function __construct( DocumentManager $mongoManager, TranslatorInterface $translator,
							  TransportInterface $mailer, TemplateRendererInterface $mailTemplate, UserServiceOptions $options ) {
		$this->persistantManager = $mongoManager;
		$this->options			 = $options;
		$this->translator		 = $translator;
		$this->mailer			 = $mailer;
		$this->mailerTemplate	 = $mailTemplate;
	}
	
	public function fetchRoles(array $roles)
	{
		
	}

}
