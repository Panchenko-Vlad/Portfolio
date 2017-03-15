<?php

use yii\helpers\Url;

/* @var $popular array */
/* @var $recent array */
/* @var $categories array */

?>

<div class="col-md-4" data-sticky_column>
    <div class="primary-sidebar">

        <?php if (!empty($popular)): ?>
            <aside class="widget">
                <h3 class="widget-title text-uppercase text-center">Популярные новости</h3>

                <?php foreach ($popular as $article): ?>
                    <div class="popular-post">

                        <a href="<?= Url::toRoute(['site/view', 'id' => $article->id]) ?>" class="popular-img"><img src="<?= $article->getImage() ?>" alt="">
                            <div class="p-overlay"></div>
                        </a>

                        <div class="p-content">
                            <a href="<?= Url::toRoute(['site/view', 'id' => $article->id]) ?>" class="text-uppercase"><?= $article->title ?></a>
                            <span class="p-date"><?= $article->getDate() ?></span>

                        </div>
                    </div>
                <?php endforeach; ?>
            </aside>
        <?php endif; ?>

        <?php if (!empty($recent)): ?>
            <aside class="widget pos-padding">
                <h3 class="widget-title text-uppercase text-center">Последние новости</h3>

                <?php foreach ($recent as $article): ?>
                    <div class="thumb-latest-posts">
                        <div class="media">
                            <div class="media-left">
                                <a href="<?= Url::toRoute(['site/view', 'id' => $article->id]) ?>" class="popular-img"><img src="<?= $article->getImage() ?>" alt="">
                                    <div class="p-overlay"></div>
                                </a>
                            </div>
                            <div class="p-content">
                                <a href="<?= Url::toRoute(['site/view', 'id' => $article->id]) ?>" class="text-uppercase"><?= $article->title ?></a>
                                <span class="p-date"><?= $article->getDate() ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </aside>
        <?php endif; ?>

        <?php if (!empty($categories)): ?>
            <aside class="widget border pos-padding">
                <h3 class="widget-title text-uppercase text-center">Категории</h3>
                <ul>
                    <?php foreach ($categories as $category): ?>
                        <li>
                            <a href="<?= Url::toRoute(['site/category', 'id' => $category->id]) ?>"><?= $category->title ?></a>
                            <span class="post-count pull-right"> (<?= $category->getArticlesCount() ?>)</span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </aside>
        <?php endif; ?>

    </div>
</div>
