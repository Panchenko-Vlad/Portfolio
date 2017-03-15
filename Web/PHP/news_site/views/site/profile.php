<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $user app\models\User */

$this->title = 'Профиль';
?>

<div class="main-content">
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1 profile">

                <?php if (Yii::$app->session->getFlash('success')): ?>
                    <div class="alert alert-success" role="alert">
                        <?= Yii::$app->session->getFlash('success') ?>
                    </div>
                <?php elseif (Yii::$app->session->getFlash('error')): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= Yii::$app->session->getFlash('error') ?>
                    </div>
                <?php endif; ?>

                <h4>Ваши данные <?= $user->isAdmin ? '(Админ)' : '' ?></h4>

                <hr>

                <ul>
                    <li>Имя: <strong><?= $user->name ?></strong></li>
                    <li>E-mail: <strong><?= $user->email ?></strong></li>
                    <li>Уведомления:
                        <strong>
                            <?php if ($user->isSendEmail && $user->isSendBrowser): ?>
                                на почту и браузер
                            <?php elseif ($user->isSendEmail): ?>
                                только на почту
                            <?php elseif ($user->isSendBrowser): ?>
                                только в браузер
                            <?php else: ?>
                                не получаю уведомлений
                            <?php endif; ?>
                        </strong></li>
                    <li>Просмотр полных новостей: <strong><?= $user->isAllowFullNews ? 'разрешен' : 'запрещен'?></strong></li>
                </ul>

                <hr>

                <h6>Настройки уведомлений</h6>

                <?php $form = ActiveForm::begin([
                        'class' => 'checkbox',
                ]); ?>

                <?= $form->field($user, 'isSendEmail')->checkbox(['email' => 'Уведомлять о новых новостях через E-mail']) ?>
                <?= $form->field($user, 'isSendBrowser')->checkbox(['browser' => 'Уведомлять о новых новостях через браузер']) ?>

                <?= Html::submitButton('Сохранить', [
                    'class' => 'btn btn-primary',
                    'name' => 'login-button'
                ]) ?>

                <?php ActiveForm::end(); ?>

                <h6>Изменение пароля</h6>

                <?= Html::a('Изменить пароль', ['auth/setup-link-new-password', 'id' => $user->id], [
                    'class' => 'btn btn-primary',
                    'data' => [
                        'confirm' => 'Вы уверены что хотите изменить пароль?',
                        'method' => 'post',
                    ],
                ]) ?>

                <br><br>

                <i>*На почту будет прислано сообщение со ссылкой, где вы сможете изменить пароль.</i>
            </div>
        </div>
    </div>
</div>


