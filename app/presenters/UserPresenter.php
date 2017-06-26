<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use Nette\Security\User;
use Nette\Mail\Message;

class UserPresenter extends BasePresenter {

    private $userModel;

    public function __construct(\UserRepository $user) {
        $this->userModel = $user;
    }

    public function renderDefault() {
        if ($this->getUser()->isLoggedIn()) {
        $userID = $this->getUser()->getId();
        var_dump($userID);
        $this->redirect("User:profile", ["id" => $userID]);
        } else {
            $this->redirect("Homepage:");
        }
        
    }

    public function renderProfile($id) {
        $user = $this->userModel->getUsername($id);
        $this->template->profileName = $user->username;
        $this->template->email = $user->email;
        $this->template->userNumberOfRecords = $this->userModel->getNumberOfRecords($id);
        $this->template->lastLogin = date("d.n.Y H:i:s", strtotime($user->lastLogin));
        $this->template->registered = date("d.n.Y H:i:s", strtotime($user->registered));
        $this->template->role = $this->userModel->getRoleName($user->role)->name;
        $this->template->name = $user->name;
        $this->template->place = $user->place; 
        $this->template->pageID = $id;
        
        }
    
    public function renderSettings($id) {
        if ($this->getUser()->isLoggedIn() && $this->getUser()->getId() == $id) {
        $user = $this->userModel->getUsername($id);
        $this->template->profileName = $user->username;
        $this->template->email = $user->email;
        $this->template->userNumberOfRecords = $this->userModel->getNumberOfRecords($id);
        $this->template->lastLogin = date("d.n.Y H:i:s", strtotime($user->lastLogin));
        $this->template->registered = date("d.n.Y H:i:s", strtotime($user->registered));
        $this->template->role = $this->userModel->getRoleName($user->role)->name;
        $this->template->name = $user->name;
        $this->template->place = $user->place;
        $this->template->pageID = $id;
        } else {
            $this->redirect("Homepage:");
        }
    }
    
    public function renderRequestPasswordReset() {
        
    }
    
    public function renderResetEmailPassword($token, $email) {
        if (!$this->userModel->verifyPasswordToken($token, $email)) {
            $this->flashMessage('Nelze obnovit heslo z tohoto odkazu.', "danger");
            $this->redirect('Sign:in');
        }
        $this->template->email = $email;
    }
    
    public function renderResetPassword($id) {
        if ($this->getUser()->isLoggedIn() && $this->getUser()->getId() == $id) {
            $user = $this->userModel->getUsername($id);
            $this->template->email = $user->email;
        } else {
            $this->redirect("Homepage:");
        }
    }
    
    
    protected function createComponentEditProfileForm() {
       // $values = $this->userModel->getUserInfo($this->getUser->getId());
        $user = $this->userModel->getUsername($this->getUser()->getId());
        $form = new Form;
        $form->addText("jmeno", "Jméno")
                ->setDefaultValue($user->name);
        $form->addText("bydliste", "Bydliště")
                ->setDefaultValue($user->place);
        $form->addSubmit("zmenit", "Změnit");
        $form->onSuccess[] = [$this, "editProfileFormSucceeded"];
        return $form;
        
    }
    
    public function editProfileFormSucceeded($form) {
        $values = $form->getValues();
        
        if ($this->userModel->updateUserInfo($values, $this->getUser()->getId())) {
            $this->flashMessage("Údaje byly změněny", "success");
            $this->redirect("this");
        } else {
            /* @var $form type */
           // $form->addError("Nepodařilo se změnit informace, zkuste to později");
        }
        
    }
    
    protected function createComponentPasswordResetEmailForm() {
        $form = new Form;
        $form->addEmail("email", "Email", 35);
        $form->addPassword("heslo", "Heslo: *", 20)
                ->setOption("description", "Alespoň 6 znaků")
                ->addRule(Form::FILLED, "Vyplňte Vaše heslo")
                ->addRule(Form::MIN_LENGTH, "Heslo musí mít alespoň %d znaků.", 6);

        $form->addPassword("heslo2", "Heslo znova: *", 20)
                ->addRule(Form::FILLED, "Heslo znova")
                ->addRule(Form::EQUAL, "Hesla se neschodují.", $form["heslo"]);
                
        $form->addSubmit("odeslat", "Odeslat");
        $form->onSuccess[] = [$this, "passwordEmailFormSucceeded"];
        return $form;
    }
    
