<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\Search\AddressinfoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '地址管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="addressinfo-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'username',
            'tel',
            'address',
            //'created_at',
            //'updated_at',

        ],
    ]); ?>
</div>
