<?php

use app\models\User;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Пользователи';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Создать пользователя', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'email:email', [
                'format' => 'html',
                'label' => 'Админка',
                'value' => function($data) {
                    if ($data->isAdmin) {
                        return Html::a(
                            (User::isYou($data->id)) ? '(Вы) active' : 'active',
                            [Url::toRoute(['user/remove-admin', 'id' => $data->id])],
                            ['class' => 'btn btn-success']
                        );
                    } else {
                        return Html::a(
                            'no active',
                            [Url::toRoute(['user/setup-admin', 'id' => $data->id])],
                            ['class' => 'btn btn-danger']
                        );
                    }
                }
            ], [
                'format' => 'html',
                'label' => 'Статус активации',
                'value' => function($data) {
                    if ($data->status) {
                        return Html::a(
                            (User::isYou($data->id)) ? '(Вы) active' : 'active',
                            [Url::toRoute(['user/remove-status', 'id' => $data->id])],
                            ['class' => 'btn btn-success']
                        );
                    } else {
                        return Html::a(
                            'no active',
                            [Url::toRoute(['user/setup-status', 'id' => $data->id])],
                            ['class' => 'btn btn-danger']
                        );
                    }
                }
            ], [
                'format' => 'html',
                'label' => 'Уведомление по почте',
                'value' => function($data) {
                    if ($data->isSendEmail) {
                        return Html::a(
                            (User::isYou($data->id)) ? '(Вы) active' : 'active',
                            [Url::toRoute(['user/remove-send-email', 'id' => $data->id])],

                            ['class' => 'btn btn-success']
                        );
                    } else {
                        return Html::a(
                            'no active',
                            [Url::toRoute(['user/setup-send-email', 'id' => $data->id])],
                            ['class' => 'btn btn-danger']
                        );
                    }
                }
            ], [
                'format' => 'html',
                'label' => 'Уведомление в браузере',
                'value' => function($data) {
                    if ($data->isSendBrowser) {
                        return Html::a(
                            (User::isYou($data->id)) ? '(Вы) active' : 'active',
                            [Url::toRoute(['user/remove-send-browser', 'id' => $data->id])],

                            ['class' => 'btn btn-success']
                        );
                    } else {
                        return Html::a(
                            'no active',
                            [Url::toRoute(['user/setup-send-browser', 'id' => $data->id])],
                            ['class' => 'btn btn-danger']
                        );
                    }
                }
            ], [
                'format' => 'html',
                'label' => 'Просмотр полной статьи',
                'value' => function($data) {
                    if ($data->isAllowFullNews) {
                        return Html::a(
                            (User::isYou($data->id)) ? '(Вы) Разрешено' : 'Разрешено',
                            [Url::toRoute(['user/disallow-full-news', 'id' => $data->id])],

                            ['class' => 'btn btn-success']
                        );
                    } else {
                        return Html::a(
                            'Запрещено',
                            [Url::toRoute(['user/allow-full-news', 'id' => $data->id])],
                            ['class' => 'btn btn-danger']
                        );
                    }
                }
            ],
            // 'status',
            // 'secret_key',
            // 'isSendEmail:email',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
