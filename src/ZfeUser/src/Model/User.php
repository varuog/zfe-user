<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ZfeUser\Model;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Description of User
 *
 * @author Gourav Sarkar
 * @ODM\Document
 * 
 */
class User {

	/** @ODM\Id(strategy="NONE") */
	private $id;

	/** @ODM\Field(type="string") @ODM\UniqueIndex */
	private $email;

	/** @ODM\Field(type="string") @ODM\Index(unique=true) */
	private $username;

	/** @ODM\Field(type="string") @ODM\Index(unique=true) */
	private $slug;

	/** @ODM\Field(type="string") */
	private $fullName;

	/** @ODM\Field(type="string") @ODM\UniqueIndex */
	private $password;

	/** @ODM\Field(type="string") @ODM\UniqueIndex */
	private $resetToken;

	/** @ODM\Field(type="int") @ODM\UniqueIndex */
	private $resetTokenTime;

	/** @ODM\Field(type="date") */
	private $approveTime;

	/** @ODM\Field(type="boolean") @ODM\Index */
	private $approved = false;

	/** @ODM\Field(type="boolean") @ODM\Index */
	private $emailVerified = false;

	/** @ODM\EmbedMany(targetDocument="Authentication") */
	private $authenticationInfo = [];
	private $roles;

	public function getId() {
		return $this->id;
	}

	public function getEmail() {
		return $this->email;
	}

	public function getUsername() {
		return $this->username;
	}

	public function getFullName() {
		return $this->fullName;
	}

	public function getPassword() {
		return $this->password;
	}

	public function getResetToken() {
		return $this->resetToken;
	}

	public function getResetTokenTime() {
		return $this->resetTokenTime;
	}

	public function setResetToken( $resetToken ) {
		$this->resetToken = $resetToken;
		return $this;
	}

	public function setResetTokenTime( $resetTokenTime ) {
		$this->resetTokenTime = $resetTokenTime;
		return $this;
	}

	public function setId( $id ) {
		$this->id = $id;
		return $this;
	}

	public function setEmail( $email ) {
		$this->email = $email;
		return $this;
	}

	public function setUsername( $username ) {
		$this->username = $username;
		return $this;
	}

	public function setFullName( $fullName ) {
		$this->fullName = $fullName;
		return $this;
	}

	public function setPassword( $password ) {
		$this->password = $password;
		return $this;
	}

	public function generateResetToken() {
		$this->resetToken = hash( 'sha256', random_int( PHP_INT_MIN, PHP_INT_MAX ) );
	}

	public function hashPassword() {
		$this->password = password_hash( $this->password, PASSWORD_DEFAULT );
	}

	public function getApproveTime() {
		return $this->approveTime;
	}

	public function getApproved() {
		return $this->approved;
	}

	public function getEmailVerified() {
		return $this->emailVerified;
	}

	public function setApproveTime( \DateTime $approveTime ) {
		$this->approveTime = $approveTime;
		return $this;
	}

	public function setApproved( $approved ) {
		$this->approved = $approved;
		return $this;
	}

	public function setEmailVerified( $emailVerified ) {
		$this->emailVerified = $emailVerified;
		return $this;
	}

	public function getSlug() {
		return $this->slug;
	}

	public function setSlug( $slug ) {
		$this->slug = $slug;
		return $this;
	}

	public function getRoles() {
		return $this->roles;
	}

	public function setRoles( $roles ) {
		$this->roles = $roles;
		return $this;
	}

	public function generateRefreashToken( Authentication $auth ) {
		//$refreshToken= new Authentication(hash('sha256', random_int(PHP_INT_MIN, PHP_INT_MAX)), time());
		throw new \BadMethodCallException( 'Method Not Implemented' );
	}

	public function generateAuthToken( array $serverInfo ) {
		$authToken					 = new Authentication( hash( 'sha256', random_int( PHP_INT_MIN, PHP_INT_MAX ) )
		, time()
		, $serverInfo[ 'REMOTE_ADDR' ]
		, $serverInfo[ 'HTTP_USER_AGENT' ] );
		$this->authenticationInfo[]	 = $authToken;
	}

	public function getAuthenticationInfo() {
		return $this->authenticationInfo;
	}

	
	/**
	 * 
	 * @return boolean
	 */
	public function getAuthToken( Authentication $authToken) {
		/*@var Authentication $authInfo */
		foreach ( $this->getAuthenticationInfo() as $authInfo ) {
			if ( $authInfo->getAuthToken() == $authToken->getAuthToken() )
				return $authInfo;
		}

		return false;
	}

}
