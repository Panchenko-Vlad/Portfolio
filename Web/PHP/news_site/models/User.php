<?php

namespace app\models;

use Yii;
use yii\helpers\Url;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property integer $isAdmin
 * @property integer $status
 * @property integer $isSendEmail
 * @property integer $isSendBrowser
 * @property string $secret_key
 * @property string $isAllowFullNews
 *
 * @property Comment[] $comments
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    const ACTIVE = 1;
    const NOT_ACTIVE = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'email', 'password'], 'required'],
            [['name'], 'match', 'pattern' => '#^[a-z0-9_-]+$#i'],
            [['email'], 'email'],
            [['email'], 'unique'],
            [['name', 'email', 'password'], 'string', 'max' => 255],
            [['isAdmin', 'status', 'isSendEmail', 'isAllowFullNews'], 'integer'],
            [['secret_key'], 'unique'],
            [['status'], 'default', 'value' => User::ACTIVE, 'on' => 'default'],
            [['status'], 'default', 'value' => User::NOT_ACTIVE, 'on' => 'emailActivation']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Имя',
            'email' => 'E-mail',
            'password' => 'Пароль',
            'isAdmin' => 'Админ',
            'status' => 'Статус',
            'isSendEmail' => 'Уведомление по почте',
            'isSendBrowser' => 'Уведомление через браузер',
            'secret_key' => 'Секретный ключ'
        ];
    }

    /**
     * Получаем все комментарии, которые относятся к текущему пользователю
     * @return \yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(Comment::className(), ['user_id' => 'id']);
    }

    /**
     * Получаем модель указанного пользователя
     * @param int|string $id
     * @return static
     */
    public static function findIdentity($id)
    {
        return User::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        // TODO: Implement findIdentityByAccessToken() method.
    }

    /**
     * Получаем идентификатор текущего пользователя
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        // TODO: Implement getAuthKey() method.
    }

    public function validateAuthKey($authKey)
    {
        // TODO: Implement validateAuthKey() method.
    }

    /**
     * Получаем пользователя, по указанной почте
     * @param string $email
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function findByEmail($email)
    {
        return User::find()->where(['email' => $email])->one();
    }

    /**
     * Шифруем пароль пользователя
     * @param string $password
     * @return string
     */
    public static function passwordInHashcode($password)
    {
        return Yii::$app->getSecurity()->generatePasswordHash($password);
    }

    /**
     * Проверяем на валидацию введенный пароль
     * @param string $password
     * @return bool
     */
    public function validatePassword($password)
    {
        return Yii::$app->getSecurity()->validatePassword($password, $this->password);
    }

    /**
     * Создаем нового пользователя
     * @return bool
     */
    public function create()
    {
        $this->password = User::passwordInHashcode($this->password);

        // Если для текущей модели используется сценарий emailActivation, то генерируем секретный ключ
        if ($this->scenario === 'emailActivation') {
            $this->status = User::NOT_ACTIVE;
            $this->generateSecretKey();
        } else {
            $this->status = User::ACTIVE;
            Email::sendNewUser($this);
        }

        return $this->save(false);
    }

    /**
     * Авторизация через Вконтакте. (Отключена, так как нельзя получить e-mail)
     * @param int $uid
     * @param string $firstName
     * @param string $lastName
     * @return bool
     */
    public function saveFromVk($uid, $firstName, $lastName)
    {
        $user = User::findByEmail($uid);

        if ($user) return Yii::$app->user->login($user);

        $this->email = $uid;
        $this->name = $firstName;
        $this->create();

        return Yii::$app->user->login($this);
    }

    /**
     * Генерируем секретный ключ для пользователя
     */
    public function generateSecretKey()
    {
        $this->secret_key = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Обнуляем секретный ключ пользователя
     */
    public function removeSecretKey()
    {
        $this->secret_key = null;
    }

    /**
     * Если ключ не истек, получаем пользователя у какого секретный ключ совпадает с переданным
     * @param string $key Секретный ключ пользователя
     * @return null|static
     */
    public static function findBySecretKey($key)
    {
        if (!self::isSecretKeyExpire($key)) return null;

        return User::findOne(['secret_key' => $key]);
    }

    /**
     * Проверяем не истекло ли время секретного ключа
     * @param string $key Секретный ключ пользователя
     * @return bool
     */
    public static function isSecretKeyExpire($key)
    {
        if (empty($key)) return false;

        $expire = Yii::$app->params['secretKeyExpire']; // Получаем время продолжительности секретного ключа
        $parts = explode('_', $key); // Делим на части сам ключ и его время создания
        $timestamp = (int) end($parts); // Получаем время создания секретного ключа
        return $timestamp + $expire >= time(); // Проверяем не истекло ли время секретного ключа
    }

    /**
     * Генерируем флеш сообщение для пользователя
     * @param string $typeMessage
     * @param string $message
     */
    public static function setFlashMessage($typeMessage, $message)
    {
        Yii::$app->session->setFlash($typeMessage, $message);
    }

    /**
     * Проверка, является ли переданный идентификатор, идентификатором текущего пользователя
     * @param int $id
     * @return bool
     */
    public static function isYou($id)
    {
        return Yii::$app->user->getId() == $id;
    }

    /**
     * Снятие админки
     * @return bool
     */
    public function removeAdmin()
    {
        $this->isAdmin = User::NOT_ACTIVE;
        return $this->save(false);
    }

    /**
     * Установка админки
     * @return bool
     */
    public function setupAdmin()
    {
        $this->isAdmin = User::ACTIVE;
        return $this->save(false);
    }

    /**
     * Снятие активации пользователя (Не сможет залогиниться, но сможет заново зарегистрироваться)
     * @return bool
     */
    public function removeStatus()
    {
        $this->status = User::NOT_ACTIVE;
        return $this->save(false);
    }

    /**
     * Установка активации пользователя
     * @return bool
     */
    public function setupStatus()
    {
        $this->status = User::ACTIVE;
        return $this->save(false);
    }

    /**
     * Снятие рассылки новых новостей на почту
     * @return bool
     */
    public function removeSendEmail()
    {
        $this->isSendEmail = User::NOT_ACTIVE;
        return $this->save(false);
    }

    /**
     * Установка рассылки новых новостей на почту
     * @return bool
     */
    public function setupSendEmail()
    {
        $this->isSendEmail = User::ACTIVE;
        return $this->save(false);
    }

    /**
     * Снятие рассылки новых новостей в браузер
     * @return bool
     */
    public function removeSendBrowser()
    {
        $this->isSendBrowser = User::NOT_ACTIVE;
        return $this->save(false);
    }

    /**
     * Установка рассылки новых новостей в браузер
     * @return bool
     */
    public function setupSendBrowser()
    {
        $this->isSendBrowser = User::ACTIVE;
        return $this->save(false);
    }


    /**
     * Запрет на просмотр полных новостей
     * @return bool
     */
    public function disallowFullNews()
    {
        $this->isAllowFullNews = User::NOT_ACTIVE;
        return $this->save(false);
    }

    /**
     * Снятие запрета на просмотр полных новостей
     * @return bool
     */
    public function allowFullNews()
    {
        $this->isAllowFullNews = User::ACTIVE;
        return $this->save(false);
    }

    /**
     * Изменение состояние рассылки на новые новости через почту
     * @param int $value
     * @return bool
     */
    public static function setSendEmail($value)
    {
        $user = User::findOne(Yii::$app->user->id);
        $user->isSendEmail = $value;
        return $user->save();
    }

    /**
     * Изменение состояние рассылки на новые новости через браузер
     * @param int $value
     * @return bool
     */
    public static function setSendBrowser($value)
    {
        $user = User::findOne(Yii::$app->user->id);
        $user->isSendBrowser = $value;
        return $user->save();
    }

    /**
     * При необходимости удалять все старые секретные ключи
     */
    public static function deleteAllSecret_key()
    {
        $users = User::find()->where('secret_key' != null)->all();

        foreach ($users as $user) {
            $user->secret_key = null;
            $user->save();
        }
    }

    /**
     * Получаем кол-во превью новостей на главной странице
     * @return mixed
     */
    public static function getPageSize()
    {
        $session = Yii::$app->session;
        $session->open();

        if (!isset($session['pageSize']))
            $session['pageSize'] = Yii::$app->params['default_pageSize'];

        return $session['pageSize'];
    }

    /**
     * Устанавливаем кол-во превью новостей на главной странице
     * @param int $pageSize
     */
    public static function setPageSize($pageSize)
    {
        $session = Yii::$app->session;
        $session->open();
        $session['pageSize'] = $pageSize;
    }

    /**
     * Получаем кол-во превью новостей на странице конкретной категории
     * @return mixed
     */
    public static function getPageSizeByCategory()
    {
        $session = Yii::$app->session;
        $session->open();

        if (!isset($session['pageSizeByCategory']))
            $session['pageSizeByCategory'] = Yii::$app->params['default_pageSizeByCategory'];

        return $session['pageSizeByCategory'];
    }

    /**
     * Устанавливаем кол-во превью новостей на странице конкретной категории
     * @param int $pageSize
     */
    public static function setPageSizeByCategory($pageSize)
    {
        $session = Yii::$app->session;
        $session->open();
        $session['pageSizeByCategory'] = $pageSize;
    }

    /**
     * Push-уведомления в браузере
     * @param Article $article
     */
    public static function sendBrowserNotice(Article $article)
    {
        if (Yii::$app->user->identity['isSendBrowser']) {
            curl_setopt_array($ch = curl_init(), array(
                CURLOPT_URL => "https://pushall.ru/api.php",
                CURLOPT_POSTFIELDS => array(
                    "type" => "self",
                    "id" => "50986",
                    "key" => "482742413c8f222cfc4ecda5f61fc46a",
                    "title" => $article->title,
                    "text" => strip_tags($article->description),
//                "icon" => Url::to('@app/web') . $article->getImage(), // не рекомендуется. Разве что маловесовые
                    "url" => Yii::$app->urlManager->createAbsoluteUrl(['/site/view', 'id' => $article->id])
                ),
                CURLOPT_RETURNTRANSFER => true
            ));
            $return = curl_exec($ch);
            curl_close($ch);
        }
    }
}
