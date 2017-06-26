<?php

namespace App\Presenters;

use Nette;
use Ublaboo\DataGrid\DataGrid;

class AdminPresenter extends BasePresenter {

    /**
     * @var \App\Components\LineListControlFactory
     * @inject
     */
    public $LineListControlFactory;

    /**
     * @var \App\Components\RecordListControlFactory
     * @inject
     */
    public $RecordListControlFactory;
    private $adminModel;

    public function __construct(\AdminRepository $admin) {
        $this->adminModel = $admin;
    }

    public function renderDefault() {

        $this->template->usersNumber = $this->adminModel->getNumberOfUsers();
        $this->template->activeUsersNumber = $this->adminModel->getNumberOfActiveUsers();
        $this->template->newUsersNumber = $this->adminModel->getNumberOfNewUsers();

        $this->template->recordsAllDatabase = $this->adminModel->getNumberOfRecords(time(), "second");
        $this->template->recordsYearDatabase = $this->adminModel->getNumberOfRecords(1, "year");
        $this->template->recordsMonthDatabase = $this->adminModel->getNumberOfRecords(1, "month");
        $this->template->recordsWeekDatabase = $this->adminModel->getNumberOfRecords(7, "day");
        $this->template->recordsTodayDatabase = $this->adminModel->getNumberOfRecords(0, "day");

    }

    public function renderUsers() {

    }

    public function renderBanlist() {

    }

    public function renderRecordsList() {

    }

    public function renderLinesList() {

    }

    public function createComponentLinesListGrid() {
        return $this->LineListControlFactory->create();
    }

    public function createComponentRecordsListGrid() {
        return $this->RecordListControlFactory->create();
    }

}
