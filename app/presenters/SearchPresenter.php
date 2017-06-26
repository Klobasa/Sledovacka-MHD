<?php
namespace App\Presenters;

class SearchPresenter extends BasePresenter {
    
     /**
     * @var \App\Components\SearchListControlFactory
     * @inject
     */
    public $SearchListControlFactory;
    
     public function createComponentSearchListGrid() {
        return $this->SearchListControlFactory->create();
    }
}
