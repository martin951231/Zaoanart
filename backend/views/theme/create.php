<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Theme */

$this->title = '添加商品主题';
$this->params['breadcrumbs'][] = ['label' => '商品主题', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="theme-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
