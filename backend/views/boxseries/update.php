<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Boxseries */

$this->title = '修改: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Boxseries', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '修改';
?>
<div class="boxseries-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
