<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Shopcar */

$this->title = '添加';
$this->params['breadcrumbs'][] = ['label' => 'Shopcars', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shopcar-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
