<?php

use yii\helpers\Html;

/**
 * @var $this yii\web\View
 * @var $user app\models\User
 */

?>

<br>

<h4><i><?= Html::a('Для активации аккаунта перейдите по этой ссылке.',
    Yii::$app->urlManager->createAbsoluteUrl([
        '/auth/activate-account',
        'key' => $user->secret_key // Передаем параметр через $_GET метод
    ])) ?></i></h4>

<br>
