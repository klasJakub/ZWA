<?php

namespace App\Models;

use App\Models\User;
use App\Models\UserModel;
use Nette;

/**
 * Class Authenticator
 * @package App\Models
 * zde probiha autentikace registrovaneho uzivatele pri prihlaseni,
 * pokud nesedi jmeno nebo heslo z db nelze se prihlasit
 */
class Authenticator implements \Nette\Security\IAuthenticator
{
  private $database;

  private $passwords;

  public function __construct(\Nette\Database\Context $database, Nette\Security\Passwords $passwords)
  {
    $this->database = $database;
    $this->passwords = $passwords;
  }

  public function authenticate(array $credentials): Nette\Security\IIdentity
  {
    [$username, $password] = $credentials;

    $userModel = new UserModel($this->database);

    $user = $userModel->getUser($username);
    if (!$this->passwords->verify($password . $user->salt, $user->password)) {
      throw new Nette\Security\AuthenticationException('Nesprávné údaje');
    }

    $userEntity = new User($user->user_id, $user->role, ['username' => $user->username]);
    $userEntity->setRoles([]);
    $userEntity->setAdministrator($user->role->type === 'administrator');
    return $userEntity;
  }
}
