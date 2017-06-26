<?php

namespace App\Presenters;

use App\Presenters\BasePresenter;
use Nette\Application\UI\Form;
use Nette\Mail\Message;
use Tomaj\Form\Renderer\BootstrapVerticalRenderer;
use Nette\Mail\SendmailMailer;

class ContactPresenter extends BasePresenter {

    private $contactModel;

    public function __construct(\ContactRepository $contact) {
        $this->contactModel = $contact;
    }

    public function renderDefault() {
        
    }

    /**
     * Kontaktní formulář
     * @return Form
     */
    protected function createComponentContactForm() {
        $form = new Form;
        $form->setRenderer(new BootstrapVerticalRenderer);
        $form->addText('name', 'Vaše jméno')
                ->addRule(Form::FILLED, 'Vyplňte vaše jméno');

        $form->addText('email', 'Váš e-mail')
                ->setEmptyValue('@')
                ->addRule(Form::FILLED, 'Vyplňte váš e-mail')
                ->addRule(Form::EMAIL, 'E-mail má nesprávný tvar');

        $form->addTextarea('text', 'Zpráva')
                ->addRule(Form::FILLED, 'Vyplňte zprávu');
        
        $form->addReCaptcha("captcha", "Prokažte, že nejste robot:")
                ->addRule(Form::FILLED, "Prokažte, že nejste robot.");

        $form->addSubmit('okSubmit', 'Odeslat');

        $form->onSubmit[] = array($this, 'contactFormSubmitted');
        return $form;
    }
    
    public function createComponentBugReportForm() {
        $form = new Form;
        $form->setRenderer(new BootstrapVerticalRenderer);
        $form->addText('name', 'Vaše jméno')
                ->addRule(Form::FILLED, 'Vyplňte vaše jméno');

        $form->addText('email', 'Váš e-mail')
                ->setEmptyValue('@')
                ->addRule(Form::FILLED, 'Vyplňte váš e-mail')
                ->addRule(Form::EMAIL, 'E-mail má nesprávný tvar');

        $form->addTextarea('text', 'Popis chyby')
                ->addRule(Form::FILLED, 'Vyplňte chybovou zprávu')
                ->setOption("description","Uveďte co nejpodrobnější popis chyby.");
        
        $form->addUpload("file", "Obrázek")
                ->setRequired(FALSE)
                ->addRule(Form::IMAGE, 'Obrázek musí být ve formátu JPEG, PNG nebo GIF.')
                ->addRule(Form::MAX_FILE_SIZE, 'Maximální velikost souboru je 1 MB.', 1000 * 1024 /* v bytech */)
                ->setOption("description","Povolené formáty: JPEG, PNG nebo GIF");
        
        $form->addReCaptcha("captcha", "Prokažte, že nejste robot:")
                ->addRule(Form::FILLED, "Prokažte, že nejste robot.");

        $form->addSubmit('okSubmit', 'Odeslat');
        
        return $form;
    }

    /**
     * Kontrola, jestli se form odeslal
     * @param type $form
     */
    public function contactFormSubmitted($form) {

        if ($this->sendMail($form->getValues())) {
            $this->flashMessage('Zpráva úspěšně odeslána!');
            $this->redirect('this');
        } else {
            $form->flashMessage('Nepodařilo se odeslat e-mail, zkuste to prosím později.');
        }
    }

    /**
     * Odesílá registrační email
     * @param type $values
     * @return boolean
     */
    private function sendMail($values) {
        $mail = new Message;
        $mail->setFrom($values['email']);
        $mail->addTo('info@sledovacka-plzen.cz');

        $template = $this->createTemplate();
        $template->setFile(__DIR__ . '/../presenters/templates/ContactFormMail.latte');

        $template->name = $values['name'];
        $template->email = $values['email'];
        $template->text = $values['text'];

        $mail->setHtmlBody($template);

        try {
            $mailer = new SendmailMailer;
            $mailer->send($mail);
            return true;
        } catch (Nette\InvalidStateException $e) {
            return false;
        }
    }

}
