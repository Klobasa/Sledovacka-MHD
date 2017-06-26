<?php

namespace App\Presenters;

use Nette;
use App\Forms\SignFormFactory;

class SignPresenter extends BasePresenter {

    /** @var SignFormFactory @inject */
    public $factory; 
    private $userModel;

    public function __construct(\UserRepository $user) {
        $this->userModel = $user;
    }

    /**
     * Sign-in form factory.
     * @return Nette\Application\UI\Form
     */
    protected function createComponentSignInForm() {
        
        
        
        $form = $this->factory->create();

        $form->onSuccess[] = function ($form) {
            $this->userModel->setLastLogin($this->getUser()->getId());
            $this->flashMessage('Přihlášení proběhlo v pořádku');
            $form->getPresenter()->redirect('Homepage:');
        };
        return $form;
    }

    public function actionOut() {
        $this->getUser()->logout();
        $this->flashMessage('Odhlášeno');
        $this->redirect('in');
    }

}
