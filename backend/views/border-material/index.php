<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\models\BorderMaterial;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use common\models\SmallImage;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use backend\models\Boxseries;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\BorderMaterialSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '边框管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="border-material-index">

    <h1><?php //Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php //Html::a('Create Border Material', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <!-- 模态框按钮 -->
    <button type="button" class="btn btn-warning" data-toggle="modal" onclick="modal_show()">A类框批量添加</button>
    <!-- 模态框 -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">A类框批量添加</h4>
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
                    <?= $form->field(new BorderMaterial, 'img_name[]')->fileInput(['id'=>'bordermaterial-img_name','multiple' => true]);?>
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
    <!-- 模态框按钮 -->
    <button type="button" class="btn btn-warning" data-toggle="modal" onclick="modal_show2()">B类框批量添加</button>
    <!-- 模态框 -->
    <div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">B类框批量添加</h4>
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
                    <button type="button" class="btn btn-primary" id="good_image_submit2" onclick="UpladFile2()">提交</button>
                    <?= $form->field(new BorderMaterial, 'img_name[]')->fileInput(['id'=>'bordermaterial-img_name2','multiple' => true]);?>
                    <?php ActiveForm::end(); ?>
                    <div id="success_info2"></div>
                    <div id="create_info2" width="100%" height="100%"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="close_id2" class="btn btn-default" data-dismiss="modal" style="">关闭</button>
                    <!--                    <button type="button" class="btn btn-primary" id="modal_submit" onclick="addimg_info()">保存</button>-->
                </div>
            </div>
        </div>
    </div>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],
            'id',
            //'img_name',
            //图片
            [
                'attribute'=>'img_name',
                'format' => ['img_name',['width'=>88]],
                'content' => function($model){
                    return '<a target="_blank" style="display: block;width:88px;height:88px" href="http://qiniu.zaoanart.com/'.$model->img_name.'"><img src="http://qiniu.zaoanart.com/'.$model->img_name.'" height="88"></a>';
                },
            ],
            //边框名
            [
                'attribute'=>'border_name',
                'format' => 'text',
                'content'=>function($model){
                    if(!$model->border_name){$model->border_name = "未设置";};
                    if($model->border_name == "未设置"){
                        return "<span id='upborder_name".$model['id']."' title='双击修改' style='cursor:pointer;color:red' ondblclick='update_border_name(".$model->id.")' date_id='".$model['id']."' data-toggle='tooltip' data-placement='bottom' >".$model->border_name."</span>";
                    }else{
                        return "<span id='upborder_name".$model['id']."' title='双击修改' style='cursor:pointer;color:black' ondblclick='update_border_name(".$model->id.")' date_id='".$model['id']."' data-toggle='tooltip' data-placement='bottom' >".$model->border_name."</span>";
                    }
                },
            ],
            //缩略图名
            'preview_img',
            //面宽
            [
                'attribute'=>'face_width',
                'format' => 'text',
                'content'=>function($model){
                    if(!$model->face_width){$model->face_width = "未设置";};
                    if($model->face_width == "未设置"){
                        return "<span id='upface_width".$model['id']."' title='双击修改' style='cursor:pointer;color:red' ondblclick='update_face_width(".$model->id.")' date_id='".$model['id']."' data-toggle='tooltip' data-placement='bottom' >".$model->face_width."</span>";
                    }else{
                        return "<span id='upface_width".$model['id']."' title='双击修改' style='cursor:pointer;color:black' ondblclick='update_face_width(".$model->id.")' date_id='".$model['id']."' data-toggle='tooltip' data-placement='bottom' >".$model->face_width."</span>";
                    }
                },
            ],
            //侧厚
            [
                'attribute'=>'Thickness',
                'format' => 'text',
                'content'=>function($model){
                    if(!$model->Thickness){$model->Thickness = "未设置";};
                    if($model->Thickness == "未设置"){
                        return "<span id='upThickness".$model['id']."' title='双击修改' style='cursor:pointer;color:red' ondblclick='update_Thickness(".$model->id.")' date_id='".$model['id']."' data-toggle='tooltip' data-placement='bottom' >".$model->Thickness."</span>";
                    }else{
                        return "<span id='upThickness".$model['id']."' title='双击修改' style='cursor:pointer;color:black' ondblclick='update_Thickness(".$model->id.")' date_id='".$model['id']."' data-toggle='tooltip' data-placement='bottom' >".$model->Thickness."</span>";
                    }
                },
            ],
            //价格
            [
                'attribute'=>'price',
                'format' => 'text',
                'content'=>function($model){
                    if(!$model->price){$model->price = "未设置";};
                    if($model->price == "未设置"){
                        return "<span id='upprice".$model['id']."' title='双击修改' style='cursor:pointer;color:red' ondblclick='update_price(".$model->id.")' date_id='".$model['id']."' data-toggle='tooltip' data-placement='bottom' >".$model->price."</span>";
                    }else{
                        return "<span id='upprice".$model['id']."' title='双击修改' style='cursor:pointer;color:black' ondblclick='update_price(".$model->id.")' date_id='".$model['id']."' data-toggle='tooltip' data-placement='bottom' >".$model->price."</span>";
                    }
                },
            ],
