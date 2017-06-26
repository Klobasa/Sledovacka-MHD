<?php

namespace App\Forms;

use Nette;
use Nette\Application\UI\Form;
use Nette\Security\User;
use Tomaj\Form\Renderer\BootstrapVerticalRenderer;
use Minetro\Forms\reCAPTCHA\ReCaptchaField;
use Minetro\Forms\reCAPTCHA\IReCaptchaValidatorFactory;

class RegisterFormFactory extends Nette\Object {

    /** @var IReCaptchaValidatorFactory @inject */
    public $reCaptchaValidatorFactory;

    /** @var IMyAutoFormFactory @inject */
    public $myAutoFormFactory;

    /** @var User */
    private $username;
    private $database;
    private $token;
    private $email;

    public function __construct(Nette\Database\Context $database) {
        $this->database = $database;
    }

    /**
     * @return Form
     */
    public function create() {

        $form = new Form;
        $form->setRenderer(new BootstrapVerticalRenderer);
        $form->addText("username", "Přezdívka:")
                ->setRequired("Přezdívka není vyplněna")
                ->addRule(Form::MIN_LENGTH, "Uživatelské jméno musí mít alespoň %d znaky.", 4)
                ->addRule(Form::MAX_LENGTH, "Uživatelské jméno může mít maximálně %d znaků.", 60);

        $form->addText("email", "E-mail: *", 35)
                ->setEmptyValue("@")
                ->addRule(Form::FILLED, "Vyplňte Váš email")
                ->addCondition(Form::FILLED)
                ->addRule(Form::EMAIL, "Neplatná emailová adresa");

        $form->addPassword("password", "Heslo: *", 20)
                ->setOption("description", "Alespoň 6 znaků")
                ->addRule(Form::FILLED, "Vyplňte Vaše heslo")
                ->addRule(Form::MIN_LENGTH, "Heslo musí mít alespoň %d znaků.", 6);

        $form->addPassword("password2", "Heslo znova: *", 20)
                // ->addCondition($form["password"], Form::VALID, TRUE)
                ->addRule(Form::FILLED, "Heslo znova")
                ->addRule(Form::EQUAL, "Hesla se neschodují.", $form["password"]);

        $form->addReCaptcha("captcha", "Prokažte, že nejste robot:")
                ->addRule(Form::FILLED, "Prokažte, že nejste robot.");

        $form->addSubmit("sent", "Registrovat");

        $form->onValidate[] = array($this, "registerFormValidated");

        return $form;
    }

    public function registerFormValidated($form) {
        $values = $form->getValues();

        if ($this->isPlayerRegistered($values->username, $values->email)) {
            $form->addError("Uživatel se stejným jménem nebo e-mailem je již registrován.");
        }

        if (!$form->hasErrors()) {
            if ($this->insertDataInDatabase($values)) {
                $this->$form->addError("Registrace se nezdařila, zkuste to později.");
            }
        }

        // if (!$form->hasErrors()) {
        //     if ($this->sendEmail($form)) {
        //         $form->addError("Nepodařilo se odeslat aktivační email. Zkuste se zaregistrovat později");
        //     }
        // }
    }

    //kontroluje, jestli již není stejný uživatel v databázi
    public function isPlayerRegistered($username, $email) {
        $registered = $this->database->table('users')->where('username = ? OR email = ?', $username, $email)->fetch();
        $registered = $this->database->table('temporary_users')->where('username = ? OR email = ?', $username, $email)->fetch();

        return ($registered) ? true : false;
    }

    //vloží uživatele do databáze
    public function insertDataInDatabase($values) {
        try {
            $this->database->table('temporary_users')->insert(array(
                "email" => $this->email = $values->email,
                "password" => Nette\Security\Passwords::hash($values->password),
                "username" => $this->username = $values->username,
                "token" => $this->token = $this->generateToken(),
            ));
            return false;
        } catch (Nette\InvalidArgumentException $e) {
            return true;
        }
    }

    //vygeneruje náhodný token pro link emailu
    public function generateToken($length = 15) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

}
