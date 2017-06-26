<?php

class UserRepository extends Repository {

    const
            RECORD_TABLE_NAME = "records",
            USERS_TABLE_NAME = "users",
            ROLES_TABLE_NAME = "roles";
    
    

    public function getNumberOfRecords($id) {

        $count = $this->database->table(self::RECORD_TABLE_NAME)
                ->where("autor = ?", $id)
                ->count();
        return $count;
    }
    
    public function setLastLogin($id) {
        $this->database->table(self::USERS_TABLE_NAME)
            ->where("id", $id)
                ->update(array("lastLogin" => (date("Y-m.d H:i:s"))));
    }
    
    public function updateUserInfo($values, $id) {
        $this->database->table(self::USERS_TABLE_NAME)
                ->where("id", $id)
                ->update(["place" => $values->bydliste, "name" => $values->jmeno]);
    }
    
    public function insertRequest($email) {
        try {
            
            $this->database->table("password_reset")->where("email", $email)->delete();
            $this->database->table("password_reset")->insert(array(
                "email" => $email,
                "token" => $this->generateToken(),
            ));
            return false;
        } catch (Nette\InvalidArgumentException $e) {
            return true;
        }
    }
    
      //vygeneruje náhodný token pro link emailu
    public function generateToken($length = 25) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    
    public function getPasswordResetData($email) {
        $values = $this->database->table("password_reset")
                ->where("email", $email)
                ->select("email, token, time")
                ->limit(1)
                ->fetch();
        return $values;
    }
    
    public function verifyEmail($email) {
        $row = $this->database->table(self::USERS_TABLE_NAME)
                ->where("email", $email)
                ->count();
        
        if ($row > 0) {
            return true;
        } else {
            return false;
        }  
    }
    
    public function verifyPasswordToken($token, $email) {
        $row = $this->database->table("password_reset")
                ->where("token", $token)
                ->where("email", $email)
                ->count();
        
        if ($row > 0) {return true;
        } else {return false;}  
    }
    
    public function resetEmailPassword($values) {
        $row = $this->database->table(self::USERS_TABLE_NAME)
                ->where("email", $values->email)
                ->update(["password" => Nette\Security\Passwords::hash($values->heslo)]);
        
        $this->database->table("password_reset")
                ->where("email", $values->email)
                ->delete();
        
        if ($row > 0) {return true;
        } else {return false;} 
    }
    
    public function verifyPassword($password, $id) {
        $row = $this->database->table(self::USERS_TABLE_NAME)
                ->where("id", $id)->fetch();
        
        if (Nette\Security\Passwords::verify($password, $row["password"])) {
            return true;
        } else {
            return false;
        }
    }
}
