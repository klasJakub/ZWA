<?php

namespace App\Models;

use Nette\Utils\DateTime;

class Model
{

  const FORUM = 'forum';
  const POST = 'post';
  const CATEGORY = 'category';

  /** @var \Nette\Database\Context */
  private $database;

  public function __construct(\Nette\Database\Context $database)
  {
    $this->database = $database;
  }

    /**
     * @param $values
     * @return mixed
     * pridani kategorie do DB s hodnotami Values
     */
  public function insertCategory($values)
  {
    return $this->database->table(self::CATEGORY)
      ->insert($values)->getPrimary();
  }

    /**
     * @param $id
     * @param $values
     * upraveni kategorie s idecke $id, na hodnoty $values
     */
  public function updateCategory($id, $values)
  {
    $this->database->table(self::CATEGORY)
      ->where('category_id = ?', $id)
      ->update($values);
  }

    /**
     * @param $name
     * @return mixed
     * vrati kategorii s nazvem $name
     */

  public function getCategoryByName($name)
  {
    return $this->database->table(self::CATEGORY)
      ->where('name = ?', $name)
      ->fetch();
  }

    /**
     * @param $name
     * @return bool
     * vraci true pokud existuje kategorie s nazvem $name, jinak false
     */

  public function isCategoryExists($name)
  {
    return $this->getCategory($name) !== null;
  }


    public function deleteCategoryForums($id)
    {
        $forum=$this->database->table(self::FORUM)
            ->where('category_id = ?', $id);
        foreach ($forum as $item){
            $this->deleteForum($item->forum_id);
        }

    }

    /**
     * @param $id
     * metoda na mazani kategorie, nejprve smaze vsechny posty u vsech svych for, pote smaze for a nakonec kategorii
     */
  public function deleteCategory($id)
  {
      $this->deleteCategoryForums($id);
    $this->database->table(self::CATEGORY)
      ->where('category_id = ?', $id)
      ->delete();
  }

  public function getCategory($id)
  {
    if (!$id) {
      return null;
    }

    return $this->database->table(self::CATEGORY)
      ->where('category_id = ?', $id)->fetch();
  }

    /**
     * @return mixed
     * metoda vraci vsechnty kategorie z db
     */

  public function getCategories()
  {
    return $this->database->table(self::CATEGORY)->fetchAll();
  }

  public function insertForum($values)
  {
    return $this->database->table(self::FORUM)
      ->insert($values)->getPrimary();
  }

  public function updateForum($id, $values)
  {
    $this->database->table(self::FORUM)
      ->where('forum_id = ?', $id)
      ->update($values);
  }


    /**
     * @param $id
     * to same jako u kategorie, nejprve smaze vsechny posty a nakonec smaze forum
     */
  public function deleteForum($id)
  {
    $this->deleteForumPosts($id);
    $this->database->table(self::FORUM)
      ->where('forum_id = ?', $id)
      ->delete();
  }

  public function getForum($id)
  {
    if (!$id) {
      return null;
    }

    $data = $this->database->table(self::FORUM)
      ->where('forum_id = ?', $id);

    return $data->fetch();
  }


  public function getForums($category_id)
  {
    return $this->database->table(self::FORUM)
      ->where('category_id = ?', $category_id)
      ->order('date DESC')
      ->fetchAll();
  }

  public function getForumsCount($type)
  {
    if (!$type) {
      return null;
    }

    return $this->database->table(self::FORUM)
      ->where('type = ?', $type)
      ->select('count(*) count')
      ->fetch()['count'];
  }

  public function insertPost($values)
  {
    return $this->database->table(self::POST)
      ->insert($values)->getPrimary();
  }

  public function updatePost($id, $values)
  {
    $this->database->table(self::POST)
      ->where('post_id = ?', $id)
      ->update($values);
  }

  public function deletePost($id)
  {
    $this->database->table(self::POST)
      ->where('post_id = ?', $id)
      ->delete();
  }

  public function deleteForumPosts($id)
  {
    $this->database->table(self::POST)
      ->where('forum_id = ?', $id)
      ->delete();
  }

  public function getPost($id)
  {
    if (!$id) {
      return null;
    }

    $data = $this->database->table(self::POST)
      ->where('post_id = ?', $id);

    return $data->fetch();
  }

  /**
   * @param $type
   * @return |null
   */
  public function getPosts($id, $limit, $offset)
  {
    if (!$id) {
      return null;
    }
    return $this->database->table(self::POST)
      ->where('forum_id = ?', $id)
      ->limit($limit, $offset)
      ->order('date DESC')
      ->fetchAll();
  }

    /**
     * param uživatelské ID
     * return všechny posty uživatele
     */
  public function getUsersPosts($userId){
      if(!$userId){
          return null;
      }

      return $this->database->table(self::POST)
          ->where('user_id = ?', $userId)
          ->order('date DESC')
          ->fetchAll();
  }

  /**
   * Vrati pocet postu u jednoho fora.
   */
  public function getPostsCount($id)
  {
    if (!$id) {
      return null;
    }

    return $this->database->table(self::POST)
      ->where('forum_id = ?', $id)
      ->select('count(*) count')
      ->fetch()['count'];
  }
}
