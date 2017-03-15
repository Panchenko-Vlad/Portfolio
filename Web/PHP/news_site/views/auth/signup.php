<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

$this->title = 'Регистрация';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="leave-comment mr0"><!--leave comment-->
    <div class="row">
        <div class="col-lg-6 col-lg-offset-3
                    col-sm-8 col-sm-offset-2
                    col-xs-10 col-xs-offset-1">
            <div class="site-login">
                <h1><?= Html::encode($this->title) ?></h1>

                <p>Для регистрации заполните следующие поля:</p>

                <?php if (Yii::$app->session->getFlash('success')): ?>
                    <div class="alert alert-success" role="alert">
                        <?= Yii::$app->session->getFlash('success') ?>
                    </div>
                <?php elseif (Yii::$app->session->getFlash('error')): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= Yii::$app->session->getFlash('error') ?>
                    </div>
                <?php endif; ?>

                <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

                <?= $form->field($model, 'name')->textInput(['autofocus' => true]) ?>
                <?= $form->field($model, 'email')->textInput() ?>

                <?= $form->field($model, 'password')->passwordInput() ?>

                <div class="form-group">
                    <div class="col-lg-12">
                        <?php if (\app\models\ChangeAccount::isEmailActivation()): ?>
                            <i>*На указанный E-mail будет отправлено письмо для активации аккаунта.</i>
                        <?php endif; ?>

                        <?= Html::submitButton('Войти', [
                            'class' => 'btn btn-primary col-md-2 pull-right',
                            'name' => 'login-button'
                        ]) ?>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>

<!--        <div class="col-md-2 col-md-offset-1">-->
            <!-- Put this script tag to the <head> of your page -->
<!--            <script type="text/javascript" src="//vk.com/js/api/openapi.js?140"></script>-->

<!--            <script type="text/javascript">-->
<!--                VK.init({apiId: 5911445});-->
<!--            </script>-->

           <!-- Put this div tag to the place, where Auth block will be -->
<!--            <div id="vk_auth"></div>-->
<!--            <script type="text/javascript">-->
<!--                VK.Widgets.Auth("vk_auth", {width: "200px", authUrl: '/auth/login-vk'});-->
<!--            </script>-->
<!--        </div>-->
    </div>
</div>