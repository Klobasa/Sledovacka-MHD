<?php

namespace App\Components\UI;

use Nette;
use Ublaboo\DataGrid\DataGrid;
use Nette\Application\UI\Control;

class UserAddedListGrid extends Control {

    /** @var Nette\Database\Context */
    protected $database;
    protected $userID;

    public function __construct(Nette\Database\Context $database) {
        $this->database = $database;
    }

    public function render() {
        $this->template->render(__DIR__ . '/useradded_control.latte');
    }
    
    public function setUserId($Id) {
        $this->userID = $Id;
    }
    
    public function createComponentUserAddedListGrid($name) {
        //id, linka, linka_text, kurz, vuz1, vuz2, datum, potvrzeni
        
        $grid = new DataGrid($this, $name);
        $grid->setPrimaryKey("id");
        $grid->setDataSource($this->database->table("records")->select("*")->where("datum > NOW() - INTERVAL 3 DAY")->where("autor = ?", $this->userID)->order("id DESC")->limit(50));
        $grid->setPagination(FALSE);
        $grid->setItemsPerPageList([50]);
        
        $grid->addColumnNumber("id", "ID")
                ->setDefaultHide();
        $grid->addColumnNumber("linka", "Linka")
                ->setDefaultHide();
        $grid->addColumnText("linka_text", "Linka")
                ->setRenderer(function($item) {
                    return strtoupper($item->linka_text . " / " . $item->kurz);
                }, function ($item) {
                    return (bool) ($item->kurz <> 0);
                });
        $grid->addColumnNumber("vuz1", "Vozy")
                ->setRenderer(function($item) {
                    return strtoupper($item->vuz1 . " + " . $item->vuz2);
                }, function ($item) {
                    return (bool) ($item->vuz2 <> 0);
                });

        $grid->addColumnDateTime("datum", "Datum");
        $grid->addColumnNumber("typ_vozidla", "Fyzické vozidlo")
                ->setReplacement([0 => "Neurčeno", 1 => "Tramvaj", 2 => "Trolejbus", 3 => "Autobus"]);
        
        
        $grid->addInlineEdit()
                ->onControlAdd[] = function($container) {
            $container->addText("linka_text", "");
            $container->addText("vuz1", "");
            $container->addText("datum", "");
            $container->addSelect("typ_vozidla", "", [
                '0' => 'Neurčeno',
                '1' => 'Tramvaj',
                '2' => 'Trolejbus',
                '3' => 'Autobus'
            ]);
            };
                
        $grid->getInlineEdit()->onSetDefaults[] = function($container, $item) {
            $container->setDefaults([
                "linka_text" => $item->linka_text."/".$item->kurz,
                "vuz1" => $item->vuz1."+".$item->vuz2,
                "datum" => substr($item->datum, 0, 10),
                "typ_vozidla" => $item->typ_vozidla
            ]);
        }; 
        
        $p = $this;
        $grid->getInlineEdit()->onSubmit[] = function($id, $values) use ($p) {
            $values->linka_text = str_replace(" ", "", $values->linka_text);
            $values->vuz1 = str_replace(" ", "", $values->vuz1);
            $values->datum = substr($values->datum, 0, 10);
            
            $linka = explode("/", $values->linka_text);
            $vozy = explode("+", $values->vuz1);

            $newValues = ["linka_text" => intval($linka[0]),
                "kurz" => (isset($linka[1]) ? intval($linka[1]) : 0),
                "vuz1" => intval($vozy[0]),
                "vuz2" => (isset($vozy[1]) ? intval($vozy[1]) : 0),
                "datum" => date('Y-m-d',strtotime($values->datum)),
                "typ_vozidla" => $values->typ_vozidla
            ];

            $arrayHash = \Nette\Utils\ArrayHash::from($newValues, true);

            
            $this->database->table("records")->where("id = ?", $id)->update($arrayHash);
            
            $p->flashMessage("Záznam aktualizován");
            $p->redrawControl("flashes");
        };
    }
}