<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Met */

$this->title = '更新材质: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '材质', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '更新';
?>
<div class="met-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
