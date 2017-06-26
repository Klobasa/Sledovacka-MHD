<?php

namespace App\Components\UI;

use Nette;
use Ublaboo\DataGrid\DataGrid;
use Nette\Application\UI\Control;

class LinesListGrid extends Control {

    /** @var Nette\Database\Context */
    protected $database;

    public function __construct(Nette\Database\Context $database) {
        $this->database = $database;
    }

    public function render() {
        $this->template->render(__DIR__ . '/lines_control.latte');
    }

    public function createComponentLinesListGrid($name) {
        $grid = new DataGrid($this, $name);


        $grid->setPrimaryKey("cislo");

        $grid->setDataSource($this->database->table("lines")->select("*"));

        $grid->addColumnNumber("cislo", "Číslo")
                ->setSortable();



        $grid->addColumnText("linka", "Linka")
                ->setSortable();


        $grid->addColumnNumber("skupina", "Skupina v menu")
                ->setReplacement([
                    1 => "Tramvaj",
                    2 => "Trolejbus",
                    3 => "Autobus",
                    4 => "Noční",
                    5 => "Speciální",
                    6 => "Výluka"
                ])
                ->setFilterSelect([
                    "" => "Vše",
                    1 => "Tramvaj",
                    2 => "Trolejbus",
                    3 => "Autobus",
                    4 => "Noční",
                    5 => "Speciální",
                    6 => "Výluka"
        ]);

        $grid->addColumnNumber("trakce", "Trakce")
                ->setReplacement([
                    0 => "Neurčeno",
                    1 => "Tramvaj",
                    2 => "Trolejbus",
                    3 => "Autobus"
                ])
                ->setFilterSelect([
                    "" => "Vše",
                    0 => "Neurčeno",
                    1 => "Tramvaj",
                    2 => "Trolejbus",
                    3 => "Autobus"
        ]);

        $grid->addColumnStatus("visible", "Status")
                        ->addOption(1, "Aktivní")
                        ->setClass("btn-success")
                        ->endOption()
                        ->addOption(0, "Neaktivní")
                        ->setClass("btn-danger")
                        ->endOption()
                ->onChange[] = [$this, 'changeLineVisibility'];


        $grid->addFilterText("linka", "Hledat", ["cislo", "linka",]);
        $grid->addFilterText("cislol", "Hledat", ["cislo", "linka",]);


        $grid->addInlineEdit()
                ->onControlAdd[] = function($container) {
            $container->addText('cislo', '');
            $container->addText('linka', '');
            $container->addSelect('skupina', '', [
                1 => "Tramvaj",
                2 => "Trolejbus",
                3 => "Autobus",
                4 => "Noční",
                5 => "Speciální",
                6 => "Výluka"
            ]);

            $container->addSelect('trakce', '', [
                '0' => 'Nic',
                '1' => 'Tramvaj',
                '2' => 'Trolejbus',
                '3' => 'Autobus'
            ]);

            $container->addSelect('visible', '', [
                '1' => 'Aktivní',
                '0' => 'Neaktivní',
            ]);
        };

        $grid->getInlineEdit()->onSetDefaults[] = function($container, $item) {
            $container->setDefaults([
                'cislo' => $item->cislo,
                'linka' => $item->linka,
                'skupina' => $item->skupina,
                'trakce' => $item->trakce,
                'visible' => $item->visible,
            ]);
        };

        $p = $this;

        $grid->getInlineEdit()->onSubmit[] = function($id, $values) use ($p) {
            $this->database->table("lines")->where("cislo = ?", $id)->update($values);

            $p->flashMessage('Record was updated!', 'success');
            $p->redrawControl('flashes');
        };
    }

    public function changeLineVisibility() {
        
    }

}
