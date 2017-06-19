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
 */
class User {

    /** @ODM\Id(strategy="NONE") */
    private $id;

    /** @ODM\Field(type="string") @ODM\UniqueIndex */
    private $email;

    /** @ODM\Field(type="string") @ODM\Index(unique=true, dropDups=true) */
    private $username;

    /** @ODM\Field(type="string") */
    private $fullName;

    /** @ODM\Field(type="string") @ODM\UniqueIndex */
    private $password;

    /** @ODM\Field(type="integer") @ODM\UniqueIndex */
    private $authToken;

    /** @ODM\Field(type="int") @ODM\UniqueIndex */
    private $authTokenTime;

    /** @ODM\Field(type="string") @ODM\UniqueIndex */
    private $refreashToken;

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

    public function getAuthToken() {
        return $this->authToken;
    }

    public function getAuthTokenTime() {
        return $this->authTokenTime;
    }

    public function getResetToken() {
        return $this->resetToken;
    }

    public function getResetTokenTime() {
        return $this->resetTokenTime;
    }

    public function setAuthToken($authToken) {
        $this->authToken = $authToken;
        return $this;
    }

    public function setAuthTokenTime($authTokenTime) {
        $this->authTokenTime = $authTokenTime;
        return $this;
    }

    public function setResetToken($resetToken) {
        $this->resetToken = $resetToken;
        return $this;
    }

    public function setResetTokenTime($resetTokenTime) {
        $this->resetTokenTime = $resetTokenTime;
        return $this;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function setEmail($email) {
        $this->email = $email;
        return $this;
    }

    public function setUsername($username) {
        $this->username = $username;
        return $this;
    }

    public function setFullName($fullName) {
        $this->fullName = $fullName;
        return $this;
    }

    public function setPassword($password) {
        $this->password = $password;
        return $this;
    }

     public function generateResetToken() {
        $this->resetToken = hash('sha256', random_int(PHP_INT_MIN, PHP_INT_MAX));
    }
    
    public function generateRefreashToken() {
        $this->refreashToken = hash('sha256', random_int(PHP_INT_MIN, PHP_INT_MAX));
    }
    
     public function generateAuthToken() {
        $this->authToken = hash('sha256', random_int(PHP_INT_MIN, PHP_INT_MAX));
    }

    public function hashPassword() {
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
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

    public function setApproveTime(\DateTime $approveTime) {
        $this->approveTime = $approveTime;
        return $this;
    }

    public function setApproved($approved) {
        $this->approved = $approved;
        return $this;
    }

    public function setEmailVerified($emailVerified) {
        $this->emailVerified = $emailVerified;
        return $this;
    }

    public function getRefreashToken() {
        return $this->refreashToken;
    }

    public function getRoles() {
        return $this->roles;
    }

    public function setRefreashToken($refreashToken) {
        $this->refreashToken = $refreashToken;
        return $this;
    }

    public function setRoles($roles) {
        $this->roles = $roles;
        return $this;
    }

}
