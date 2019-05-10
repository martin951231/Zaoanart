<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Boxseries */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Boxseries', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="boxseries-view">

    <p>
        <?= Html::a('添加', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('修改', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('删除', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '你确定要删除吗?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'series_name',
            'created_at',
            'update_at',
        ],
    ]) ?>

</div>
