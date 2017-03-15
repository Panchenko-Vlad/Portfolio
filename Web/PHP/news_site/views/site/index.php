<?php

use app\models\User;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;

/* @var $this yii\web\View */
/* @var $pagination yii\data\Pagination */

/* @var $articles array */
/* @var $popular array */
/* @var $recent array */
/* @var $categories array */

$this->title = 'Новостной блог';
?>
<!--main content start-->
<div class="main-content">
    <div class="container">
        <div class="row">
            <div class="col-md-8">

                <?php if (Yii::$app->session->getFlash('success')): ?>
                    <div class="alert alert-success" role="alert">
                        <?= Yii::$app->session->getFlash('success') ?>
                    </div>
                <?php elseif (Yii::$app->session->getFlash('error')): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= Yii::$app->session->getFlash('error') ?>
                    </div>
                <?php endif; ?>

                <?php foreach ($articles as $article): ?>
                    <article class="post">
                        <div class="post-thumb">
                            <a href="<?= Url::toRoute(['site/view', 'id' => $article->id]) ?>"><img src="<?= $article->getImage() ?>" alt=""></a>

                            <a href="<?= Url::toRoute(['site/view', 'id' => $article->id]) ?>" class="post-thumb-overlay text-center">
                                <div class="text-uppercase text-center">Просмотр</div>
                            </a>
                        </div>
                        <div class="post-content">
                            <header class="entry-header text-center text-uppercase">
                                <h6><a href="<?= Url::toRoute(['site/category', 'id' => $article->category->id]) ?>"> <?= $article->category->title ?></a></h6>

                                <h1 class="entry-title"><a href="<?= Url::toRoute(['site/view', 'id' => $article->id]) ?>"><?= $article->title ?></a></h1>


                            </header>
                            <div class="entry-content">
                                <p><?= $article->description ?></p>

                                <div class="btn-continue-reading text-center text-uppercase">
                                    <a href="<?= Url::toRoute(['site/view', 'id' => $article->id]) ?>" class="more-link">Читать дальше</a>
                                </div>
                            </div>
                            <div class="social-share">
                                <span class="social-share-title pull-left text-capitalize"><?= $article->getDate() ?></span>
                                <ul class="text-center pull-right">
                                    <li><a class="s-facebook" href="#"><i class="fa fa-eye"></i></a></li><?= (int) $article->viewed ?>
                                </ul>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>

                <?php
                echo LinkPager::widget([
                    'pagination' => $pagination,
                ]);
                ?>

                <?php if (!empty($articles)): ?>
                    <div class="btn-group pull-right pageSize">
                        <button type="button" class="btn btn-default"><?= User::getPageSize() ?></button>
                        <button type="button" data-toggle="dropdown" class="btn btn-default"><span class="caret"></span></button>
                        <ul class="dropdown-menu">
                            <li><?= Html::a('1', ['site/change-page-size', 'pageSize' => 1]) ?></li>
                            <li><?= Html::a('2', ['site/change-page-size', 'pageSize' => 2]) ?></li>
                            <li><?= Html::a('3', ['site/change-page-size', 'pageSize' => 3]) ?></li>
                            <li><?= Html::a('4', ['site/change-page-size', 'pageSize' => 4]) ?></li>
                            <li><?= Html::a('5', ['site/change-page-size', 'pageSize' => 5]) ?></li>
                            <!--                        <li class="divider"></li>-->
                        </ul>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <?= $this->render('/partials/sidebar', [
                'popular' => $popular,
                'recent' => $recent,
                'categories' => $categories
            ]) ?>
        </div>
    </div>
</div>
<!-- end main content-->