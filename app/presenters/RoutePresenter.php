<?php

namespace App\Presenters;

use Nette;
use App\Model;
use Nette\Utils\DateTime;
use Nette\Database\Table\Selection;

class RoutePresenter extends BasePresenter {

    private $database;

    // $dbdate = $this->datetime->format("Y-m-d");


    public function __construct(Nette\Database\Context $database) 
    {
        $this->database = $database;
    }

    public function renderDefault() 
    {
        $date = new DateTime();
       $czDate = $date->format("d. F");
       $this->template->datum = $czDate;
    }

}
