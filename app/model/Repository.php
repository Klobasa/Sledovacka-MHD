<?php

abstract class Repository extends Nette\Object {

    /** @var Nette\Database\Context */
    protected $database;

    public function __construct(Nette\Database\Context $database) {
        $this->database = $database;
    }

    /* Vrátí info o uživateli */

    public function getUsername($userId) {
        return $this->database->table("users")->where("id", $userId)->fetch();
    }
    
    /**
     * Vrací název role 
     * @param type $role
     * @return type
     */
    public function getRoleName($role) {
        return $this->database->table("roles")->where("number", $role)->fetch();
    }
            
           

}
