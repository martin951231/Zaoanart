<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Shopcar */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="shopcar-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'goods_id')->textInput() ?>

    <?= $form->field($model, 'user_id')->textInput() ?>

    <?= $form->field($model, 'color')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'img_width')->textInput() ?>

    <?= $form->field($model, 'img_height')->textInput() ?>

    <?= $form->field($model, 'box_width')->textInput() ?>

    <?= $form->field($model, 'box_height')->textInput() ?>

    <?= $form->field($model, 'decoration_status')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'core_material')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'drawing_core_val')->textInput() ?>

    <?= $form->field($model, 'core_offset')->textInput() ?>

    <?= $form->field($model, 'core_offset_direction')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'core_shift_val')->textInput() ?>

    <?= $form->field($model, 'core_shift_direction')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'core_price')->textInput() ?>

    <?= $form->field($model, 'decoration_price')->textInput() ?>

    <?= $form->field($model, 'total_price')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'box_name')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
