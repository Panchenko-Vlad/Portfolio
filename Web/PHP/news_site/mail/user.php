<?php
/* @var $this \yii\web\View view component instance */
/* @var $user array */
/* @var $newUser array */
?>
<!--main content start-->
<div class="main-content">
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <h4><strong><?= $user->name ?>, добавлен новый пользователь.</strong></h4>
                <hr>
                <article class="post">
                    <h4>ID: <strong><?= $newUser->id ?></strong></h4>
                    <h4>Имя: <strong><?= $newUser->name ?></strong></h4>
                    <h4>Почта: <strong><?= $newUser->email ?></strong></h4>
                </article>
            </div>
        </div>
    </div>
</div>
<!-- end main content-->

