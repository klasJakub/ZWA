<?php

namespace App\Components\Register;

use App\Models\Model;
use App\Models\User;
use App\models\UserModel;
use \Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Utils\DateTime;

/**
 * Class Register
 * @package App\Components\Register
 * Registrace uzivatele, pokud se jedna o noveho uzivatele formular mu priradi roli 2, navic pokud o stareho, muze
 * zde menit sve udaje. V modelu se pak soli hesla pomoci metody GetSalt. Do databaze se uklada i sul i hash hesla.
 */
class Register extends Control
{

    function createComponentRegister()
    {
        $form = new Form();

        $model = new UserModel($this->getPresenter()->database);

        $user = $model->getUserById($this->getPresenter()->id);

        $label = 'Jméno';
        $form->addText('name', $label . '*')
            ->setHtmlAttribute('placeholder', $label)
            ->setHtmlAttribute('class', 'name')
            ->addRule(Form::FILLED);

        $label2 = 'Heslo';
        $form->addPassword('password', $label2 . '*')
            ->setHtmlAttribute('placeholder', $label2)
            ->setHtmlAttribute('class', 'password')
            ->addRule(Form::FILLED);

        $label3 = 'Heslo znovu';
        $form->addPassword('repassword', $label3 . '*')
            ->setHtmlAttribute('placeholder', $label3)
            ->setHtmlAttribute('class', 'repassword')
            ->addRule(Form::FILLED)
            ->addRule(Form::EQUAL, 'password error', $form['password']);

        if ($user) {
            $form['name']->setDefaultValue($user->username);
        }

        // send
        $form->addSubmit('submit', 'Odeslat')
            ->setHtmlAttribute('class', 'btn submit');


        $form->onSuccess[] = function (Form $form) {
            $values = $form->getValues();
            $id = $this->getPresenter()->id;


            $userModel = new UserModel($this->getPresenter()->database);
            $user = $userModel->getUserById($id);

            if ($id && $user->username !== $values->name) {
                if ($userModel->isUserExists($values->name)) {
                    $this->template->validMessage = "Uživatel existuje, zvolte jiné jméno";
                    return $form;
                }
            }
            if ($userModel->isUserExists($values->name) && !$id) {
                $this->template->validMessage = "Uživatel existuje, zvolte jiné jméno";
            } else {
                $salt = $userModel->getSalt();
                $password = password_hash($values->password . $salt, PASSWORD_BCRYPT);
                if ($id) {
                    $items = [
                        'username' => $values->name,
                        'password' => $password,
                        'salt' => $salt
                    ];
                    $this->getPresenter()->user->getIdentity()->username = $values->name;
                    $userModel->updateUser($id, $items);
                    $this->getPresenter()->redirect('Profile:default');
                } else {
                    $items = [
                        'username' => $values->name,
                        'password' => $password,
                        'salt' => $salt,
                        'role_id' => 2
                    ];

                    $userModel->insertUser($items);
                    $this->getPresenter()->redirect('Sign:default');
                }
            }

        };
        return $form;
    }


    public function render()
    {
        $this->template->setFile(dirname(__FILE__) . '/register.latte');
        $this->template->form = $this->createComponentRegister();
        $this->template->render();
    }

}