//            'series_id',
            //色系
            [
                'attribute'=>'sid',
                'format' => 'text',
                'content' => function($model){
                    $res = Boxseries::find()->select('series_name')->where(['id'=>$model->sid])->one();
                    $series_arr = Boxseries::find()->select('id,series_name')->all();
                    $html = '';
                    for($i=0;$i<count($series_arr);$i++){
                        $html .= '<li title="点击修改" data-toggle="tooltip" data-placement="bottom" onclick="upseries('.$series_arr[$i]['id'].','.$model->id.')" class="series_li">'.$series_arr[$i]['series_name'].'</li>';
                    }
					if($res){
						return '<div>
                                <span id="series_name'.$model->id.'">'.$res->series_name.'</span>
                                <ul class="series_ul">'.$html.'</ul>
                            </div>';
					}else{
						return '<div>
                                <span id="series_name'.$model->id.'">未设置</span>
                                <ul class="series_ul">'.$html.'</ul>
                            </div>';
					}
                    
                }
            ],
            'cate',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
<script type="text/javascript">
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    })
    function modal_show(){
        $("#myModal").modal('show')
        var el = document.getElementById('create_info');
        var childs = el.childNodes;
        for(var k = childs .length - 1; k >= 0; k--) {
            el.removeChild(childs[k]);
        }
        $("#bordermaterial-img_name").val('');
    }
    function modal_show2(){
        $("#myModal2").modal('show')
        var el = document.getElementById('create_info');
        var childs = el.childNodes;
        for(var k = childs .length - 1; k >= 0; k--) {
            el.removeChild(childs[k]);
        }
        $("#bordermaterial-img_name").val('');
    }
    //A类文件上传
    function UpladFile(){
        $("#close_id").css("display",'none')
        var el = document.getElementById('create_info');
        var childs = el.childNodes;
        for(var k = childs .length - 1; k >= 0; k--) {
            el.removeChild(childs[k]);
        }
        var objFile = document.getElementById("bordermaterial-img_name");
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
            url:"/border-material/gettoken",
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
                                    'border_name' : img_name,
                                    'img_name' : new_name
                                }
                                //添加数据库
                                $.ajax({
                                    url:'/border-material/addimg',
                                    type:"post",
                                    data:data2,
                                    dataType: 'json',
                                    success:function(msg){
                                        var success_dom = document.getElementById('create_info'+i);
                                        success_dom.remove();
                                        res.push(i)
                                        if(res.length == msg1.length){
                                            alert('添加成功,预览图已生成')
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
    //B类文件上传
    function UpladFile2(){
        $("#close_id2").css("display",'none')
        var el = document.getElementById('create_info2');
        var childs = el.childNodes;
        for(var k = childs .length - 1; k >= 0; k--) {
            el.removeChild(childs[k]);
        }
        var objFile = document.getElementById("bordermaterial-img_name2");
        html1 = "";
        html1 += '<div id="progress2" style="text-align: center;height:60px;border-radius: 50px;">';
        html1 += '<div id="progress_num2" style="text-align: left;height:20px;border-radius: 50px;">1</div>';
        html1 += '<div id="progress_img2" style="text-align: center;overflow:hidden;height:20px;border-radius: 50px;"></div>';
        html1 += '<div id="progressa2" style="width:0px;height:20px;background-color:#13ff13;border-radius: 50px;">0%</div>';
        html1 += '</div>'
        var arr = new Array();
        num = new Array();
        var res = new Array();
        var res_info = new Array();
        var img_name = new Array();
        var new_name = new Array();
        for(var i=0;i<objFile.files.length;i++){
            arr[i] = objFile.files[i]['name'];
        }
        var data = {
            'img' : arr
        }
        $.ajax({
            url:"/border-material/gettoken2",
            type:"post",
            data:data,
            dataType: "json",
            success:function(msg1){
                for(var i=0; i<msg1.length; i++){
                    var node1 = document.getElementById("create_info2");
                    var node2 = document.createElement('div');
                    node2.setAttribute("id",'create_info2'+msg1[i]['data_id']);
                    node1.appendChild(node2);
                    $("#create_info2"+i).html(html1);
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
                                $("#create_info2"+i+" #progress2 #progressa2").css("width",percentComplete+'%');
                                $("#create_info2"+i+" #progress2 #progressa2").text(percentComplete+'%');
                                $("#create_info2"+i+" #progress2 #progress_img2").text(file['name']);
                                $("#create_info2"+i+" #progress2 #progress_num2").text(i+1);
                                // console && console.log(percentComplete, ",", formatSpeed);
                            }
                        }, true);
                        xhr.onreadystatechange = function(response) {
                            if (xhr.readyState == 4 && xhr.status == 200 && xhr.responseText != "") {
                                var blkRet = JSON.parse(xhr.responseText);
                                img_name.push(blkRet.img_name)
                                new_name.push(blkRet.new_name)
                                var img_width = blkRet.img_width;
                                var img_height = blkRet.img_height;
                                var img_url = 'http://qiniu.zaoanart.com/'+new_name+'?imageAve';
                                var data2 = {
                                    'border_name' : img_name,
                                    'img_name' : new_name
                                }
                                res.push(i)
                                if(res.length == msg1.length){
                                    //添加数据库
                                    $.ajax({
                                        url:'/border-material/addimg2',
                                        type:"post",
                                        data:data2,
                                        dataType: 'json',
                                        success:function(msg){
                                            if(msg == 1){
                                                for(var r = childs .length - 1; r >= 0; r--) {
                                                    el.removeChild(childs[r]);
                                                }
                                                alert('添加成功,预览图已生成')
                                                $("#close_id2").css("display",'')
                                                $("#myModal").modal('hide')
                                            }else{
                                                alert('添加失败')
                                                $("#close_id2").css("display",'')
                                                $("#myModal").modal('hide')
                                            }

                                        },
                                        error:function(msg){
                                            console.log(msg);
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
    //修改边框名
    function update_border_name(id){
        $('[data-toggle="tooltip"]').tooltip('destroy');
        var obj = document.getElementById("upborder_name"+id);
        var Newobj = document.createElement('input');
        Newobj.value=obj.innerText;
        Newobj.setAttribute("type","input");
        Newobj.setAttribute("id","upborder_name"+id);
        Newobj.setAttribute("name",obj.innerText);
        Newobj.setAttribute("value",obj.innerText);
        Newobj.style = 'max-width:150px';
        obj.parentNode.appendChild(Newobj);
        obj.parentNode.removeChild(obj);
        addEventListener("keypress", function (e) {
            var date = {
                'id': id,
                'name': Newobj.value,
            };
            console.log()
            if(e.keyCode == 13){
                $.ajax({
                    url:"/border-material/upborder_name",
                    type:"post",
                    data: date,
                    dataType: 'json',
                    success:function(msg){
                        var txt = document.getElementById("upborder_name"+id);
                        var Newtext = document.createElement('span');
                        Newtext.setAttribute("type","span");
                        Newtext.setAttribute("id",'upborder_name'+msg.id);
                        Newtext.setAttribute("value",Newobj.value);
                        Newtext.setAttribute('data-toggle','tooltip');
                        Newtext.setAttribute('data-placement','bottom');
                        Newtext.setAttribute("ondblclick", "update_border_name("+msg.id+")");
                        Newtext.style = 'cursor:pointer';
                        Newtext.innerHTML = Newobj.value;
                        Newtext.style = 'max-width:150px';
                        Newtext.style = 'white-space: pre-line';
                        //Newtext.style.color = '#FF830F';
                        Newtext.title = '双击修改';
                        txt.parentNode.appendChild(Newtext);
                        txt.parentNode.removeChild(txt);
                        $('[data-toggle="tooltip"]').tooltip();
                    },
                    error:function(data){
                        alert('修改失败,边框名称不能为空');
                        location.reload();
                    }
                });
            }
        });
    }
    //修改面宽
    function update_face_width(id){
        $('[data-toggle="tooltip"]').tooltip('destroy');
        var obj = document.getElementById("upface_width"+id);
        var Newobj = document.createElement('input');
        Newobj.value=obj.innerText;
        Newobj.setAttribute("type","input");
        Newobj.setAttribute("id","upface_width"+id);
        Newobj.setAttribute("name",obj.innerText);
        Newobj.setAttribute("value",obj.innerText);
        Newobj.setAttribute("oninput","value=value.replace(/[^\\d.]/g,'')");
        Newobj.style = 'max-width:150px';
        obj.parentNode.appendChild(Newobj);
        obj.parentNode.removeChild(obj);
        addEventListener("keypress", function (e) {
            var date = {
                'id': id,
                'face_width': Newobj.value,
            };
            if(e.keyCode == 13){
                $.ajax({
                    url:"/border-material/upface_width",
                    type:"post",
                    data: date,
                    dataType: 'json',
                    success:function(msg){
                        var txt = document.getElementById("upface_width"+id);
                        var Newtext = document.createElement('span');
                        Newtext.setAttribute("type","span");
                        Newtext.setAttribute("id",'upface_width'+msg.id);
                        Newtext.setAttribute("value",Newobj.value);
                        Newtext.setAttribute('data-toggle','tooltip');
                        Newtext.setAttribute('data-placement','bottom');
                        Newtext.setAttribute("ondblclick", "update_face_width("+msg.id+")");
                        Newtext.style = 'cursor:pointer';
                        Newtext.innerHTML = Newobj.value;
                        Newtext.style = 'max-width:150px';
                        Newtext.style = 'white-space: pre-line';
                        Newtext.title = '双击修改';
                        txt.parentNode.appendChild(Newtext);
                        txt.parentNode.removeChild(txt);
                        $('[data-toggle="tooltip"]').tooltip();
                    },
                    error:function(data){
                        alert('修改失败,面宽不能为空');
                        location.reload();
                    }
                });
            }
        });
    }
    //修改侧厚
    function update_Thickness(id){
        $('[data-toggle="tooltip"]').tooltip('destroy');
        var obj = document.getElementById("upThickness"+id);
        var Newobj = document.createElement('input');
        Newobj.value=obj.innerText;
        Newobj.setAttribute("type","input");
        Newobj.setAttribute("id","upThickness"+id);
        Newobj.setAttribute("name",obj.innerText);
        Newobj.setAttribute("value",obj.innerText);
        Newobj.setAttribute("oninput","value=value.replace(/[^\\d.]/g,'')");
        Newobj.style = 'max-width:150px';
        obj.parentNode.appendChild(Newobj);
        obj.parentNode.removeChild(obj);
        addEventListener("keypress", function (e) {
            var date = {
                'id': id,
                'Thickness': Newobj.value,
            };
            if(e.keyCode == 13){
                $.ajax({
                    url:"/border-material/upthickness",
                    type:"post",
                    data: date,
                    dataType: 'json',
                    success:function(msg){
                        var txt = document.getElementById("upThickness"+id);
                        var Newtext = document.createElement('span');
                        Newtext.setAttribute("type","span");
                        Newtext.setAttribute("id",'upThickness'+msg.id);
                        Newtext.setAttribute("value",Newobj.value);
                        Newtext.setAttribute('data-toggle','tooltip');
                        Newtext.setAttribute('data-placement','bottom');
                        Newtext.setAttribute("ondblclick", "update_thickness("+msg.id+")");
                        Newtext.style = 'cursor:pointer';
                        Newtext.innerHTML = Newobj.value;
                        Newtext.style = 'max-width:150px';
                        Newtext.style = 'white-space: pre-line';
                        Newtext.title = '双击修改';
                        txt.parentNode.appendChild(Newtext);
                        txt.parentNode.removeChild(txt);
                        $('[data-toggle="tooltip"]').tooltip();
                    },
                    error:function(data){
                        alert('修改失败,侧厚不能为空');
                        location.reload();
                    }
                });
            }
        });
    }
    //修改价格
    function update_price(id){
        $('[data-toggle="tooltip"]').tooltip('destroy');
        var obj = document.getElementById("upprice"+id);
        var Newobj = document.createElement('input');
        Newobj.value=obj.innerText;
        Newobj.setAttribute("type","input");
        Newobj.setAttribute("id","upprice"+id);
        Newobj.setAttribute("name",obj.innerText);
        Newobj.setAttribute("value",obj.innerText);
        Newobj.setAttribute("oninput","value=value.replace(/[^\\d.]/g,'')");
        Newobj.style = 'max-width:150px';
        obj.parentNode.appendChild(Newobj);
        obj.parentNode.removeChild(obj);
        addEventListener("keypress", function (e) {
            var date = {
                'id': id,
                'price': Newobj.value,
            };
            if(e.keyCode == 13){
                $.ajax({
                    url:"/border-material/upprice",
                    type:"post",
                    data: date,
                    dataType: 'json',
                    success:function(msg){
                        var txt = document.getElementById("upprice"+id);
                        var Newtext = document.createElement('span');
                        Newtext.setAttribute("type","span");
                        Newtext.setAttribute("id",'upprice'+msg.id);
                        Newtext.setAttribute("value",Newobj.value);
                        Newtext.setAttribute('data-toggle','tooltip');
                        Newtext.setAttribute('data-placement','bottom');
                        Newtext.setAttribute("ondblclick", "update_price("+msg.id+")");
                        Newtext.style = 'cursor:pointer';
                        Newtext.innerHTML = Newobj.value;
                        Newtext.style = 'max-width:150px';
                        Newtext.style = 'white-space: pre-line';
                        Newtext.title = '双击修改';
                        txt.parentNode.appendChild(Newtext);
                        txt.parentNode.removeChild(txt);
                        $('[data-toggle="tooltip"]').tooltip();
                    },
                    error:function(data){
                        alert('修改失败,价格不能为空');
                        location.reload();
                    }
                });
            }
        });
    }
    //修改色系
    function upseries(sid,id){
        var data = {
            'id' : id,
            'sid' : sid
        }
        $.ajax({
            url:"/border-material/upseries",
            type:"post",
            data:data,
            dataType: 'json',
            success:function(msg){
                var id = msg.id
                $('#series_name'+id).text(msg.series_name)
            },
            error:function(data){
                alert('修改失败');
                location.reload();
            }
        });
    }
</script>
<style>
    ul,li{list-style:none;padding:0px;margin:0px}
    .series_ul{
        width:120px;
        white-space: pre-wrap;
    }
    .series_li{
        display: inline-block;
        font-size: 12px;
        width: 30px;
        border: solid 1px #ccc;
        border-radius: 5px;
        text-align: center;
        margin-right: 5px;
        margin-bottom: 5px;
        height: 20px;
        line-height: 20px;
        cursor: pointer;
        background-color: #9c9e9e47;
    }
    .series_li:hover{
        background-color: #e1eaea47;
    }


</style>