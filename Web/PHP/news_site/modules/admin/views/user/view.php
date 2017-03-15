<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы уверены что хотите удалить пользователя?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'email:email',
            'password', [
                'label' => 'Админка',
                'value' => function($data) {
                    return $data->isAdmin ? 'active' : 'no active';
                }
            ], [
                'label' => 'Статус активации',
                'value' => function($data) {
                    return $data->status ? 'active' : 'no active';
                }
            ], [
                'label' => 'Уведомление по почте',
                'value' => function($data) {
                    return $data->isSendEmail ? 'active' : 'no active';
                }
            ],
            'secret_key',
        ],
    ]) ?>

</div>
