<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\MetSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '材质';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="met-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('添加新材质', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
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
//            'margin_price',
            //留白价格
            [
                'attribute'=>'margin_price',
                'format' => 'text',
                'content'=>function($model){
                    if(!$model->margin_price){$model->margin_price = "未设置";};
                    if($model->margin_price == "未设置"){
                        return "<span id='upmargin_price".$model['id']."' title='双击修改' style='cursor:pointer;color:red' ondblclick='update_margin_price(".$model->id.")' date_id='".$model['id']."' data-toggle='tooltip' data-placement='bottom' >".$model->margin_price."</span>";
                    }else{
                        return "<span id='upmargin_price".$model['id']."' title='双击修改' style='cursor:pointer;color:black' ondblclick='update_margin_price(".$model->id.")' date_id='".$model['id']."' data-toggle='tooltip' data-placement='bottom' >".$model->margin_price."</span>";
                    }
                },
            ],
            'created_at',
            'updated_at',

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
        Newobj.setAttribute("oninput","value=value.replace(/[^\\d]/g,'')");
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
                    url:"/met/upprice",
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

    //修改留白价格
    function update_margin_price(id){
        $('[data-toggle="tooltip"]').tooltip('destroy');
        var obj = document.getElementById("upmargin_price"+id);
        var Newobj = document.createElement('input');
        Newobj.value=obj.innerText;
        Newobj.setAttribute("type","input");
        Newobj.setAttribute("id","upmargin_price"+id);
        Newobj.setAttribute("name",obj.innerText);
        Newobj.setAttribute("value",obj.innerText);
        Newobj.setAttribute("oninput","value=value.replace(/[^\\d]/g,'')");
        Newobj.style = 'max-width:150px';
        obj.parentNode.appendChild(Newobj);
        obj.parentNode.removeChild(obj);
        addEventListener("keypress", function (e) {
            var date = {
                'id': id,
                'margin_price': Newobj.value,
            };
            if(e.keyCode == 13){
                $.ajax({
                    url:"/met/upmargin_price",
                    type:"post",
                    data: date,
                    dataType: 'json',
                    success:function(msg){
                        var txt = document.getElementById("upmargin_price"+id);
                        var Newtext = document.createElement('span');
                        Newtext.setAttribute("type","span");
                        Newtext.setAttribute("id",'upmargin_price'+msg.id);
                        Newtext.setAttribute("value",Newobj.value);
                        Newtext.setAttribute('data-toggle','tooltip');
                        Newtext.setAttribute('data-placement','bottom');
                        Newtext.setAttribute("ondblclick", "update_margin_price("+msg.id+")");
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
