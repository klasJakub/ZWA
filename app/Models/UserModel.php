<?php

namespace App\Models;

use mysql_xdevapi\BaseResult;
use Nette\Utils\DateTime;

class UserModel
{

  const USER = 'user';

  /** @var \Nette\Database\Context */
  private $database;

  public function __construct(\Nette\Database\Context $database)
  {
    $this->database = $database;
  }


  public function getUser($username)
  {
    if (!$username) {
      return null;
    }
    $data = $this->database->table(self::USER)
      ->where('username = ?', $username);
    return $data->fetch();
  }

  public function getUserById($id)
  {
    if (!$id) {
      return null;
    }
    $data = $this->database->table(self::USER)
      ->where('user_id = ?', $id);
    return $data->fetch();
  }

  public function isUserExists($username)
  {
    return $this->getUser($username) !== null;
  }

  public function insertUser($values)
  {
    $result = $this->database->table(self::USER)->insert($values);
    return $result->getPrimary();
  }

  public function updateUser($id, $values)
  {
    if (!$id) {
      return null;
    }
    return $this->database->table(self::USER)
      ->where('user_id = ?', $id)
        ->update($values);
  }


  public function getSalt()
  {
    return substr(MD5((new DateTime())->format('YmdHi')), 0, 25);
  }

}
