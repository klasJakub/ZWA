<?php

namespace App\Components\Sign;

use App\Models\Model;
use App\Models\UserModel;
use \Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Utils\DateTime;

/**
 * Class Sign
 * @package App\Components\Sign
 * Zde je prihlasovaci formular, je zde validace jestli user sedi s jinym userem v db
 *
 */
class Sign extends Control
{

  function createComponentSign()
  {
    $form = new Form();

    $label = 'Jméno';
    $form->addText('name', $label . '*')
      ->setHtmlAttribute('placeholder', $label)
      ->addRule(Form::FILLED);

    $label2 = 'Heslo';
    $form->addPassword('password', $label2 . '*')
      ->setHtmlAttribute('placeholder', $label2)
      ->addRule(Form::FILLED);


    // send
    $form->addSubmit('submit', 'Odeslat')
      ->setHtmlAttribute('class', 'btn');

    $form->onSuccess[] = function (Form $form) {
      $values = $form->getValues();

      $userModel = new UserModel($this->getPresenter()->database);
      if (!$userModel->isUserExists($values->name)) {
        $this->template->validMessage = 'Uživatel neexistuje';
      } else {
        $user = $this->getPresenter()->user;
        try {
          $user->login($values->name, $values->password);
          $this->getPresenter()->redirect('Homepage:default');
        } catch (\Nette\Security\AuthenticationException $e) {
          $this->template->validMessage = $e->getMessage();
        }
      }

    };
    return $form;
  }


  public function render()
  {
    $this->template->setFile(dirname(__FILE__) . '/sign.latte');
    $this->template->form = $this->createComponentSign();
    $this->template->render();
  }

}
