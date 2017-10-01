<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ZfeUser\Model;

use \Zend\Permissions\Rbac\Role as ZfRole;

/**
 * Description of Social
 *
 * @author LaptopRK
 */
class Role extends ZfRole {

    protected $priority = 0;

    public function setPriority($priority): Role {
        $this->priority = $priority;
        return $this;
    }

    public function getPriority(): int {
        return $this->priority;
    }

    public function setName($name): Role {
        $this->name = $name;
        return $this;
    }

    public function getPermissions(): array {
        return $this->permissions;
    }

    public function getParent(): Role {
        return parent::getParent();
    }

}
