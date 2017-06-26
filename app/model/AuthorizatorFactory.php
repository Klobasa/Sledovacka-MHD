<?php

namespace App\Model;

use Nette;

class AuthorizatorFactory {
    /** @return Nette\Security\Permission */
    public static function create() {
        $acl = new Nette\Security\Permission;
        
        $acl->addRole("1"); //Prezentace
        $acl->addRole("3"); //Člen
        $acl->addRole("5"); //VIP
        $acl->addRole("10"); //Trial Admin
        $acl->addRole("15"); //Admin
        $acl->addRole("50"); //Správce
        
        $acl->addResource("backend");
        $acl->addResource("usersettings");
        
        $acl->deny("1", "usersettings");
        $acl->allow("3", "usersettings");
        $acl->deny("3", "backend");
        $acl->allow("10", ["backend", "usersettings"]);
        
        return $acl;        
    }
}




