<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ZfeUser\Model;

/**
 * Description of Social
 *
 * @author LaptopRK
 */
class Social
{
   

        
    private $providerName;
    private $id;
    private $accessToken;
    private $accessTokenSecret;

    public function __construct($id, $providerName, $accesstoken, $accessTokenSecret='')
    {
        $this->id=$id;
        $this->providerName= $providerName;
        $this->accessToken=$accesstoken;
        $this->accessTokenSecret=$accessTokenSecret;
    }
    public function getProviderName()
    {
        return $this->providerName;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAccessToken()
    {
        return $this->accessToken;
    }

    public function setProviderName($providerName)
    {
        $this->providerName = $providerName;
        return $this;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
        return $this;
    }
    
     public function getAccessTokenSecret()
    {
        return $this->accessTokenSecret;
    }

    public function setAccessTokenSecret($accessTokenSecret)
    {
        $this->accessTokenSecret = $accessTokenSecret;
        return $this;
    }
}
