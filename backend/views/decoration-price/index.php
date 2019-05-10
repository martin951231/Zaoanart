<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\DecorationPriceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '装裱方式价格';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="decoration-price-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('添加', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'decoration_method',
            'decoration_code',
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
            //浮动比例
            [
                'attribute'=>'float_scale',
                'format' => 'text',
                'content'=>function($model){
                    if(!$model->float_scale){$model->float_scale = "未设置";};
                    if($model->float_scale == "未设置"){
                        return "<span id='upfloat_scale".$model['id']."' title='双击修改' style='cursor:pointer;color:red' ondblclick='update_float_scale(".$model->id.")' date_id='".$model['id']."' data-toggle='tooltip' data-placement='bottom' >".$model->float_scale."</span>";
                    }else{
                        return "<span id='upfloat_scale".$model['id']."' title='双击修改' style='cursor:pointer;color:black' ondblclick='update_float_scale(".$model->id.")' date_id='".$model['id']."' data-toggle='tooltip' data-placement='bottom' >".$model->float_scale."</span>";
                    }
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
                    url:"/decoration-price/upprice",
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

    //修改浮动比例
    function update_float_scale(id){
        $('[data-toggle="tooltip"]').tooltip('destroy');
        var obj = document.getElementById("upfloat_scale"+id);
        var Newobj = document.createElement('input');
        Newobj.value=obj.innerText;
        Newobj.setAttribute("type","input");
        Newobj.setAttribute("id","upfloat_scale"+id);
        Newobj.setAttribute("name",obj.innerText);
        Newobj.setAttribute("value",obj.innerText);
        Newobj.setAttribute("oninput","value=value.replace(/[^\\d.]/g,'')");
        Newobj.style = 'max-width:150px';
        obj.parentNode.appendChild(Newobj);
        obj.parentNode.removeChild(obj);
        addEventListener("keypress", function (e) {
            var date = {
                'id': id,
                'float_scale': Newobj.value,
            };
            if(e.keyCode == 13){
                $.ajax({
                    url:"/decoration-price/upfloat_scale",
                    type:"post",
                    data: date,
                    dataType: 'json',
                    success:function(msg){
                        var txt = document.getElementById("upfloat_scale"+id);
                        var Newtext = document.createElement('span');
                        Newtext.setAttribute("type","span");
                        Newtext.setAttribute("id",'upfloat_scale'+msg.id);
                        Newtext.setAttribute("value",Newobj.value);
                        Newtext.setAttribute('data-toggle','tooltip');
                        Newtext.setAttribute('data-placement','bottom');
                        Newtext.setAttribute("ondblclick", "update_float_scale("+msg.id+")");
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

</script>