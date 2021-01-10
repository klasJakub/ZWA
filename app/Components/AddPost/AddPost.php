<?php

namespace App\Components\AddPost;

use App\Models\Model;
use \Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Utils\DateTime;

/**
 * Class AddPost
 * @package App\Components\AddPost
 * Podstranka na pridavani fora, formular s textArea
 */

class AddPost extends Control
{

  private $edit;

  public function setEdit()
  {
    $this->edit = true;
  }

  function createComponentAddPost()
  {
    $form = new Form();

    $model = new Model($this->getPresenter()->database);

    $post = $model->getPost($this->getPresenter()->id);

    $label = 'Text';
    $form->addTextArea('text', $label . '*')
      ->setHtmlAttribute('placeholder', $label)
      ->setHtmlAttribute('class', 'form-control')
      ->addRule(Form::FILLED);

    if ($post && $this->edit) {
      $form['text']->setDefaultValue($post->text);
    }

    // send
    $form->addSubmit('send', 'Odeslat')
      ->setHtmlAttribute('class', 'btn');

    $form->onSuccess[] = function (Form $form) {
      $values = $form->getValues();

      $id = $this->getPresenter()->id;

      $items = [
        'text' => $values->text
      ];

      $model = new Model($this->getPresenter()->database);

      if ($id && $this->edit) {
        $model->updatePost($id, $items);
        $id=$model->getPost($id)->forum_id;
      } else {
        $items += [
          'forum_id' => $id,
          'user_id' => $this->getPresenter()->user->id,
          'date' => new DateTime()
        ];
        $lastId = $model->insertPost($items);
      }


      $this->getPresenter()->redirect('Forum:forum', ['id' => $id]);
    };
    return $form;
  }


  public function render()
  {
    $this->template->setFile(dirname(__FILE__) . '/addPost.latte');
    $this->template->form = $this->createComponentAddPost();
    $this->template->render();
  }

}
