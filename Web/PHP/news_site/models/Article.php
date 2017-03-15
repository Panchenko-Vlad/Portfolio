<?php

namespace app\models;

use Yii;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * This is the model class for table "article".
 *
 * @property integer $id
 * @property string $title
 * @property string $description
 * @property string $content
 * @property string $image
 * @property integer $viewed
 * @property integer $status
 * @property string $date
 * @property integer $category_id
 *
 * @property Comment[] $comments
 */
class Article extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_NOT_ACTIVE = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'article';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['title', 'required'],
            [['title', 'description', 'content'], 'string'],
            [['date'], 'date', 'format' => 'php:Y-m-d'],
            [['date'], 'default', 'value' => date('Y-m-d')],
            [['title'], 'string', 'max' => 255],
            [['image'], 'file', 'extensions' => 'jpg,png']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Заголовок',
            'description' => 'Описание',
            'content' => 'Контент',
            'image' => 'Картинка',
            'viewed' => 'Просмотров',
            'status' => 'Статус',
            'date' => 'Дата',
            'category_id' => 'ID Категории',
        ];
    }

    /**
     * Перед удалением самой статьи, удаляем из проекта её картинку
     * @return bool
     */
    public function beforeDelete()
    {
        $this->deleteImage(); // Удалим картинку, перед удалением самой статьи
        return parent::beforeDelete();
    }

    /**
     * Является ли гостем текущий пользователь
     * @return bool
     */
    public static function isGuest()
    {
        return Yii::$app->user->isGuest;
    }

    /**
     * Разрешается ли текущему пользователю просматривать полную новость
     * @return mixed
     */
    public static function isAllowedFullNews()
    {
        return Yii::$app->user->identity['isAllowFullNews'];
    }


    /**
     * Сохраняем имя картинки в базу
     * @param string $fileName
     * @return bool
     */
    public function saveImage($fileName)
    {
        $this->image = $fileName;
        return $this->save(false);
    }

    /**
     * Загружаем картинку и сохраняем в текущую модель
     * @param ImageUpload $model
     * @return bool
     */
    public function imageLoad(ImageUpload $model)
    {
        $file = UploadedFile::getInstance($model, 'image');

        return $this->saveImage($model->uploadFile($file, $this->image));
    }

    /**
     * Сохраняем идентификатор категории к какой относится данная новость
     * @param int $category_id
     * @return bool
     */
    public function saveCategory($category_id)
    {
        $category = Category::findOne($category_id);
        if ($category != null) {
            $this->link('category', $category);
            return true;
        }
    }

    /**
     * Сохраняем текущую новость и по умолчанию указываем её статус активным
     * @return bool
     */
    public function saveArticle()
    {
        $this->status = self::STATUS_ACTIVE;
        return $this->save();
    }

    /**
     * Удаление картинки текущей новости
     */
    public function deleteImage()
    {
        $imageUploadModel = new ImageUpload();
        $imageUploadModel->deleteCurrentImage($this->image);
    }

    /**
     * Получаем путь к картинке относительно папки web, если картинки нет, возвращаем картинку по умолчанию
     * @return string
     */
    public function getImage()
    {
        return ($this->image) ? '/uploads/' . $this->image : '/no-image.png';
    }

    /**
     * Устанавливаем связь между текущей новости и категорией к какой она присвоена. После чего можем получать
     * данные о категории напрямую через новость
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }

    /**
     * Форматируем дату в желаемый вид. Вид по-умолчанию устанавилвается в config/web.php
     * @return string
     */
    public function getDate()
    {
        // Из 2017-03-06 в 6 Марта 2017
        return Yii::$app->formatter->asDate($this->date);
    }

    /**
     * Получаем список всех активных новостей
     * @return array
     */
    public static function getAll()
    {
        $query = Article::find()->where(['status' => 1])->orderBy('id desc');

        $count = $query->count();

        $pagination = new Pagination(['totalCount' => $count, 'pageSize' => User::getPageSize()]);

        $articles = $query->offset($pagination->offset)->limit($pagination->limit)->all();

        $data = [
            'articles' => $articles,
            'pagination' => $pagination
        ];

        return $data;
    }

    /**
     * Получаем указанное кол-во самых популярных новостей
     * @param int $limit
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getPopular($limit = 3)
    {
        return self::find()->where(['status' => 1])->orderBy('viewed desc')->limit($limit)->all();
    }

    /**
     * Получаем указанное кол-во свежих новостей
     * @param int $recent
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getRecent($recent = 4)
    {
        return self::find()->where(['status' => 1])->orderBy('id desc')->limit($recent)->all();
    }

    /**
     * Устанавливаем связь между текущей новости и всеми комментариями относящимися к этой новости
     * @return \yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(Comment::className(), ['article_id' => 'id']);
    }

    /**
     * Инкрементируем кол-во просмотров новости
     * @return bool
     */
    public function viewedCounter()
    {
        $this->viewed += 1;
        return $this->save(false);
    }

    /**
     * Получаем все активные комментарии, относящиеся к текущей новости
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getArticleComments()
    {
        return $this->getComments()->where(['status' => 1])->all();
    }

    /**
     * Получаем текстовый вид статуса текущей новости
     * @return string
     */
    public function getStatus()
    {
        if ($this->status == self::STATUS_ACTIVE) {
            return 'active';
        } else {
            return 'no active';
        }
    }

    /**
     * Устанавливаем для новости неактивный статус
     * @return bool
     */
    public function hidden()
    {
        $this->status = self::STATUS_NOT_ACTIVE;
        return $this->save(false);
    }

    /**
     * Устанавливаем для новости активный статус
     * @return bool
     */
    public function show()
    {
        $this->status = self::STATUS_ACTIVE;
        return $this->save(false);
    }
}
