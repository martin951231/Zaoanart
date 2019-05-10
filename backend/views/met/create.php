<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Met */

$this->title = '添加材质';
$this->params['breadcrumbs'][] = ['label' => '材质', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="met-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
