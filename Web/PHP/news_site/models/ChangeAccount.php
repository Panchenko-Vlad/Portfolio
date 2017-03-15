<?php

namespace app\models;

use yii\base\InvalidParamException;
use yii\base\Model;
use Yii;

/**
 * @var $user \app\models\User
 */

class ChangeAccount extends Model
{
    private $_user;

    /**
     * При создании экземпляра, сразу проверяем секретный ключ на валидность и в случае ошибки отправляем исключение
     * @param array $key
     * @param array $config
     */
    public function __construct($key, $config = [])
    {
        if (empty($key) || !is_string($key))
            throw new InvalidParamException('Ключ не может быть пустым!');

        $this->_user = User::findBySecretKey($key);

        if (!$this->_user)
            throw new InvalidParamException('Не верный ключ!');

        parent::__construct($config);
    }

    /**
     * Активируем аккаунт пользователя
     * @return bool
     */
    public function activateAccount()
    {
        $user = $this->_user;
        $user->status = User::ACTIVE;
        $user->removeSecretKey();
        return $user->save(false);
    }

    /**
     * Изменяем пароль пользователя
     * @param string $password
     * @return bool
     */
    public function changePassword($password)
    {
        $user = $this->_user;
        $user->password = User::passwordInHashcode($password);
        $user->removeSecretKey();
        return $user->save(false);
    }

    /**
     * Получаем имя пользователя
     * @return string
     */
    public function getUserName()
    {
        $user = $this->_user;
        return $user->name;
    }

    /**
     * Получаем модель текущего пользователя
     */
    public function getModelUser()
    {
        return $this->_user;
    }

    /**
     * Авторизуем пользователя
     * @return bool
     */
    public function loginUser()
    {
        return Yii::$app->user->login($this->_user, Yii::$app->params['rememberUser']);
    }


    /**
     * Использовать ли подтверждение почты при регистрации или нет
     * @return mixed
     */
    public static function isEmailActivation()
    {
        return Yii::$app->params['emailActivation'];
    }

    /**
     * Получаем тех. почту
     * @return mixed
     */
    public static function getSupportEmail()
    {
        return Yii::$app->params['supportEmail'];
    }
}