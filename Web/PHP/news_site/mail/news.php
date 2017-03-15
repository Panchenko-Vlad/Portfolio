<?php
/* @var $this \yii\web\View view component instance */
use yii\helpers\Html;

/* @var $article array */
?>
<!--main content start-->
<div class="main-content">
    <div class="container">
        <div class="row">
            <div class="col-md-8">

                <h4 style="color: black; font-style: italic">
                    <strong><?= $user->name ?>, добавлена новость.</strong>
                </h4>

                <hr>

                <br>

                <article class="post" style="text-align: center">

                    <img src="<?= $message->embed($pathToImage) ?>" height="400px" width="auto" alt="">

                    <h4>
                        <strong>
                            <?= Html::a($article->category->title,
                                Yii::$app->urlManager->createAbsoluteUrl([
                                    '/site/category',
                                    'id' => $article->category->id // Передаем параметр через $_GET метод
                                ]), [
                                    'style' => '
                                        color: #00acdf; 
                                        font-weight: 700;
                                        font-size: 12px;
                                        text-decoration: none;
                                        letter-spacing: 0.5px;'
                                ]) ?>
                        </strong>
                    </h4>

                    <h1>
                        <?= Html::a($article->title,
                            Yii::$app->urlManager->createAbsoluteUrl([
                                '/site/view',
                                'id' => $article->id // Передаем параметр через $_GET метод
                            ]), [
                                'style' => '
                                    font-weight: 600; 
                                    color: #333333; 
                                    font-size: 24px;
                                    line-height: 34px;
                                    text-decoration: none;
                                    text-transform: uppercase;'
                            ]) ?>
                    </h1>

                    <p style="color: black;"><?= $article->description ?></p>

                    <br><br>

                </article>
            </div>
        </div>
    </div>
</div>
<!-- end main content-->

