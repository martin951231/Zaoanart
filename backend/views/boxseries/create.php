<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Boxseries */

$this->title = '添加边框系列';
$this->params['breadcrumbs'][] = ['label' => 'Boxseries', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="boxseries-create">


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
