<?php

class BaseRepository extends Repository {

//Načítá BasePresenter
    /**
     * vymaže neaktivované uživatele starší jak den
     */
    public function removeNonActivated() {
        $this->database->table("temporary_users")
                ->where("time < NOW() - INTERVAL 1 DAY")
                ->delete();
        return true;
    }
    
    public function removeNonResetedPassword() {
        $this->database->table("password_reset")
                ->where("time < NOW() - INTERVAL 1 DAY")
                ->delete();
        return true;
    }

}
