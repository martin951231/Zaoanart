<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Goods */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Goods', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="goods-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('更新', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
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
            'name',
            'time',
            'category',
            'theme',
            'image',
            'author',
            'type',
            'length',
            'width',
            'max_length',
            'max_width',
            'min_length',
            'min_width',
            'shape',
            'title',
            'price',
            'premium',
            'content:ntext',
            'color',
            'link',
            'introduction',
            'review',
            'is_appear',
            'is_recommend',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
