<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $user app\models\User */

$this->title = 'Изменение пароля';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="leave-comment mr0"><!--leave comment-->
    <div class="row">
        <div class="col-lg-6 col-lg-offset-3
                    col-sm-8 col-sm-offset-2
                    col-xs-10 col-xs-offset-1">
            <div class="site-login">
                <h1><?= Html::encode($this->title) ?></h1>

                <p>Для изменения пароля заполните следующее поле:</p>

                <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

                <?= $form->field($user, 'password')->passwordInput([
                    'value' => '',
                    'placeholder' => 'Введите новый пароль'
                ]) ?>
                <div class="form-group">
                    <div class="col-lg-12">
                        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary col-md-2 pull-right', 'name' => 'login-button']) ?>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>