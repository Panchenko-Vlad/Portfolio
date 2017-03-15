<?php

use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $commentForm yii\base\Model */

/* @var $article array */
/* @var $popular array */
/* @var $recent array */
/* @var $categories array */
/* @var $comments array */

$this->title = $article->title;
?>
<!--main content start-->
<div class="main-content">
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <article class="post">
                    <div class="post-thumb">
                        <a href="<?= Url::toRoute(['site/view', 'id' => $article->id]) ?>"><img src="<?= $article->getImage() ?>" alt=""></a>
                    </div>
                    <div class="post-content">
                        <header class="entry-header text-center text-uppercase">
                            <h6><a href="<?= Url::toRoute(['site/category', 'id' => $article->category->id]) ?>"> <?= $article->category->title ?></a></h6>

                            <h1 class="entry-title">
                                <a href="<?= Url::toRoute(['site/view', 'id' => $article->id]) ?>">
                                    <?= $article->title ?>
                                </a>
                            </h1>


                        </header>
                        <div class="entry-content">
                            <p><?= $article->content ?></p>
                        </div>

                        <div class="social-share">
							<span class="social-share-title pull-left text-capitalize"><?= $article->getDate() ?></span>
                            <ul class="text-center pull-right">
                                <li><a class="s-facebook" href="#"><i class="fa fa-eye"></i></a></li><?= (int) $article->viewed ?>
                            </ul>
                        </div>
                    </div>
                </article>

                <?= $this->render('/partials/comments', [
                    'article' => $article,
                    'comments' => $comments,
                    'commentForm' => $commentForm
                ]) ?>
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
