<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Loginimg */

//$this->title = 'Create Loginimg';
$this->params['breadcrumbs'][] = ['label' => 'Loginimgs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="loginimg-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
