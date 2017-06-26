<?php

class AdminRepository extends Repository {

    const
            USERS_TABLE_NAME = "users",
            RECORD_TABLE_NAME = "records";

    /**
     * Get number of all users
     * @return Users in database
     */
    public function getNumberOfUsers() {
        $users = $this->database->table(self::USERS_TABLE_NAME)
                ->count();
        return $users;
    }

    public function getNumberOfActiveUsers() {
        $date = new DateTime("-1 month");
        $users = $this->database->table(self::USERS_TABLE_NAME)
                ->where("DATE(lastLogin) >= ?", $date->format("Y-m-d"))
                ->count();
        return $users;
    }

    public function getNumberOfNewUsers() {
        $date = new DateTime("-1 month");
        $users = $this->database->table(self::USERS_TABLE_NAME)
                ->where("DATE(registered) >= ?", $date->format("Y-m-d"))
                ->count();
        return $users;
    }

    public function getNumberOfRecords($number, $type) {
        $date = new DateTime("-" . $number . " " . $type);
        $users = $this->database->table(self::RECORD_TABLE_NAME)
                ->where("DATE(added) >= ?", $date->format("Y-m-d"))
                ->count();
        return $users;
    }

    public function database() {
        return $this->database;
    }

}
