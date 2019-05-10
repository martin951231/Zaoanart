<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\DecorationPrice */

$this->title = 'Create Decoration Price';
$this->params['breadcrumbs'][] = ['label' => 'Decoration Prices', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="decoration-price-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
