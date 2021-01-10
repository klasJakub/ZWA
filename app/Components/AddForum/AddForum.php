<?php

namespace App\Components\AddForum;

use App\Models\Model;
use Cassandra\Date;
use \Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Utils\DateTime;


/**
 * Class AddForum
 * @package App\Components\AddForum
 * Zde je formular na pridavani Fora, jedina validace je ze si user musi zvolit kategorii
 */
class AddForum extends Control
{

    function createComponentAddForum()
    {
        $form = new Form();

        $model = new Model($this->getPresenter()->database);

        $forum = $model->getForum($this->getPresenter()->id);

        $label = 'Jméno';
        $form->addText('name', $label . '*')
            ->setHtmlAttribute('placeholder', $label)
            ->setHtmlAttribute('class', 'form-control')
            ->addRule(Form::FILLED);

        $categories = $model->getCategories();

        $items = [
            null => 'Vyber kategorii'
        ];

        foreach ($categories as $key => $item) {
            $items += [
                $key => $item->name
            ];
        }

        $label1 = 'Kategorie';
        $form->addSelect('category', $label1, $items);

        if ($forum) {
            $form['name']->setDefaultValue($forum->name);
            $form['category']->setDefaultValue($forum->category_id);
        }

        // send
        $form->addSubmit('send', 'Odeslat')
            ->setHtmlAttribute('class', 'btn');

        $form->onSuccess[] = function (Form $form) {
            $values = $form->getValues();

            $id = $this->getPresenter()->id;
            $model = new Model($this->getPresenter()->database);


            if ($values->category == null) {

                $this->template->validMessage = "Vyberte kategorii";
                return $form;

            }


            $items = [
                'name' => $values->name,
                'category_id' => $values->category
            ];

            if ($id) {
                $model->updateForum($id, $items);
            } else {
                $items += [
                    'user_id' => $this->getPresenter()->user->getId(),
                    'date' => new DateTime()
                ];
                $lastId = $model->insertForum($items);
            }

            $this->getPresenter()->redirect('Forum:default');
        };
        return $form;
    }


    public function render()
    {
        $this->template->setFile(dirname(__FILE__) . '/addForum.latte');
        $this->template->form = $this->createComponentAddForum();
        $this->template->render();
    }

}
