<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use Nette\Utils\DateTime;
use Tomaj\Form\Renderer\BootstrapVerticalRenderer;

class InsertPresenter extends BasePresenter {
    
     /**
     * @var \App\Components\UserAddedListControlFactory
     * @inject
     */
    public $UserAddedListControlFactory;

    private $insertModel;

    public function __construct(\InsertRepository $insert) {
        $this->insertModel = $insert;
    }

    public function renderDefault() {
        if ($this->getUser()->getId() <> NULL) {
        $time = $this->template->userInserts = $this->insertModel->searchInsertDataByUser($this->getUser()->getId());
        }
        //$this->template->datum = date("d. m. Y", $time->datum);
    }

    protected function createComponentInsertForm() {
        $date = new DateTime();

        $form = new Form;
        $form->setRenderer(new BootstrapVerticalRenderer);

        $form->addSelect("line", "Linka:", $this->insertModel->getLinesList())
                ->setPrompt("Vyberte linku");


        //       $form->addText("line", "Linka:")
        //               ->setType("number")
        //               ->setRequired("Vyplňte linku");

        $form->addText("sequence", "Kurz:")
                ->setType("number");

        $form->addText("vehicles", "Vozy:")
                ->setOption("description", "např.: 452 nebo 452453 nebo 452+453")
                ->setRequired("Vyplňte vozy");

        $form->addText("date", "Datum:")
                ->setType("date")
                ->setDefaultValue($date->format("Y-m-d"))
                ->setRequired("Vyberte datum")
                ->setOption("description", "např: " + $date->format("Y-m-d"));


        $form->addText("poznamka", "Poznámka:")
                ->setOption("description", "max. 250 znaků")
                ->setRequired(FALSE)
                ->addRule(Form::MAX_LENGTH, "Poznámka je přílíš dlouhá", 250);

        $form->addRadioList("typVozu", "Vozidlo:", [
                    1 => "Tramvaj",
                    2 => "Trolejbus",
                    3 => "Autobus"
                ])
                ->setOption("id", "typVozu")
                ->setRequired("Vyberte typ vozidla");

        $form->addSubmit("send", "Uložit");

        $form->onSubmit[] = array($this, "insertFormSucceeded");
        return $form;
    }

    public function insertFormSucceeded($form) {
        $values = $form->getValues();
        $vehicles = preg_replace('/\s+/', '', $values->vehicles);

        if ($this->insertModel->divideVehicles($vehicles)) {
            $separateVehicles = $this->insertModel->divideVehicles($vehicles);
            $values->vehicle1 = $separateVehicles["vehicle1"];
            $values->vehicle2 = $separateVehicles["vehicle2"];
        } else {
            $form->addError("Chybně zadané vozy.");
        }

        if (!$form->hasErrors()) {
            //kontrola, jestli už není záznam v DB
            $check = $this->insertModel->checkData($values);

            //pokud není, vloží se do databáze
            if (!$check) {
                if ($this->insertModel->insertRecordInDatabase($values, $this->getUser()->getId())) {
                    $this->flashMessage("Spoj byl úspěšně vložen", "success"); //flashová informace - app/presenters/templates/@layout.latte
                    $this->redirect("this"); //přesměrování na aktuální stránku
                } else {
                    $form->addError("Nepodařilo se přidat záznam do databáze, zkuste to později.");
                }
            } else {
                if ($values->sequence <> 0 && $check->kurz === 0) {
                    $this->insertModel->updateSequence($values); //aktualizace kurzu
                }
                $this->insertModel->confirmRecord($values, $check);
                $this->flashMessage("Spoj by úspěšně aktualizován.", "success");
                $this->redirect("this");
            }
        }
    }
    
    public function createComponentUserAddedListGrid() {
        $control = $this->UserAddedListControlFactory->create();
        $control->setUserId($this->getUser()->getId());
        return $control;
    }
    
    

}
