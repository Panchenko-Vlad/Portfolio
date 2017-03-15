<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ArticleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Новости';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="article-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Создать новость', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'title',
            'description:ntext',
            'content:ntext',
            'date',
            [
                'format' => 'html',
                'label' => 'Картинка',
                'value' => function($data) {
                    return Html::img($data->getImage(), ['width' => 200]);
                }
            ], [
                'format' => 'html',
                'label' => 'Доп. действия',
                'value' => function($data) {
                    if ($data->getStatus() == 'active') {
                        return Html::a(
                            'Скрыть',
                            [Url::toRoute(['article/hide', 'id' => $data->id])],
                            ['class' => 'btn btn-warning']
                        );
                    } else {
                        return Html::a(
                            'Показать',
                            [Url::toRoute(['article/show', 'id' => $data->id])],
                            ['class' => 'btn btn-success']
                        );
                    }
                }
            ],
//            'image',
//            'viewed',
//            'status',
//            'category_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
