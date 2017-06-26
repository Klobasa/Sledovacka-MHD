<?php

namespace App\Presenters;

use Nette;
use App\Model;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter {

    /** @var \BaseRepository @inject */
    public $baseModel;

    public function beforeRender() {
        // odstraňuje z databáze neaktivované uživatele
        $this->baseModel->removeNonActivated();
        //odstraňuje nezměněná hesla
        $this->baseModel->removeNonResetedPassword();
        
        // získávní jména uživatele
        if ($this->getUser()->isLoggedIn() == true) {
            $this->template->username = $this->baseModel->getUsername($this->getUser()->getId())->username;
            $this->template->userID = $this->getUser()->getId();
        } else {
            $this->template->username = null;
        }
    }

}
