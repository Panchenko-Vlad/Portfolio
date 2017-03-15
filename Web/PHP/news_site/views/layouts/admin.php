<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => 'Вернуться',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => [
            ['label' => 'Пользователи', 'url' => ['/admin/user/index']],
            ['label' => 'Новости', 'url' => ['/admin/article/index']],
            ['label' => 'Комментарии', 'url' => ['/admin/comment/index']],
            ['label' => 'Категории', 'url' => ['/admin/category/index']],
        ],
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; Yii-site <?= date('Y') ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
<?php $this->registerJsFile('/ckeditor/ckeditor.js') ?>
<?php $this->registerJsFile('/ckfinder/ckfinder.js') ?>
<script>
    $(document).ready(function() {
        var editor = CKEDITOR.replaceAll();
        CKFinder.setupCKEditor(editor);
    })
</script>
</body>
</html>
<?php $this->endPage() ?>
