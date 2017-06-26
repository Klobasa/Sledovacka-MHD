<?php

class HomepageModel extends Repository {

    private $recordsTable = "records";

    //vytvoří datum v českém formátu (např.: 25. března)
    public function createDate() {
        $aj = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
        $cz = array("ledna", "února", "března", "dubna", "května", "června", "července", "srpna", "září", "října", "listopadu", "prosince");

        for ($i = 0; $i > -7; $i--) {
            $date = new DateTime($i . ' days');
            $czDate = $date->format("d. F");
            $czDate = str_replace($aj, $cz, $czDate);

            $array[] = $czDate;
        }
        return $array;
    }

    //převede anglický den na český
    public function createDay() {
        $ajd = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
        $czd = array("Pondělí", "Úterý", "Středa", "Čtvrtek", "Pátek", "Sobota", "Neděle");

        for ($i = 0; $i > -7; $i--) {
            $day = new DateTime($i . ' days');
            $czDay = $day->format("l");
            $czDay = str_replace($ajd, $czd, $czDay);

            $array[] = $czDay;
        }
        return $array;
    }

    //hledá v databázi tramvaje
    public function searchTram() {

        for ($i = 0; $i > -7; $i--) {
            $date = new DateTime($i . ' days');
            $trams = $this->database->table($this->recordsTable)
                    ->select("linka_text, kurz, vuz1, vuz2, autor, poznamka")
                    ->where("DATE(datum) = ? AND trakce = ?", $date->format("Y-m-d"), 1)
                    ->order("linka, kurz, vuz1");

            if (count($trams) > 0) {
                $array[] = $trams;
            } else {
                $array[] = null;
            }
        }
        return $array;
    }

    //hledá v databázi trolejbusy
    public function searchTrolley() {

        for ($i = 0; $i > -7; $i--) {
            $date = new DateTime($i . ' days');
            $trolleys = $this->database->table($this->recordsTable)
                    ->select("linka_text, kurz, vuz1, autor, poznamka")
                    ->where("DATE(datum) = ? AND trakce = ?", $date->format("Y-m-d"), 2)
                    ->order("linka, kurz, vuz1");
        if (count($trolleys) > 0) {
                $array[] = $trolleys;
            } else {
                $array[] = null;
            }
        }
        return $array;
    }

    /**
     * Hledá v databázi autobusy
     * @return type
     */
    public function searchBus() {

        for ($i = 0; $i > -7; $i--) {
            $date = new DateTime($i . ' days');
            $bus = $this->database->table($this->recordsTable)
                    ->select("linka_text, kurz, vuz1, autor, poznamka")
                    ->where("DATE(datum) = ? AND trakce = ?", $date->format("Y-m-d"), 3)
                    ->order("linka, kurz, vuz1");
            
        if (count($bus) > 0) {
                $array[] = $bus;
            } else {
                $array[] = null;
            }
        }
        return $array;
    }

}
