<?php

namespace App\Components\UI;

use Nette;
use Ublaboo\DataGrid\DataGrid;
use Nette\Application\UI\Control;

class SearchListGrid extends Control {
    /** @var Nette\Database\Context */
    protected $database;

    public function __construct(Nette\Database\Context $database) {
        $this->database = $database;
    }

    public function render() {
        $this->template->render(__DIR__ . '/search_control.latte');
    }
    
    public function createComponentSearchListGrid($name) {
        
        $grid = new DataGrid($this, $name);
        $grid->setPrimaryKey("id");
        $grid->setDataSource($this->database->table("records")->select("id, linka_text, kurz, vuz1, vuz2, datum")->order("id DESC"));
      //  $grid->setPagination(FALSE);
        
        $grid->setItemsPerPageList([10, 30, 50, 100]);
        
        $grid->addColumnText("linka", "Linka", "linka_text")
                ->setRenderer(function($item) {
                    return strtoupper($item->linka_text . " / " . $item->kurz);
                }, function ($item) {
                    return (bool) ($item->kurz <> 0);
                });
        
        $grid->addColumnText("vuz", "Vůz", "vuz1")
                ->setRenderer(function($item) {
                    return strtoupper($item->vuz1 . " + " . $item->vuz2);
                }, function($item) {
                    return (bool) ($item->vuz2 <> 0);
                });
        $grid->addColumnDateTime("datum", "Datum");
        
        $grid->addFilterText("linka", "Hledat", ["linka_text", "kurz"]);
        $grid->addFilterText("vuz", "Hledat", ["vuz1", "vuz2"]);
        $grid->addFilterRange("datum", "Datum:");
        

        //$grid->addFilterText("linka", "Hledat", "kurz");
        

    }
}



