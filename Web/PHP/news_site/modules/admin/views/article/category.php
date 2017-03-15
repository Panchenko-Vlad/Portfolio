<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Article */
/* @var $form yii\widgets\ActiveForm */

/* @var $selectedCategory int Выбранная категория текущей статьи */
/* @var $categories array Список всех существующих категорий */
?>

<div class="article-form">

    <?php $form = ActiveForm::begin(); ?>

    <label>
        <h5><strong>Категория</strong></h5>
        <?= Html::dropDownList('category', $selectedCategory, $categories, ['class' => 'form-control']) ?>
    </label>

    <?= Html::submitButton('Готово', ['class' => 'btn btn-success']) ?>

    <?php ActiveForm::end(); ?>

</div>
