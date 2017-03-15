<?php

use yii\widgets\ActiveForm;

/* @var $commentForm yii\base\Model */
/* @var $article array */
/* @var $comments array */

?>

<?php if (!empty($comments)): ?>
    <h4>Комментариев: <?= count($comments) ?></h4>

    <?php if (Yii::$app->session->getFlash('comment')): ?>
        <div class="alert alert-success" role="alert">
            <?= Yii::$app->session->getFlash('comment') ?>
        </div>
    <?php endif; ?>

    <?php foreach ($comments as $comment): ?>
        <div class="bottom-comment"><!--bottom comment-->

            <div class="comment-text">
                <h5><?= $comment->user->name ?></h5>

                <p class="comment-date"><?= $comment->getDate() ?></p>

                <p class="para"><?= $comment->text ?></p>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
<!-- end bottom comment-->

<div class="leave-comment"><!--leave comment-->
    <h4>Оставьте сообщение</h4>

    <?php $form = ActiveForm::begin([
        'action' => ['site/comment', 'id' => $article->id],
        'options' => ['class' => 'form-horizontal contact-form', 'role' => 'form']
    ]) ?>
    <div class="form-group">
        <div class="col-md-12">
            <?= $form->field($commentForm, 'comment')->textarea([
                'class' => 'form-control',
                'placeholder' => 'Ваше сообщение'
            ])->label(false) ?>
        </div>
    </div>
    <button type="submit" class="btn send-btn">Опубликовать</button>
    <?php ActiveForm::end() ?>
</div><!--end leave comment-->