    public function passwordEmailFormSucceeded($form) {
        $values = $form->getValues();

        if ($this->userModel->resetEmailPassword($values)) {
            $this->flashMessage("Heslo bylo bylo úspěčně změněno.", "success");
            $this->redirect("Sign:in");
        } else {
            $this->flashMessage("Heslo se nepodařilo změnit", "danger");
            $this->redirect("this");
        }
    }
    
        protected function createComponentPasswordResetForm() {
        $form = new Form;
        $form->addEmail("email", "Email", 35);
        
        $form->addPassword('s_heslo', 'Heslo:')
                ->setRequired('Vložte staré heslo.');
        
        $form->addPassword("heslo", "Heslo: *", 20)
                ->setOption("description", "Alespoň 6 znaků")
                ->addRule(Form::FILLED, "Vyplňte Vaše heslo")
                ->addRule(Form::MIN_LENGTH, "Heslo musí mít alespoň %d znaků.", 6);

        $form->addPassword("heslo2", "Heslo znova: *", 20)
                ->addRule(Form::FILLED, "Heslo znova")
                ->addRule(Form::EQUAL, "Hesla se neschodují.", $form["heslo"]);
                
        $form->addSubmit("odeslat", "Odeslat");
        $form->onSuccess[] = [$this, "passwordResetFormSucceeded"];
        return $form;
    }
    
    public function PasswordResetFormSucceeded($form) {
        $values = $form->getValues();
        
        if (!$this->userModel->verifyPassword($values->s_heslo)) {
            $this->$form->addError("Špatně zadané staré heslo");
        }
        
        if (!$form->hasErrors()) {
            
        }
        
    }
    
    protected function createComponentPasswordResetRequestForm() {
        $form = new Form;
        $form->addEmail("email", "E-mail", 35)
                ->addRule(Form::FILLED, "Vyplňte Váš email")
                ->addCondition(Form::FILLED)
                ->addRule(Form::EMAIL, "Neplatná emailová adresa");
        $form->addSubmit("odeslat", "Resetovat heslo");
        $form->onSuccess[] = [$this, "passwordResetRequestFormSucceeded"];
        return $form;
    }
    
    public function passwordResetRequestFormSucceeded($form) {
        $values = $form->getValues();
        
        if(!$form->hasErrors()) {
            if (!$this->userModel->verifyEmail($values->email)) {
                $this->$form->addError("Tento email není přiřazen k účtu");
            }
        }
        
        if(!$form->hasErrors()) {
            if ($this->userModel->insertRequest($values->email)) {
                $this->$form->addError("Nepodařilo se obnovit heslo, zkuste to později");
            }  
        }
        
        if (!$form->hasErrors()) {
            if ($this->sendResetEmail($values->email)) {
                $this->flashMessage("Odkaz pro změnu hesla vám byl zaslán na vaši e-mailovou adresu.", "info");
             $this->flashMessage("Odkaz pro změnu hesla vám byl zaslán na vaši e-mailovou adresu.", "info");
                $form->getPresenter()->redirect("Sign:in");
            } else {
            $this->flashMessage("Heslo se nepodařilo obnovit, zkuste to později", "danger");
            $form->getPresenter()->redirect("Sign:in");
        }
        }
            
    }
  
    
    public function sendResetEmail($email) {
        $message = $this->generateResetEmail($email);
         try {
         $mailer = new Nette\Mail\SmtpMailer([
        'host' => 'smtp-120942.m42.wedos.net',
        'username' => 'info@sledovacka-plzen.cz',
        'password' => 'Zavridvere1!',
        'secure' => 'ssl',
]);  
            $mailer->send($message["message"]);
            return true;
        } catch (Nette\Mail\SmtpException $e) {
            return false;
        }
    }
    
    public function generateResetEmail($email) {
        $dbValues = $this->userModel->getPasswordResetData($email);
        
        $activateTime = date('d.m.Y G:i', strtotime($dbValues->time . "+1 days"));
        
        $message = new Message;
        $message->addTo($email)
                ->setFrom('Sledovačka MHD <info@sledovacka-plzen.cz>');
        
        $template = $this->createTemplate();
        $template->setFile(__DIR__ . '/../presenters/templates/resetPasswordEmail.latte');
        $template->values = $dbValues;
        $template->email = $email;
        $template->activateTime = $activateTime;
        
        $message->setHtmlBody($template);
        return array("message" => $message, "token" => $dbValues["token"]);
    }

}
