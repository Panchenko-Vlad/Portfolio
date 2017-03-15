<?php

use app\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

/* @var $this yii\web\View */
/* @var $pagination yii\data\Pagination */

/* @var $category array */
/* @var $articles array */
/* @var $popular array */
/* @var $recent array */
/* @var $categories array */

$this->title = $category->title;
?>
<!--main content start-->
<div class="main-content">
    <div class="container">
        <div class="row">
            <div class="col-md-8">

                <?php foreach ($articles as $article): ?>
                <article class="post post-list">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="post-thumb">
                                <a href="<?= Url::toRoute(['site/view', 'id' => $article->id]) ?>"><img src="<?= $article->getImage() ?>" alt="" class="pull-left"></a>

                                <a href="<?= Url::toRoute(['site/view', 'id' => $article->id]) ?>" class="post-thumb-overlay text-center">
                                    <div class="text-uppercase text-center">Просмотр</div>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="post-content">
                                <header class="entry-header text-uppercase">
                                    <h6><a href="<?= Url::toRoute(['site/category', 'id' => $article->category->id]) ?>"> <?= $article->category->title ?></a></h6>

                                    <h1 class="entry-title"><a href="<?= Url::toRoute(['site/view', 'id' => $article->id]) ?>"><?= $article->title ?></a></h1>
                                </header>
                                <div class="entry-content">
                                    <p><?= $article->description ?></p>
                                </div>
                                <div class="social-share">
                                    <span class="social-share-title pull-left text-capitalize"><?= $article->getDate() ?></span>
                                </div>
                            </div>
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
                        <button type="button" class="btn btn-default"><?= User::getPageSizeByCategory() ?></button>
                        <button type="button" data-toggle="dropdown" class="btn btn-default"><span class="caret"></span></button>

                        <ul class="dropdown-menu">
                            <li><?= Html::a('1', ['site/change-page-size-by-category',
                                    'pageSize' => 1,
                                    'category_id' => $category->id]) ?></li>

                            <li><?= Html::a('2', ['site/change-page-size-by-category',
                                    'pageSize' => 2,
                                    'category_id' => $category->id]) ?></li>

                            <li><?= Html::a('3', ['site/change-page-size-by-category',
                                    'pageSize' => 3,
                                    'category_id' => $category->id]) ?></li>

                            <li><?= Html::a('4', ['site/change-page-size-by-category',
                                    'pageSize' => 4,
                                    'category_id' => $category->id]) ?></li>

                            <li><?= Html::a('5', ['site/change-page-size-by-category',
                                    'pageSize' => 5,
                                    'category_id' => $category->id]) ?></li>
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
