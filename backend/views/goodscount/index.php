<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\data\ActiveDataProvide;
use backend\models\Category;
use backend\models\Label;
use backend\models\Goods;
use backend\models\Goodscount;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use common\models\SmallImage;
use yii\imagine\Image;
use backend\controllers\GoodsController;
use backend\controllers\GoodscountController;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use yii\widgets\LinkPager;
use yii\data\Pagination;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\GoodsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
//echo phpinfo();
$this->title = '商品';
$this->params['breadcrumbs'][] = $this->title;
?>
<!--<div style="width:500px;height=500px;background-color:666666">12</div>-->
<div class="goods-index">
    <style>
        ul{
            padding: 0px;
        }
        ul li{
            list-style: none;
        }
        .ul_li{
            display: inline-block;
            width: 25px;
            height: 15px;
            font-size: 10px;
            cursor:pointer;
        }
        #upcategory1{
            display: block;
        }
        .text_style{
            width:100px;
            height:100px;
            white-space:normal;
            word-break: break-all;
            overflow: auto;
        }
        .select_input{
            width:100px;
            display:inline;
        }
        .checkbox_size{
            zoom:300%
        }
        .modal_style{
            margin-top:5px;
            margin-left:5px
        }
        #img1{
            border: ridge;
            background-color: antiquewhite;
        }
        .label_style{
            margin-left: 10px;
            margin-top: 10px;
        }
        a.asc:after {
            font-family: 'FontAwesome';
            content: "\f160";
        }
        a.desc:after {
            font-family: 'FontAwesome';
            content: "\f161";
        }
    </style>
    <h1><?php //Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <p>
        <link href="https://cdn.bootcss.com/toastr.js/2.1.4/toastr.min.css" rel="stylesheet">
        <script src="https://cdn.bootcss.com/toastr.js/2.1.4/toastr.min.js"></script>
        <a href="https://mp.weixin.qq.com/wxamp/statistics/visit/behavior?token=2096498381&lang=zh_CN" target="_blank">小程序数据分析</a>
        <a href="https://das.base.shuju.aliyun.com/token3rd/dashboard/view/pc.htm?pageId=665a197a-d0cf-4478-af4e-e6ef07f0631c&accessToken=84e2eec99f69774983289dd4173d7070" target="_blank">PC数据分析</a>
        <a href="http://das.base.shuju.aliyun.com/dashboard/pc.htm?workspaceId=b33b4b2a-a3e7-46dd-8c69-5d48cfc1765e&pageId=6093ddb6-31b2-4996-ab67-04f27968c82c" target="_blank">PC用户分析</a>
<!--    每页显示数量-->
    <div>
        <input id="pagesize" type="text" placeholder="每页显示数量" style="width:100px">
        <button type="button" class="btn btn-primary" onclick="up_pagesize(<?= Yii::$app->user->identity->id; ?>)">确认</button>
    </div>
