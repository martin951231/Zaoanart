<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\BorderMaterial */

$this->title = '添加';
$this->params['breadcrumbs'][] = ['label' => 'Border Materials', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="border-material-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
