<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Label */

$this->title = '更新标签: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => '标签', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '更新';
?>
<div class="label-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
