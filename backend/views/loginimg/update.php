<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Loginimg */

$this->title = 'Update Loginimg: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Loginimgs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="loginimg-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
