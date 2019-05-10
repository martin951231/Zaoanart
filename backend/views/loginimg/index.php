<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\models\Loginimg;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\LoginimgSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Loginimgs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="loginimg-index">
    <!-- 模态框按钮 -->
    <button type="button" class="btn btn-warning" data-toggle="modal" onclick="modal_show()">批量添加</button>
    <!-- 模态框 -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">批量添加</h4>
                </div>
                <div class="modal-body">
                    <?php if(Yii::$app->session->hasFlash('success')):?>
                        <div class="alert alert-danger">
                            <?=Yii::$app->session->getFlash('success')?>
                        </div>
                    <?php endif ?>
                    <?php $form=ActiveForm::begin([
                        'id'=>'upload',
                        'enableAjaxValidation' => false,
                        'options'=>['enctype'=>'multipart/form-data']
                    ]);
                    ?>
                    <!--                    <button type="button" class="btn btn-primary" id="good_image_submit" onclick="uploads()">提交</button>-->
                    <button type="button" class="btn btn-primary" id="good_image_submit" onclick="UpladFile()">提交</button>
                    <?= $form->field(new Loginimg, 'login_img[]')->fileInput(['multiple' => true]);?>
                    <?php ActiveForm::end(); ?>
                    <div id="success_info"></div>
                    <div id="create_info" width="100%" height="100%"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="close_id" class="btn btn-default" data-dismiss="modal" style="">关闭</button>
                    <!--                    <button type="button" class="btn btn-primary" id="modal_submit" onclick="addimg_info()">保存</button>-->
                </div>
            </div>
        </div>
    </div>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'id',
//            'login_img',
            [
                'attribute'=>'login_img',
                'format' => ['login_img',['width'=>88]],
                'content' => function($model){
                    return '<a target="_blank" style="display: block;width:88px;height:88px" href="http://qiniu.zaoanart.com/'.$model->login_img.'"><img src="http://qiniu.zaoanart.com/'.$model->login_img.'" height="88"></a>';
                },
            ],
            'img_name',
            'created_at',
            'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
<script type="text/javascript">

    function modal_show(){
        $("#myModal").modal('show')
        var el = document.getElementById('create_info');
        var childs = el.childNodes;
        for(var k = childs .length - 1; k >= 0; k--) {
            el.removeChild(childs[k]);
        }
        $("#goods-image").val('');
    }
    //文件上传
    function UpladFile(){
        $("#close_id").css("display",'none')
        var el = document.getElementById('create_info');
        var childs = el.childNodes;
        for(var k = childs .length - 1; k >= 0; k--) {
            el.removeChild(childs[k]);
        }
        var objFile = document.getElementById("loginimg-login_img");
        html1 = "";
        html1 += '<div id="progress" style="text-align: center;height:60px;border-radius: 50px;">';
        html1 += '<div id="progress_num" style="text-align: left;height:20px;border-radius: 50px;">1</div>';
        html1 += '<div id="progress_img" style="text-align: center;overflow:hidden;height:20px;border-radius: 50px;"></div>';
        html1 += '<div id="progressa" style="width:0px;height:20px;background-color:#13ff13;border-radius: 50px;">0%</div>';
        html1 += '</div>'
        var arr = new Array();
        num = new Array();
        var res = new Array();
        var res_info = new Array();
        for(var i=0;i<objFile.files.length;i++){
            arr[i] = objFile.files[i]['name'];
        }
        var data = {
            'img' : arr
        }
        $.ajax({
            url:"/loginimg/gettoken",
            type:"post",
            data:data,
            dataType: "json",
            success:function(msg1){
                for(var i=0; i<msg1.length; i++){
                    var node1 = document.getElementById("create_info");
                    var node2 = document.createElement('div');
                    node2.setAttribute("id",'create_info'+msg1[i]['data_id']);
                    node1.appendChild(node2);
                    $("#create_info"+i).html(html1);
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
                                $("#create_info"+i+" #progress #progressa").css("width",percentComplete+'%');
                                $("#create_info"+i+" #progress #progressa").text(percentComplete+'%');
                                $("#create_info"+i+" #progress #progress_img").text(file['name']);
                                $("#create_info"+i+" #progress #progress_num").text(i+1);
                                // console && console.log(percentComplete, ",", formatSpeed);
                            }
                        }, true);
                        xhr.onreadystatechange = function(response) {
                            if (xhr.readyState == 4 && xhr.status == 200 && xhr.responseText != "") {
                                var blkRet = JSON.parse(xhr.responseText);
                                var img_name = blkRet.img_name;
                                var new_name = blkRet.new_name;
                                var img_width = blkRet.img_width;
                                var img_height = blkRet.img_height;
                                var img_url = 'http://qiniu.zaoanart.com/'+new_name+'?imageAve';
                                var data2 = {
                                    'img_name' : img_name,
                                    'login_img' : new_name
                                }
                                //添加数据库
                                $.ajax({
                                    url:'/loginimg/addimg',
                                    type:"post",
                                    data:data2,
                                    dataType: 'json',
                                    success:function(msg){
                                        var success_dom = document.getElementById('create_info'+i);
                                        success_dom.remove();
                                        res.push(i)
                                        if(res.length == msg1.length){
                                            $("#close_id").css("display",'')
                                            $("#myModal").modal('hide')
                                        }
                                    },
                                    error:function(msg){
                                        console.log(msg);
                                    }
                                });
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
