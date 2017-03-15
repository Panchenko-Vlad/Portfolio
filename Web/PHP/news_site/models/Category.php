<?php

namespace app\models;

use Yii;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "category".
 *
 * @property integer $id
 * @property string $title
 */
class Category extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Название',
        ];
    }

    /**
     * Устанавливаем связь между текущей категорией и новостями, какие относятся к данной категории
     * @return \yii\db\ActiveQuery
     */
    public function getArticles()
    {
        return $this->hasMany(Article::className(), ['category_id' => 'id']);
    }

    /**
     * Получаем кол-во новостей, какие относятся к данной категории
     * @return int|string
     */
    public function getArticlesCount()
    {
        return $this->getArticles()->where(['status' => 1])->count();
    }

    /**
     * Получаем список всех категорий
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getAll()
    {
        return Category::find()->all();
    }

    /**
     * Получаем список всех категорий в виде ключ -> значение
     * @return array
     */
    public static function getAllInArray()
    {
        return ArrayHelper::map(Category::getAll(), 'id', 'title');
    }

    /**
     * Получаем список активных новостей, какие относятся к указанной категории
     * @param int $id
     * @return array
     */
    public static function getArticlesByCategory($id)
    {
        $query = Article::find()->where(['status' => 1, 'category_id' => $id]);

        $count = $query->count();

        $pagination = new Pagination(['totalCount' => $count, 'pageSize' => User::getPageSizeByCategory()]);

        $articles = $query->offset($pagination->offset)->limit($pagination->limit)->all();

        $data = [
            'articles' => $articles,
            'pagination' => $pagination
        ];

        return $data;
    }
}
