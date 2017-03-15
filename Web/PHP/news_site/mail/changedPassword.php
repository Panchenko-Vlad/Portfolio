<?php
/* @var $this \yii\web\View view component instance */
/* @var $user app\models\User */
/* @var $newPassword string */
?>
<!--main content start-->
<div class="main-content">
    <div class="container">
        <div class="row">
            <div class="col-md-8">

                <h4><strong><?= $user->name ?>, пароль успешно был изменен.</strong></h4>

                <hr>

                <h6>Данные для входа на сайт:</h6>

                <br><br>

                <article class="post">
                    <h4>E-mail: <strong><?= $user->email ?></strong></h4>
                    <h4>Новый пароль: <strong><?= $newPassword ?></strong></h4>
                </article>

            </div>
        </div>
    </div>
</div>
<!-- end main content-->

