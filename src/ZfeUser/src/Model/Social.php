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

}
