<?php

use Nette\Mail\Message;

class RegisterRepository extends Repository {
    
    private $temporary = "temporary_users";
    

    public function activateUser($token) {
        $registered = $this->database->table($this->temporary)->where('token', $token)->fetch();

        if ($registered) {
            $register = $this->database->table($this->temporary)
                    ->select("id, username, email, password, time")
                    ->where("token = ?", $token)
                    ->limit(1)
                    ->fetch();

            $this->database->table("users")->insert(array(
                "username" => $register->username,
                "email" => $register->email,
                "password" => $register->password,
                "registered" => $register->time
            ));

            $this->database->table($this->temporary)
                    ->where("id = ?", $register->id)
                    ->delete();

            return true;
        } else {
            return false;
        }
    }
    
        /**
     * Generuje registrační email
     * @param type 
     * @return type
     */
    public function generateEmail($email) {  

        $values = $this->database->table('temporary_users')
               ->select('username, email, token, time')
                ->where('email', $email)
               ->limit(1)
                       ->fetch();
        return $values;
    }

}
