<?php

namespace App\Presenters;

use Nette;
use App\Model;
use Nette\Utils\DateTime;
use Nette\Database\Table\Selection;

class HomepagePresenter extends BasePresenter {

    private $homepageModel;

    // $dbdate = $this->datetime->format("Y-m-d");


    public function __construct(\HomepageModel $homepage) {

        $this->homepageModel = $homepage;
    }

    public function renderDefault() {
        $this->template->records0 = $this->homepageModel->searchTram();
        $this->template->records1 = $this->homepageModel->searchTrolley();
        $this->template->records2 = $this->homepageModel->searchBus();
        





        //  $this->template->record = array($records0, $records1, $records2);

        $this->template->traction = array("Tramvaje", "Trolejbusy", "Autobusy");
        $this->template->dates = $this->homepageModel->createDate();
        $this->template->days = $this->homepageModel->createDay();
    }

}
