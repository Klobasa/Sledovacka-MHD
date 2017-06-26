<?php

namespace App\Components\UI;

use Nette;
use Ublaboo\DataGrid\DataGrid;
use Nette\Application\UI\Control;

class AdminUsersGrid extends Control {

    /** @var Nette\Database\Context */
    protected $database;

    public function __construct(Nette\Database\Context $database) {
        $this->database = $database;
    }

    public function render() {
        $this->template->render(__DIR__ . '/adminUsers_control.latte');
    }

    public function createComponentAdminUsersGrid($name) {
        $grid = new DataGrid($this, $name);


        $grid->setPrimaryKey("id");

        $grid->setDataSource($this->database->table("users")->select("*"));

        $grid->addColumnNumber("id", "ID")
                ->setSortable();
        $grid->addColumnText("username", "Uživatelské jméno")
                ->setSortable();
        $grid->addColumnText("name", "Skutečné jméno")
                ->setSortable();
        $grid->addColumnText("place", "Bydliště")
                ->setSortable();

        $grid->addColumnNumber("role", "Oprávnění")
                ->setReplacement([
                    0 => "Host",
                    1 => "Prezentace",
                    3 => "Člen",
                    5 => "VIP Člen",
                    10 => "Trial Admin",
                    15 => "Admin",
                    50 => "Správce"
                ])
                ->setFilterSelect([
                    "" => "Vše",
                    0 => "Host",
                    1 => "Prezentace",
                    3 => "Člen",
                    5 => "VIP Člen",
                    10 => "Trial Admin",
                    15 => "Admin",
                    50 => "Správce"
        ]);

        $grid->addColumnDateTime("registered", "Registrován")
                ->setFilterDateRange();
        
        $grid->addColumnDateTime("lastLogin", "Poslední přihlášení")
                ->setFilterDateRange();
        
        $grid->addColumnText("email", "E-mail")
                ->setSortable();
        
        
        $grid->addColumnStatus("banned", "Ban")
                        ->addOption(0, "Ne")
                        ->setClass("btn-success")
                        ->endOption()
                        ->addOption(1, "Zabanován")
                        ->setClass("btn-danger")
                        ->endOption()
                ->onChange[] = [$this, 'changeLineVisibility'];


        $grid->addInlineEdit()
                ->onControlAdd[] = function($container) {
            $container->addText('username', '');
            $container->addText('name', '');
            $container->addText('place', '');
            $container->addSelect('role', '', [
                0 => "Host",
                    1 => "Prezentace",
                    3 => "Člen",
                    5 => "VIP Člen",
                    10 => "Trial Admin",
                    15 => "Admin",
                    50 => "Správce"
            ]);

            $container->addText('email', '');
            $container->addSelect('banned', '', [
                '0' => 'Ne',
                '1' => 'Zabanován',
            ]);
        };

        $grid->getInlineEdit()->onSetDefaults[] = function($container, $item) {
            $container->setDefaults([
                'username' => $item->username,
                'name' => $item->name,
                'place' => $item->place,
                'role' => $item->role,
                'email' => $item->email,
                'banned' => $item->banned,
            ]);
        };

        $p = $this;

        $grid->getInlineEdit()->onSubmit[] = function($id, $values) use ($p) {
            $this->database->table("users")->where("id = ?", $id)->update($values);

            $p->flashMessage('Record was updated!', 'success');
            $p->redrawControl('flashes');
        };
    }

    public function changeLineVisibility() {

    }

}
