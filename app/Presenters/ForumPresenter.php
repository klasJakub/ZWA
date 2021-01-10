<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Components\AddForum\AddForum;
use App\Components\AddPost\AddPost;
use App\Components\Sign\Sign;
use App\Models\Model;
use Nette;


final class ForumPresenter extends Nette\Application\UI\Presenter
{

  /** @var \Nette\Database\Context */
  public $database;

  public $id;

  public function __construct(\Nette\Database\Context $database)
  {
    $this->database = $database;
  }

    /**
     * zobrazi stranku vsech for pod jejich kategoriemi, pokud kategorie nema  zadna fora nezobrazi se
     */
  public function actionDefault()
  {
    $model = new Model($this->database);
    $categories = $model->getCategories();
    $data = [];
    foreach ($categories as $key => $item) {
      $forums = $model->getForums($key);
      $data += [
        $key => [
          'name' => $item->name,
          'data' => $forums
        ]
      ];
    }

    $this->template->data = $data;
  }

    /**
     * @param $id
     * @param int $page
     * @throws Nette\Application\AbortException
     * zobrazi forum a vsechny jeho komentare
     */
  public function actionForum($id, $page = 1)
  {
    if (!$id) {
      $this->redirect('Forum:default');
    }
    $this->id = $id;
    $model = new Model($this->database);
    $forum = $model->getForum($id);
    $this->template->title = $forum->name;
    $this->template->id = $id;

    $limit = 5;
    $offset = $limit * $page - $limit;

    $posts = $model->getPosts($id, $limit, $offset);

    $this->template->posts = $posts;
    $count = $model->getPostsCount($id);
    $pages = $count / $limit;
    if (intval($pages) < $pages) {
      $pages += 1;
    }
    $this->template->pages = intval($pages);
    $this->template->limit = $limit;
    $this->template->page = $page;
  }

    /**
     * @param $id
     * @throws Nette\Application\AbortException
     * zobrazi formular na pridani komentare
     */
  public function actionAddPost($id)
  {
    if (!$this->user->isLoggedIn()) {
      $this->redirect('Forum:default');
    }
    $this->id = $id;

    $this->addComponent(new AddPost(), 'addPost');
  }

    /**
     * @param $id
     * @throws Nette\Application\AbortException
     * Smaze forum a vsechny jeho posty, zobrazi defualtni stranku for
     */
public function actionDelete($id){
    if (!$this->user->isLoggedIn()) {
      $this->redirect('Forum:default');
    }
    $model = new Model($this->database);

    if ($id) {
      $forum = $model->getForum($id);
      if (!$forum) {
        $this->redirect('Forum:default');
      }
      if (!$this->user->getIdentity()->isAdministrator() && $forum->user_id !== $this->user->getId()) {
        $this->redirect('Forum:default');
      }
    }

    $model->deleteForum($id);
    $this->redirect('Forum:default');
  }

    /**
     * @param $id
     * @throws Nette\Application\AbortException
     * Upravi komentar a otevre stranku fora kde jsme komentar upravili
     */
  public function actionEditPost($id)
  {
    if (!$this->user->isLoggedIn()) {
      $this->redirect('Forum:default');
    }

    if ($id) {
      $model = new Model($this->database);
      $post = $model->getPost($id);
      if (!$post) {
        $this->redirect('Forum:default');
      }
      if (!$this->user->getIdentity()->isAdministrator() && $post->user_id !== $this->user->getId()) {
        $this->redirect('Forum:default');
      }
    }
    $this->id = $id;

    $addPost = new AddPost();
    $addPost->setEdit();
    $this->addComponent($addPost, 'addPost');

  }

    /**
     * @param $id
     * @throws Nette\Application\AbortException
     * Smaze komentar a otevre stranku fora kde jsme komentar smazali
     */
  public function actionDeletePost($id)
  {
    if (!$this->user->isLoggedIn()) {
      $this->redirect('Forum:default');
    }
    $model = new Model($this->database);

    if ($id) {
      $post = $model->getPost($id);
      if (!$post) {
        $this->redirect('Forum:default');
      }
      if (!$this->user->getIdentity()->isAdministrator() && $post->user_id !== $this->user->getId()) {
        $this->redirect('Forum:default');
      }
    }

    $post = $model->getPost($id);
    $postId = $post->post_id;
    $ForumId=$post->forum_id;

    $model->deletePost($id);
    $this->redirect('Forum:forum', ['id' => $ForumId]);
  }

  public function actionAdd($id)
  {
    if (!$this->user->isLoggedIn()) {
      $this->redirect('Forum:default');
    }

    if ($id) {
      $model = new Model($this->database);
      $forum = $model->getForum($id);
      if (!$forum) {
        $this->redirect('Forum:default');
      }
      if (!$this->user->getIdentity()->isAdministrator() && $forum->user_id !== $this->user->getId()) {
        $this->redirect('Forum:default');
      }
    }

    $this->id = $id;
    $this->template->title = $id ? 'Upravit forum' : 'Přidat forum';
    $this->addComponent(new AddForum(), 'addForum');
  }


}
