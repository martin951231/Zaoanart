<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Theme */

$this->title = '更新商品主题: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => '商品主题', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '更新';
?>
<div class="theme-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
