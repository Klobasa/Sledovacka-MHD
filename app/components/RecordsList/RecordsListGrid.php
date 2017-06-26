<?php

namespace App\Components\UI;

use Nette;
use Ublaboo\DataGrid\DataGrid;
use Nette\Application\UI\Control;

class RecordsListGrid extends Control {

    /** @var Nette\Database\Context */
    protected $database;

    public function __construct(Nette\Database\Context $database) {
        $this->database = $database;
    }

    public function render() {
        $this->template->render(__DIR__ . '/records_control.latte');
    }

    public function createComponentRecordsListGrid($name) {
        //typ_vozidla, datum, autor, poznamka, potvrzeno, added

        $grid = new DataGrid($this, $name);
        $grid->setPrimaryKey("id");
        $grid->setDataSource($this->database->table("records")->select("*")->order("id DESC"));

        $grid->addColumnNumber("id", "ID")
                ->setSortable();
        $grid->addColumnNumber("linka", "Linka");
        $grid->addColumnText("linka_text", "Linka text");
        $grid->addColumnNumber("kurz", "Kurz");
        $grid->addColumnNumber("vuz1", "Vůz1");
        $grid->addColumnNumber("vuz2", "Vůz2");
        $grid->addColumnText("autor", "Uživatel");
        $grid->addColumnNumber("trakce", "Trakce")
                ->setReplacement([0 => "Neurčeno", 1 => "Tramvaj", 2 => "Trolejbus", 3 => "Autobus"])
                ->setFilterSelect(["" => "Vše", 0 => "Neurčeno", 1 => "Tramvaj", 2 => "Trolejbus", 3 => "Autobus",]);
        $grid->addColumnNumber("typ_vozidla", "Fyzické vozidlo")
                ->setReplacement([0 => "Neurčeno", 1 => "Tramvaj", 2 => "Trolejbus", 3 => "Autobus"])
                ->setFilterSelect(["" => "Vše", 0 => "Neurčeno", 1 => "Tramvaj", 2 => "Trolejbus", 3 => "Autobus",]);
        $grid->addFilterText("id", "ID");
        $grid->addFilterText("linka", "Hledat", ["linka_text", "linka",]);
        $grid->addFilterText("linka_text", "Hledat", ["linka_text", "linka",]);
        $grid->addFilterText("vuz1", "Hledat", ["vuz1", "vuz2",]);
        $grid->addFilterText("vuz2", "Hledat", ["vuz1", "vuz2",]);
        $grid->addFilterText("autor", "Autor");
    }

}
