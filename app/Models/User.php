<?php

namespace App\Models;

class User extends \Nette\Security\Identity
{
  public $administrator;

  public function setAdministrator($administrator)
  {
    $this->administrator = $administrator;
  }

  public function isAdministrator()
  {
    return $this->administrator;
  }
}
