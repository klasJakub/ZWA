<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Components\AddForum\AddForum;
use App\Components\AddPost\AddPost;
use App\Components\Register\Register;
use App\Components\Sign\Sign;
use App\Models\Model;
use App\Models\UserModel;
use Nette;


final class ProfilePresenter extends Nette\Application\UI\Presenter
{

    /** @var \Nette\Database\Context */
    public $database;

    public $id;

    public function __construct(\Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    /**
     * @throws Nette\Application\AbortException
     * Zobrazi profil prihlaseneho uzivatele, pokud je uzivatel neprihlaseny, zobrazi homepage
     */
    public function actionDefault()
    {
        if (!$this->user->isLoggedIn()) {
            $this->redirect('Homepage:default');
        }

        $model = new Model($this->database);
        $posts = $model->getUsersPosts($this->user->id);
        $this->template->posts = $posts;
    }

    /**
     * @throws Nette\Application\AbortException
     * Zobrazi editovatelny registracni formular, ve kterem si uzivatel muze zmenit jmeno nebo heslo
     * pripadne zobrazit svoje komentare
     */
    public function actionEdit()
    {
        if (!$this->user->isLoggedIn()) {
            $this->redirect('Homepage:default');
        }
        $model = new UserModel($this->database);
        $user = $model->getUserbyId($this->user->id);
        $this->template->data = $user;
        $this->id=$this->user->id;
        $this->addComponent(new Register(), 'editUser');
    }




}
