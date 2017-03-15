<?php

use yii\helpers\Html;

/* @var $this \yii\web\View view component instance */
/* @var $user array */

?>
<!--main content start-->
<div class="main-content">
    <div class="container">
        <div class="row">
            <div class="col-md-8">

                <br>

                <h4><i><?= Html::a('Для изменения пароля перейдите по этой ссылке.',
                    Yii::$app->urlManager->createAbsoluteUrl([
                        '/auth/change-password',
                        'key' => $user->secret_key // Передаем параметр через $_GET метод
                    ])) ?></i></h4>

                <br>

            </div>
        </div>
    </div>
</div>
<!-- end main content-->

