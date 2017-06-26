<?php

namespace App\Presenters;

use Nette;
use App\Forms\RegisterFormFactory;
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;


class RegisterPresenter extends BasePresenter {

    /** @var Nette\Mail\IMailer */
    public $mailer;

    /** @var RegisterFormFactory @inject */
    public $factory;
    private $registerModel;

    public function __construct(\RegisterRepository $register) {
        $this->registerModel = $register;
    }

    public function renderDefault() {

    }

    /**
     * Register form factory.
     * @return Nette\Application\UI\Form
     */
    protected function createComponentRegisterForm() {
        $form = $this->factory->create();



        $form->onSuccess[] = function ($form) {

            if ($this->sendEmail($form)) {
                $this->flashMessage("<strong>Výborně!</strong> Vaše registrace je téměř hotová, na email obdržíte aktivační odkaz pro ověření vaší adresy.", "info");
                $form->getPresenter()->redirect('Homepage:');
            } else {
                $this->flashMessage("<strong>Pozor!</strong> Registraci se nepodařilo dokončit, zkuste se zaregistrovat později.", "danger");
                $form->getPresenter()->redirect('Register:');
            }
        };

        //$form->onError[] = $this->flashMessage('Hesla se neshodují nebo uživatel již existuje.');
        return $form;
    }
    

    public function generateEmail($form) {
        $formValues = $form->getValues();
        $values = $this->registerModel->generateEmail($formValues["email"]); //in = email from form; back = data form db - username, email, token, time

       
        $activateTime = date('d.m.Y G:i', strtotime($values->time . "+1 days"));

        $message = new Message;
        $message->addTo($formValues['email'])
                ->setFrom("registrace@sledovacka-plzen.cz");

        $template = $this->createTemplate();
        $template->setFile(__DIR__ . '/../presenters/templates/email.latte');
        $template->values = $values;
        $template->activateTime = $activateTime;

        $message->setHtmlBody($template);
        return array('message' => $message, 'token' => $values['token']);
    }

    /**
     * Posílá registrační email
     * @param type $form
     * @return boolean
     */
    public function sendEmail($form) {

        $message = $this->generateEmail($form);
        try {
            $mailer = new Nette\Mail\SmtpMailer([
        'host' => 'smtp-120942.m42.wedos.net',
        'username' => 'registrace@sledovacka-plzen.cz',
        'password' => 'Autobus4210!',
        'secure' => 'ssl',
]);
            $mailer->send($message['message']);
            return true;
        } catch (Nette\Mail\SmtpException $e) {
            $form->addError("Registrace se nezdařila, zkuste to později.");
            $this->registerModel->database->table("temporary_users")
                    ->where("token = ?", $message['token'])
                    ->delete();
            return false;
        }
    }

    public function actionVerify($token) {
        if ($this->registerModel->activateUser($token)) {
            $this->flashMessage('Aktivace proběhla v pořádku, nyní se můžete přihlásit.', "success");
            $this->redirect('Sign:in');
        } else {
            $this->flashMessage('<strong>Aktivace se nezdařila!</strong> S největší pravděpodobností vypršela platnost odkazu nebo byl již uživatel aktivován, prosím, zaregistrujte se znova.', "danger");
            $this->redirect('Register:');
        }
    }

}
