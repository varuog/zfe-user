<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
 */

namespace ZfeUser\Model;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Description of Authentication
 *
 * @author Gourav Sarkar
 * @ODM\Document
 */
class Authentication {

	/** @ODM\ID */
	private $authToken;

	/** @ODM\Field(type="int") @ODM\Index */
	private $authTokenTime;

	/** @ODM\Field(type="string") @ODM\UniqueIndex */
	private $refreashToken;

	public function __construct( $authToken, $authTokenTime ) {
		$this->authToken	 = $authToken;
		$this->authTokenTime = $authTokenTime;
	}

	public function getAuthToken() {
		return $this->authToken;
	}

	public function getAuthTokenTime() {
		return $this->authTokenTime;
	}

	public function getRefreashToken() {
		return $this->refreashToken;
	}

	public function setAuthToken( $authToken ) {
		$this->authToken = $authToken;
		return $this;
	}

	public function setAuthTokenTime( $authTokenTime ) {
		$this->authTokenTime = $authTokenTime;
		return $this;
	}

	public function setRefreashToken( $refreashToken ) {
		$this->refreashToken = $refreashToken;
		return $this;
	}

}
