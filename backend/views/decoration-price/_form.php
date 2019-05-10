<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\DecorationPrice */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="decoration-price-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'decoration_method')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'decoration_code')->textInput() ?>

    <?= $form->field($model, 'price')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
