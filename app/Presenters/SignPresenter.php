<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Components\Register\Register;
use App\Components\Sign\Sign;
use Nette;


final class SignPresenter extends Nette\Application\UI\Presenter
{

  /** @var \Nette\Database\Context */
  public $database;
  public $id=null;

  public function __construct(\Nette\Database\Context $database)
  {
    $this->database = $database;
  }

  public function actionDefault()
  {
    $sign = new Sign();
    $this->addComponent($sign, 'sign');
  }

  public function actionRegister()
  {
    $register = new Register();
    $this->addComponent($register, 'register');
  }

  public function actionOut()
  {
    $this->getUser()->logout(true);
    $this->redirect('Sign:default');
  }

}
