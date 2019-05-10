<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\ShopcarSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '购物车订单';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shopcar-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php //Html::a('Create Shopcar', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

            'id',
//            'goods_id',
//            'user_id',
//            'color',
//            'img_name',
            //图片
            [
                'attribute'=>'img_name',
                'format' => ['image',['width'=>88]],
                'content' => function($model){
                $url = 'http://118.178.89.229/resource/preview_img/';
//                $url = 'http://localhost/yii-application/backend/web/preview_img/';
                    return '<div style="width:500px;height:500px;position: absolute;margin-left: 88px;margin-top: -206px;display:none" id="imgshow'.$model->id.'"><img src="'.$url.$model->img_name.'" height="500px"></div><img style="max-width:88px;max-height:88px" src="'.$url.$model->img_name.'" onclick=find_img('.$model->id.')>';
                },
            ],
            'img_width',
            'img_height',
            'box_width',
            'box_height',
//            'decoration_status',
//            'core_material',
//            'drawing_core_val',
//            'core_offset',
//            'core_offset_direction',
//            'core_shift_val',
//            'core_shift_direction',
//            'core_price',
//            'decoration_price',
            'total_price',
            'status',
//            'created_at',
//            'box_name',
//            'excel_name',
//推荐
            [
                'attribute' => 'excel_name',
                'format' => 'text',
                'content' => function($model){
                $excel_name = '"'.$model->excel_name.'"';
                    return "<button id='is_recommend".$model['id']."' style='cursor:pointer'  class='is_recommend btn btn-info ' date_id='".$model['id']."'  onclick='to_excel(".$model->id.",".$excel_name.")' data-toggle='tooltip' data-placement='bottom' title='点击导出excel表格'>导出</button>";
                },
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
<script type="text/javascript">
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    })
    function to_excel(id,excel_name){
        if(!excel_name){
            alert('暂未生成订单')
        }else{
            var url = 'http://www.zaoanart.com/resource/excel/'
            //var url = 'http://localhost/yii-application/backend/web/excel/'
            window.open(url+excel_name)
        }
    }
    //后台查看图片
    function find_img(img){
        $("#imgshow"+img).animate({width:'toggle'})
    }




</script>
