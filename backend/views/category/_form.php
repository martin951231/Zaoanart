 <?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Category */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="category-form">



    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'pid')->textInput() ?>
    <?= $form->field($model,'pid')->dropDownList([
        'active'=>'Active',
        'inactive'=>'Inactive',
        'prompt'=>'Select Status'
    ])->label(false); ?>

    <?php //$form->field($model, 'category_name')->textInput(['maxlength' => true]) ?>

    <?php //$form->field($model, 'order_id')->textInput() ?>

    <?php //$form->field($model, 'created_at')->textInput() ?>

    <?php  //$form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '添加' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
