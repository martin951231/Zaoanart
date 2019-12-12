<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Addressinfo */

$this->title = 'Create Addressinfo';
$this->params['breadcrumbs'][] = ['label' => 'Addressinfos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="addressinfo-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
