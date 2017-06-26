<?php

namespace App\Forms;

use Nette;
use Nette\Application\UI\Form;
use Nette\Security\User;
use Tomaj\Form\Renderer\BootstrapVerticalRenderer;

class SignFormFactory extends Nette\Object {

    /** @var User */
    private $user;

    public function __construct(User $user) {
        $this->user = $user;
    }

    /**
     * @return Form
     */
    public function create() {
        $form = new Form;
        $form->setRenderer(new BootstrapVerticalRenderer);
        $form->addText('name', 'Uživatel:')
                ->setRequired('Vložte uživatelské jméno.');

        $form->addPassword('password', 'Heslo:')
                ->setRequired('Vložte heslo.');

        $form->addCheckbox('remember', 'Zůstat přihlášen');

        $form->addSubmit('send', 'Přihlásit se');

        $form->onSuccess[] = array($this, 'formSucceeded');
        return $form;
    }

    public function formSucceeded(Form $form, $values) {
        if ($values->remember) {
            $this->user->setExpiration('14 days', FALSE);
        } else {
            $this->user->setExpiration('20 minutes', TRUE);
        }

        try {
            $this->user->login($values->name, $values->password);
        } catch (Nette\Security\AuthenticationException $e) {
            $form->addError($e->getMessage());
        }
    }

}
