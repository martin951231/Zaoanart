<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\search\GoodsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="goods-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'time') ?>

    <?= $form->field($model, 'category') ?>

    <?= $form->field($model, 'theme') ?>

    <?php // echo $form->field($model, 'image') ?>

    <?php // echo $form->field($model, 'author') ?>

    <?php // echo $form->field($model, 'type') ?>

    <?php // echo $form->field($model, 'length') ?>

    <?php // echo $form->field($model, 'width') ?>

    <?php // echo $form->field($model, 'max_length') ?>

    <?php // echo $form->field($model, 'max_width') ?>

    <?php // echo $form->field($model, 'min_length') ?>

    <?php // echo $form->field($model, 'min_width') ?>

    <?php // echo $form->field($model, 'shape') ?>

    <?php // echo $form->field($model, 'title') ?>

    <?php // echo $form->field($model, 'price') ?>

    <?php // echo $form->field($model, 'premium') ?>

    <?php // echo $form->field($model, 'content') ?>

    <?php // echo $form->field($model, 'color') ?>

    <?php // echo $form->field($model, 'link') ?>

    <?php // echo $form->field($model, 'introduction') ?>

    <?php // echo $form->field($model, 'review') ?>

    <?php // echo $form->field($model, 'is_appear') ?>

    <?php // echo $form->field($model, 'is_recommend') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
