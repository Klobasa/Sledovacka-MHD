<?php

class InsertRepository extends Repository {

    private $recordsTable = "records";

    public function getLinesList() {
        $group = array('Tramvaje', 'Trolejbusy', 'Autobusy', 'Noční', 'Speciální', 'Výluka');
        $dbLineList = $this->database->table("lines")->select("*")->where("visible = ?", 1);

        foreach ($dbLineList as $line) {
            //$lineList[$group[$line->skupina - 1]] = [Nette\Utils\Html::el("option")->value($line->cislo)->setHtml($line->linka)];

            $lineList[$group[$line->skupina - 1]][$line->cislo] = Nette\Utils\Html::el()->setText($line->linka)->data("vozidlo", $line->trakce);
        }
        return $lineList;
    }

    /**
     * Aktualizace kurzu
     * @param type $values
     */
    public function updateSequence($values) {
        $this->database->table($this->recordsTable)
                ->where("linka", $values->line)
                ->where("vuz1", $values->vehicle1)
                ->where("vuz2", $values->vehicle2)
                ->where("datum", $values->date)
                ->update(array("kurz" => $values->sequence));
    }

    /**
     *
     * @param type $values
     * @param type $check
     */
    public function confirmRecord($values, $check) {
        $this->database->table($this->recordsTable)
                ->where("linka", $values->line)
                ->where("vuz1", $values->vehicle1)
                ->where("vuz2", $values->vehicle2)
                ->where("datum", $values->date)
                ->update(array("potvrzeni" => ($check->potvrzeni + 1)));
    }

    /**
     * kontrola, jestli už daný spoj neni v db
     * @param type $values
     * @param type $tableDate
     * @return boolean or array
     */
    public function checkData($values) {
        $added = $this->database->table($this->recordsTable)
                ->where("linka", $values->line)
                ->where("vuz1", $values->vehicle1)
                ->where("vuz2", $values->vehicle2)
                ->where("datum", $values->date)
                ->fetch();

        if ($added == TRUE) {
            return $added;
        } else {
            return FALSE;
        }
    }

    /**
     * Vloží záznam do databáze
     * @param type $values
     * @return boolean
     */
    public function insertRecordInDatabase($values, $id) {
        //získání trakce z databáze
        $traction = $this->database->table("lines")->get($values->line);

        if ($this->database->table($this->recordsTable)->insert([
                    "linka" => $values->line,
                    "linka_text" => $traction->cislo,
                    "kurz" => $values->sequence,
                    "vuz1" => $values->vehicle1,
                    "vuz2" => $values->vehicle2,
                    "trakce" => $traction->trakce,
                    "datum" => $values->date,
                    "autor" => $id,
                    "poznamka" => $values->poznamka,
                    "typ_vozidla" => $values->typVozu
                    ])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Rozdělí 2 vozy
     * @param type $vehicles
     * @return boolean
     */
    public function divideVehicles($vehicles) {
        if (strlen($vehicles) > 3) {
            if (strlen($vehicles) == 6) {
                $vehicle1 = substr($vehicles, 0, 3);
                $vehicle2 = substr($vehicles, 3, 6);
            } else if (strlen($vehicles) == 7) {
                $vehicle1 = substr($vehicles, 0, 3);
                $vehicle2 = substr($vehicles, 4, 7);
            } else {
                return false;
            }
        } else if (strlen($vehicles) == 3) {
            $vehicle1 = $vehicles;
            $vehicle2 = 0;
        } else {
            return false;
        }
        return array("vehicle1" => $vehicle1, "vehicle2" => $vehicle2);
    }
    
       //hledá v databázi záznamy uživatele
    public function searchInsertDataByUser($userID) {
            $search = $this->database->table($this->recordsTable)
                    ->select("linka_text, kurz, vuz1, vuz2, datum, poznamka, potvrzeni")
                        ->where("autor = ?", $userID)
                    ->order("datum, added")
                    ->limit(50);

            if (count($search) > 0) {
                $array = $search;
            } else {
                $array = null;
            }

        return $array;
    }

}
