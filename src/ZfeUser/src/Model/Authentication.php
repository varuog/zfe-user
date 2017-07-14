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
 * @ODM\EmbeddedDocument
 */
class Authentication {

	/** @ODM\Field(type="string") @ODM\UniqueIndex */
	private $authToken;

	/** @ODM\Field(type="int") @ODM\Index */
	private $authTokenTime;

	/** @ODM\Field(type="string") @ODM\UniqueIndex */
	private $refreashToken;

	/** @ODM\Field(type="string") @ODM\UniqueIndex */
	private $logIp;

	/** @ODM\Field(type="string") @ODM\Index */
	private $deviceID;

	/**
	 * 
	 * @todo IP Address should be converted to ineteger
	 * @param type $authToken
	 * @param type $authTokenTime
	 * @param type $ip
	 * @param type $device
	 */
	public function __construct( $authToken, $authTokenTime, $ip, $device ) {
		$this->authToken	 = $authToken;
		$this->authTokenTime = $authTokenTime;
		$this->logIp		 = $ip;
		$this->deviceID		 = $device;
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

	public function getLogIp() {
		return $this->logIp;
	}

	public function getDeviceID() {
		return $this->deviceID;
	}

}
