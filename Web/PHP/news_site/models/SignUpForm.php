<?php

namespace app\models;

use Yii;
use yii\base\Model;

class SignUpForm extends Model
{
    public $name;
    public $email;
    public $password;

    public function rules()
    {
        return [
            [['name', 'email', 'password'], 'required'],
            [['name'], 'string'],
            [['email'], 'email'],
            [['email'], 'unique', 'targetClass' => 'app\models\User', 'targetAttribute' => 'email'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Имя',
            'email' => 'E-mail',
            'password' => 'Пароль'
        ];
    }

    /**
     * Регистрация пользователя
     * @return User
     */
    public function signUp()
    {
        // Если пользователь с такой почтой уже существует и его статус == 0, удаляем
        $this->checkThisUser();

        if ($this->validate()) {
            // Если emailActivation == true, используем сценарий emailActivation, иначе используем default
            $user = ChangeAccount::isEmailActivation() ? new User(['scenario' => 'emailActivation']) : new User();

            // Записываем в модель пользователя введенные данные из формы
            $user->attributes = $this->attributes;
            $user->create();

            return $user;
        }
    }

    /**
     * Если пользователь с такой почтой уже существует и его статус = 0, удаляем
     */
    public function checkThisUser()
    {
        $user = User::findByEmail($this->email);
        if ($user !== null && $user->status == 0) {
            $thisUser = User::findIdentity($user->id);
            $thisUser->delete();
        }
    }
}