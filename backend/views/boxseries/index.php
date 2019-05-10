<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\BoxseriesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '边框系列';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="boxseries-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('添加边框系列', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

            'id',
//            'cate_name',
            //边框名
            [
                'attribute'=>'series_name',
                'format' => 'text',
                'content'=>function($model){
                    if(!$model->series_name){$model->series_name = "未设置";};
                    if($model->series_name == "未设置"){
                        return "<span id='upseries_name".$model['id']."' title='双击修改' style='cursor:pointer;color:red' ondblclick='update_series_name(".$model->id.")' date_id='".$model['id']."' data-toggle='tooltip' data-placement='bottom' >".$model->series_name."</span>";
                    }else{
                        return "<span id='upseries_name".$model['id']."' title='双击修改' style='cursor:pointer;color:black' ondblclick='update_series_name(".$model->id.")' date_id='".$model['id']."' data-toggle='tooltip' data-placement='bottom' >".$model->series_name."</span>";
                    }
                },
            ],
            'created_at',
            'update_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>

<script type="text/javascript">
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    })

    //修改边框名
    function update_series_name(id){
        $('[data-toggle="tooltip"]').tooltip('destroy');
        var obj = document.getElementById("upseries_name"+id);
        var Newobj = document.createElement('input');
        Newobj.value=obj.innerText;
        Newobj.setAttribute("type","input");
        Newobj.setAttribute("id","upseries_name"+id);
        Newobj.setAttribute("name",obj.innerText);
        Newobj.setAttribute("value",obj.innerText);
        Newobj.style = 'max-width:150px';
        obj.parentNode.appendChild(Newobj);
        obj.parentNode.removeChild(obj);
        addEventListener("keypress", function (e) {
            var date = {
                'id': id,
                'series_name': Newobj.value,
            };
            if(e.keyCode == 13){
                $.ajax({
                    url:"/boxseries/upseries_name",
                    type:"post",
                    data: date,
                    dataType: 'json',
                    success:function(msg){
                        var txt = document.getElementById("upseries_name"+id);
                        var Newtext = document.createElement('span');
                        Newtext.setAttribute("type","span");
                        Newtext.setAttribute("id",'upseries_name'+msg.id);
                        Newtext.setAttribute("value",Newobj.value);
                        Newtext.setAttribute('data-toggle','tooltip');
                        Newtext.setAttribute('data-placement','bottom');
                        Newtext.setAttribute("ondblclick", "update_series_name("+msg.id+")");
                        Newtext.style = 'cursor:pointer';
                        Newtext.innerHTML = Newobj.value;
                        Newtext.style = 'max-width:150px';
                        // Newtext.style = 'white-space: pre-line';
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


</script>
