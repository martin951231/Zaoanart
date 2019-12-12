<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Addressinfo */

$this->title = 'Update Addressinfo: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Addressinfos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="addressinfo-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
