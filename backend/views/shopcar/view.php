<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Shopcar */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Shopcars', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="shopcar-view">

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
            'goods_id',
            'user_id',
            'color',
            'img_name',
            'img_width',
            'img_height',
            'box_width',
            'box_height',
            'decoration_status',
            'core_material',
            'drawing_core_val',
            'core_offset',
            'core_offset_direction',
            'core_shift_val',
            'core_shift_direction',
            'core_price',
            'decoration_price',
            'total_price',
            'status',
            'created_at',
            'box_name',
            'excel_name',
        ],
    ]) ?>

</div>
