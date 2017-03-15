<?php
/**
 * Работа с сервисом PushAll
 *
 * @author     Vladyslav Platonov <vlad@plat-x.com>
 * @copyright  2013-2015 Plat-X
 * @version    1.0
 * @link       http://plat-x.com/
 */


namespace app\components;

use yii\base\Model;
use yii\web\HttpException;


/**
 *  АПИ сервиса PushAll.ru
 *
 * @id integer ID пользователя для отправки, обязательно
 * @key string Ключ для отправки, обязательно
 * @type string Тип push-уведомления (TYPE_SELF, TYPE_BROADCAST, TYPE_UNICAST),
 *                  по умолчанию - TYPE_SELF
 * @title string Заголовок уведомления, обязательно
 * @text string Текст уведомления, обязательно
 * @icon string Ссылка на иконку, не обязательно
 * @url string Ссылка, на которую осущиствится переход при
 *              клике на уведомление, не обязательно
 * @hidden Видимость уведомления (HIDDEN_FALSE, HIDDEN_HISTORY, HIDDEN_BAND),
 *          по умолчанию - HIDDEN_FALSE
 * @encode Кодировка сообщения, по умолчанию - UTF-8
 * @priority integer Приоритет сообщения
 *                      (PRIORITY_DEFAULT, PRIORITY_NOT_IMPORTANT, PRIORITY_IMPORTANT),
 *                      по умолчанию - PRIORITY_DEFAULT
 */
class PushAll extends Model
{
    /**
     * Типы
     */
    const TYPE_SELF = 'self';
    const TYPE_BROADCAST = 'broadcast';
    const TYPE_UNICAST = 'unicast';

    /**
     * Скрытие уведомлений
     */
    const HIDDEN_FALSE = 0;
    const HIDDEN_HISTORY = 1;
    const HIDDEN_BAND = 2;

    /**
     * Приоритеты
     */
    const PRIORITY_DEFAULT = 0;
    const PRIORITY_NOT_IMPORTANT = -1;
    const PRIORITY_IMPORTANT = 1;

    /**
     * Тип уведомления
     *
     * @var string
     */
    public $type = self::TYPE_SELF;

    /**
     * Номер подписки
     *
     * @var integer
     */
    public $id;

    /**
     * Ключ подписки
     *
     * @var string
     */
    public $key;

    /**
     * Заголовок уведомления
     *
     * @var string
     */
    public $title;

    /**
     * Текст уведомления
     *
     * @var string
     */
    public $text;

    /**
     * URL картинки (не больше 512кб)
     *
     * @var string
     */
    public $icon;

    /**
     * Ссылка для перехода
     *
     * @var string
     */
    public $url;

    /**
     * Скрытие уведомления
     *
     * @var int
     */
    public $hidden = self::HIDDEN_FALSE;

    /**
     * Кодировка
     *
     * @var string
     */
    public $encode = 'UTF-8';

    /**
     * Приоритет уведомления
     *
     * @var int
     */
    public $priority = self::PRIORITY_DEFAULT;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['title', 'text', 'id', 'key'], 'required'],
            [['id', 'priority', 'hidden'], 'integer'],
            [['url', 'encode', 'icon', 'title', 'text', 'key', 'type'], 'string'],
        ];
    }

    /**
     * @throws \yii\base\Exception
     */
    public function beforeValidate()
    {
        if (empty($this->id)) {
            throw new HttpException('Установите свойство ID для PushAll!');
        }

        if (empty($this->key)) {
            throw new HttpException('Установите свойство key для PushAll!');
        }

        return parent::beforeValidate();
    }

    /**
     * @return mixed
     */
    public function send()
    {
        if ($this->validate()) {
            $params = [
                "type" => $this->type,
                "id" => $this->id,
                "key" => $this->key,
                "text" => $this->text,
                "title" => $this->title,
                "hidden" => $this->hidden,
                "priority" => $this->priority,
            ];

            if (!empty($this->url)) {
                $params['url'] = $this->url;
            }

            if (!empty($this->encode)) {
                $params['encode'] = $this->encode;
            }

            if (!empty($this->icon)) {
                $params['icon'] = $this->icon;
            }

            curl_setopt_array($ch = curl_init(), array(
                CURLOPT_URL => "https://pushall.ru/api.php",
                CURLOPT_POSTFIELDS => $params,
                CURLOPT_SAFE_UPLOAD => true,
                CURLOPT_RETURNTRANSFER => true
            ));

            $return = curl_exec($ch); //получить ответ или ошибку

            curl_close($ch);

            return $return;
        }

        return $this->errors;
    }
}