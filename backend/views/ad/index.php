<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use backend\models\Ad;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\Ad */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '广告';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ad-index">
    <style>
        .info_style{
            border: ridge;
            background-color: antiquewhite;
        }
    </style>
    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
    <!-- <?//= //Html::a('添加新广告', ['create'], ['class' => 'btn btn-success']) ?> -->
    </p>
    <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#myModal">添加新广告</button>
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">添加新广告</h4>
                </div>
                <div class="modal-body info_style">
                    <div id="info">
                        <from id="submit_info" name="info" method="post" enctype="multipart/form-data">
                            <span><?php $form=ActiveForm::begin([
                                    'id'=>'upload1',
                                    'enableAjaxValidation' => false,
                                    'options'=>['enctype'=>'multipart/form-data']
                                ]);
                                ?>
                                <?= $form->field(new Ad, 'image[]')->fileInput(['multiple' => true]);?>
                                <?php ActiveForm::end(); ?>
                            </span>
                            <span>广告标题:<input id="info_title" name="info_title" type="text" class="form-control select_input modal_style"></span>
                            <span>商品ID:<input id="info_num" name="info_num" type="text" class="form-control select_input modal_style"></span>
                            <span>显示状态:<select name="xialakuang" id="info_list" class="form-control select_input modal_style">
                                <option value="1">显示</option>
                                <option value="0">隐藏</option>
                            </select></span>
                        </from>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    <button type="button" class="btn btn-primary" onclick="create_ad()">保存</button>
                </div>
            </div>
        </div>
    </div>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute'=>'id',
                'headerOptions' => ['width' => '20'],
                'content' => function($model){
                    return $model->id;
                },
            ],
            'image',
            [
                'attribute'=>'img_name',
//                'headerOptions' => ['width' => '400'],
                'content' => function($model){
                    return '<a target="_blank" style="display: block;width:88px;height:44px" href="http://qiniu.zaoanart.com/'.$model->img_name.'"><img src="http://qiniu.zaoanart.com/'.$model->img_name.'" height="44"></a>';
                },
            ],

            'title:ntext',
            [
                'attribute'=>'goods_id',
                'headerOptions' => ['width' => '20'],
                'content' => function($model){
                    return $model->goods_id;
                },
            ],
//            'link',
//            [
//                'attribute' => 'is_appear',
//                'format' => 'text',
//                'content' => function($this){
//                    if($this['is_appear'] == 1){
//                        return '显示';
//                    }else{
//                        return '不显示';
//                    }
//                }
//            ],
            [
                'attribute' => 'is_appear',
                'format' => 'text',
                'headerOptions' => ['width' => '20'],
                'content' => function($model){
                    if($model['is_appear'] == 0){
                        return "<button id='".$model['id']."' style='cursor:pointer'  class='status btn btn-inverse' date_id='".$model['id']."' status='".$model['is_appear']."'>隐藏</button>";
                    }else{
                        return "<button id='".$model['id']."' style='cursor:pointer' class='status btn btn-success' date_id='".$model['id']."' status='".$model['is_appear']."'>显示</button>";
                    }
                },
            ],
            // 'created_at',
            // 'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
<script type="text/javascript">
    $('.status').click(function(){
        var date = {
            'id': $(this).attr("date_id"),
            'is_appear': $(this).attr("status")
        };
        $.ajax({
            url:"/ad/status",
            type:"post",
            data: date,
            success:function(msg){
                var num = parseInt(msg);
                var span = document.getElementById(num);
                var status = span.getAttribute('status');
                if(status == 0){
                    span.setAttribute('status','1');
                    span.setAttribute('class','btn btn-success');
                    span.innerText = '显示';
                }else{
                    span.setAttribute('status','0');
                    span.setAttribute('class','btn btn-inverse');
                    span.innerText = '隐藏';
                }
            },
            error:function(data){
                alert('修改失败');
                location.reload();
            }
        });
    });
    function create_ad()
    {
        var submit_form = new FormData($("#upload1")['0']);
        var objFile = document.getElementById("ad-image");
        var arr = new Array();
        var res = new Array();
        arr[0] = objFile.files[0]['name']
        var data = {
            'img' : arr
        }
        $.ajax({
            url:"/ad/gettoken",
            type:"post",
            data:data,
            dataType: "json",
            success:function(msg1){
                for(var i=0; i<msg1.length; i++){
                    var token = msg1[i]['uptoken'];
                    var file = objFile.files[msg1[i]['data_id']];
                    var key = msg1[i]['new_name'];
                    var up_url = 'https://upload-z2.qiniup.com';
                    var Qiniu_upload = function(file, token, key,i) {
                        var xhr = new XMLHttpRequest();
                        xhr.open('POST', up_url, true);
                        var formData, startDate;
                        formData = new FormData();
                        if (key !== null && key !== undefined) formData.append('key', key);
                        formData.append('token', token);
                        formData.append('file', file);
                        var taking;
                        xhr.upload.addEventListener("progress", function(evt) {
                            if (evt.lengthComputable) {
                                var nowDate = new Date().getTime();
                                taking = nowDate - startDate;
                                var x = (evt.loaded) / 1024;
                                var y = taking / 1000;
                                var uploadSpeed = (x / y);
                                var formatSpeed;
                                if (uploadSpeed > 1024) {
                                    formatSpeed = (uploadSpeed / 1024).toFixed(2) + "Mb\/s";
                                } else {
                                    formatSpeed = uploadSpeed.toFixed(2) + "Kb\/s";
                                }
                                var percentComplete = Math.round(evt.loaded * 100 / evt.total);
                            }
                        }, true);
                        xhr.onreadystatechange = function(response) {
                            if (xhr.readyState == 4 && xhr.status == 200 && xhr.responseText != "") {
                                var blkRet = JSON.parse(xhr.responseText);
                                res.push(i)
                                if(res.length == msg1.length){
                                    var info_title = $("#info_title").val();
                                    var info_num = $("#info_num").val();
                                    var info_list = $("#info_list").val();
                                    var data = {
                                        'img_name': blkRet.img_name,
                                        'new_name': blkRet.new_name,
                                        'info_title':info_title,
                                        'info_num':info_num,
                                        'info_list':info_list,
                                    }
                                    //添加数据库
                                    $.ajax({
                                        url:"/ad/create_info",
                                        type:"post",
                                        data: data,
                                        success:function(res){
                                            alert(res);
                                            //location.reload();
                                        },
                                        error:function(){
                                            alert('添加失败');
                                            // location.reload();
                                        }
                                    });
                                }
                            } else if (xhr.status != 200 && xhr.responseText) {
                                alert('上传失败,请重试');
                            }
                        };
                        startDate = new Date().getTime();
                        // $("#progressbar").show();
                        xhr.send(formData);
                    };
                    if (file && token != "") {
                        Qiniu_upload(file, token, key,i);
                    } else {
                        console && console.log("form input error");
                    }
                }
            },
            error:function(msg){

            }
        });
    }
</script>
