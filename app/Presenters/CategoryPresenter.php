<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Components\AddCategory\AddCategory;
use App\Components\AddForum\AddForum;
use App\Models\Model;
use Nette;

final class CategoryPresenter extends Nette\Application\UI\Presenter
{

  /** @var \Nette\Database\Context */
  public $database;

  public function __construct(\Nette\Database\Context $database)
  {
    $this->database = $database;
  }

  public $id;

    /**
     * Zobrazi vsechny kategorie ktere jsou vytvoreny, lze zobrazit pouze adminovi
     */
  public function actionDefault()
  {
    $model = new Model($this->database);
    $categories = $model->getCategories();
    $this->template->categories = $categories;
  }

    /**
     * @param $id
     * zobrazi podstranku pro pridani kategorie,
     * lze zobrazit pouze adminovi
     */
  public function actionAdd($id)
  {
    $this->id = $id;


    $this->template->title = $id ? "Upravit kategorii" : "Přidat kategorii";
    $this->addComponent(new AddCategory(), 'addCategory');
  }

  public function actionDelete($id)
  {
    $model = new Model($this->database);
    $model->deleteCategory($id);
    $this->redirect('Category:default');
  }

  public function startup()
  {
    parent::startup();
    if ($this->user->isLoggedIn() && $this->user->getIdentity()->isAdministrator()) {
    } else {
      $this->redirect('Homepage:default');
    }
  }
}