<!--    查询图片所在页-->
    <div style="margin-top:5px">
        <input id="img_page" type="text" placeholder="跳转图片所在页" style="width:100px">
        <button type="button" class="btn btn-primary" onclick="img_page()">搜索</button>
    </div>

    <?= LinkPager::widget([
        'pagination' => $page,
        'firstPageLabel' => '首页',
        'prevPageLabel' => '上一页',
        'nextPageLabel' => '下一页',
        'lastPageLabel' => '尾页',
    ]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
         'pager'=>[
        //'options'=>['class'=>'hidden']//关闭分页
        'firstPageLabel'=>"首页",
        'prevPageLabel'=>'上一页',
        'nextPageLabel'=>'下一页',
        'lastPageLabel'=>'尾页',
        ],
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],
//            [
//                'class' => 'yii\grid\CheckboxColumn',
//                'checkboxOptions'=>function($model){
//                    return [
//                        'id' => $model->id ,
//                        'class' => 'checkbox_size',
//                    ];
//                },
//                'contentOptions' => [
//                    'width'=>'50'
//                ],
//            ],
            [
                'attribute'=>'id',
                'contentOptions' => [
                    'width'=>'100'
                ],
            ],
            //名称
            [
                'attribute'=>'name',
                'format' => 'text',
                'contentOptions' => [
                    'width'=>'400'
                ],
                'content'=>function($model){
                    return "<span style='cursor:pointer;color:#000;width:200px;display: inline-block;white-space:pre-wrap;word-break: break-all;' >".$model['name']."</span>";
                },
            ],
            //图片
            [
                'attribute'=>'image',
                'format' => ['image',['width'=>88]],
                'contentOptions' => [
                    'width'=>'200'
                ],
                'content' => function($model){
                    return '<div style="width:500px;height:500px;position: absolute;margin-left: 88px;margin-top: -206px;display:none" id="imgshow'.$model->id.'"><img src="http://qiniu.zaoanart.com/'.$model->image.'" height="500px"></div><img src="http://qiniu.zaoanart.com/'.$model->image.'?imageView2/1/w/88/h/88" onclick=find_img('.$model->id.')>';
                },
            ],
            //PC访问次数
            [
                'label'=>'PC访问次数',
                'attribute'=>'search_sum',
                'format' => 'text',
                'contentOptions' => [
                    'width'=>'200'
                ],
                'content'=>function($model){
                    return "<span style='cursor:pointer;color:#000;width:200px;display: inline-block;white-space:pre-wrap;word-break: break-all;' >".$model['search_sum']." 次</span>";
                },
            ],
            //小程序访问次数
            [
                'label'=>'小程序访问次数',
                'attribute'=>'search_sum_wechat',
                'format' => 'text',
                'contentOptions' => [
                    'width'=>'200'
                ],
                'content'=>function($model){
                    return "<span style='cursor:pointer;color:#000;width:200px;display: inline-block;white-space:pre-wrap;word-break: break-all;' >".$model['search_sum_wechat']." 次</span>";
                },
            ],
            //PC装裱次数
            [
                'label'=>'PC装裱次数',
                'attribute'=>'decoration_sum',
                'format' => 'text',
                'contentOptions' => [
                    'width'=>'200'
                ],
                'content'=>function($model){
                    return "<span style='cursor:pointer;color:#000;width:200px;display: inline-block;white-space:pre-wrap;word-break: break-all;' >".$model['decoration_sum']." 次</span>";
                },
            ],
            //小程序装裱次数
            [
                'label'=>'小程序装裱次数',
                'attribute'=>'decoration_sum_wechat',
                'format' => 'text',
                'contentOptions' => [
                    'width'=>'200'
                ],
                'content'=>function($model){
                    return "<span style='cursor:pointer;color:#000;width:200px;display: inline-block;white-space:pre-wrap;word-break: break-all;' >".$model['decoration_sum_wechat']." 次</span>";
                },
            ],
            //PC收藏次数
            [
                'label'=>'PC收藏次数',
                'attribute'=>'keep_sum',
                'format' => 'text',
                'contentOptions' => [
                    'width'=>'200'
                ],
                'content'=>function($model){
                    return "<span style='cursor:pointer;color:#000;width:200px;display: inline-block;white-space:pre-wrap;word-break: break-all;' >".$model['keep_sum']." 次</span>";
                },
            ],
            //小程序收藏次数
            [
                'label'=>'小程序收藏次数',
                'attribute'=>'keep_sum_wechat',
                'format' => 'text',
                'contentOptions' => [
                    'width'=>'200'
                ],
                'content'=>function($model){
                    return "<span style='cursor:pointer;color:#000;width:200px;display: inline-block;white-space:pre-wrap;word-break: break-all;' >".$model['keep_sum_wechat']." 次</span>";
                },
            ],
//            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
<script src="https://unpkg.com/qiniu-js@2.2.2/dist/qiniu.min.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.9.1.js"></script>
<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<script type="text/javascript">
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    })
    //修改每页显示数量
    function up_pagesize(uid){
        if($("#pagesize").val() == ""){
            alert('请填写要显示的数量');
        }else{
            var data = {
                'uid' : uid,
                'pagesize' : $("#pagesize").val()
            }
            $.ajax({
                url:"/goods/up_pagesize",
                type:"post",
                data:data,
                dataType: 'json',
                success:function(msg){
                    if(msg == 1){
                        location.reload();
                    }else{
                        alert('失败');
                    }
                },
                error:function(msg){
                    console.log(msg);
                }
            });
        }
    }
    //查询图片所在页并跳转
    function img_page(){
        var id = $('#img_page').val()
        var data = {
            'id' : id
        }
        $.ajax({
            url:"/goodscount/find_img_page",
            type:"post",
            data:data,
            dataType: 'json',
            success:function(msg){
                if(msg ==1){
                    alert('暂无该图片')
                }else{
                    window.location.href=msg.url;
                }
            },
            error:function(msg){
                console.log(msg);
            }
        });
    }
    //后台查看图片
    function find_img(img){
        $("#imgshow"+img).animate({width:'toggle'})
    }
</script>












