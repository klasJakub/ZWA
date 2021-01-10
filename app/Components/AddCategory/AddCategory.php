<?php

namespace App\Components\AddCategory;

use App\Models\Model;
use App\Models\UserModel;
use \Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Utils\DateTime;


/**
 * Class AddCategory
 * @package App\Components\AddCategory
 * Vytvoreni formulare pro zadani nazvu kategorie a odeslani
 * odeslani ho prida do DB
 */
class AddCategory extends Control
{
//funkce na vytvoreni dalsi kategorie, kategorii muze pridat jenom
    function createComponentAddCategory()
    {
        $form = new Form();

        $model = new Model($this->getPresenter()->database);

        $category = $model->getCategory($this->getPresenter()->id);

        $label = 'Název kategorie';
        $form->addText('name', $label . '*')
            ->setHtmlAttribute('placeholder', $label)
            ->setHtmlAttribute('class', 'form-control')
            ->addRule(Form::FILLED);

        if ($category) {
            $form['name']->setDefaultValue($category->name);
        }

        // send
        $form->addSubmit('send', 'Odeslat')
            ->setHtmlAttribute('class', 'btn');

        $form->onSuccess[] = function (Form $form) {
            $values = $form->getValues();
            $id = $this->getPresenter()->id;


            $items = [
                'name' => $values->name
            ];

            $model = new Model($this->getPresenter()->database);

            if ($id) {
                $model->updateCategory($id, $items);
            } else {
                $lastId = $model->insertCategory($items);
            }

            $this->getPresenter()->redirect('Category:default');
        };
        return $form;
    }


    public function render()
    {
        $this->template->setFile(dirname(__FILE__) . '/addCategory.latte');
        $this->template->form = $this->createComponentAddCategory();
        $this->template->render();
    }

}
