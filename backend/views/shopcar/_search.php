<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\search\ShopcarSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="shopcar-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'goods_id') ?>

    <?= $form->field($model, 'user_id') ?>

    <?= $form->field($model, 'color') ?>

    <?= $form->field($model, 'img_name') ?>

    <?php // echo $form->field($model, 'img_width') ?>

    <?php // echo $form->field($model, 'img_height') ?>

    <?php // echo $form->field($model, 'box_width') ?>

    <?php // echo $form->field($model, 'box_height') ?>

    <?php // echo $form->field($model, 'decoration_status') ?>

    <?php // echo $form->field($model, 'core_material') ?>

    <?php // echo $form->field($model, 'drawing_core_val') ?>

    <?php // echo $form->field($model, 'core_offset') ?>

    <?php // echo $form->field($model, 'core_offset_direction') ?>

    <?php // echo $form->field($model, 'core_shift_val') ?>

    <?php // echo $form->field($model, 'core_shift_direction') ?>

    <?php // echo $form->field($model, 'core_price') ?>

    <?php // echo $form->field($model, 'decoration_price') ?>

    <?php // echo $form->field($model, 'total_price') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'box_name') ?>

    <?php // echo $form->field($model, 'excel_name') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
