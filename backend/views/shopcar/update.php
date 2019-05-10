<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Shopcar */

$this->title = '更新: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Shopcars', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="shopcar-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
