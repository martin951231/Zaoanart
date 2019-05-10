<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\KeepSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Keeps';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="keep-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php //Html::a('Create Keep', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <!-- Modal -->
    <div class="modal fade bs-example-modal-lg" id="keepModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">查看图片</h4>
                </div>
                <div class="modal-body" id="keep_img_modal">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                </div>
            </div>
        </div>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],
//
            'id',
//            'uid',
            [
                'attribute' => 'uid',
                'format' => 'text',
                'content' => function($model){
                    return $model->account->username;
                },
            ],
            'created_at',
            'updated_at',
            'keep_name',
            //查看图片
            [
                'attribute'=>'id',
                'format' => 'text',
                'content'=>function($model){
                    return "<span id='keep".$model['id']."' data_id='".$model->id."' style='left:40%;cursor:pointe;' class='btn btn-info label_view' title='点击查看和修改标签' onclick='select_keep(".$model->id.")' data-toggle='modal' data-target='#keepModal'>点击查看</span>";
                },
                'contentOptions' => [
                    'width'=>'50px'
                ],
            ],
            //是否推荐
            [
                'attribute' => 'status',
                'format' => 'text',
                'content' => function($model){
                    if($model['status'] == 1){
                        return "<button id='status".$model['id']."' style='cursor:pointer'  class='is_status btn btn-success ' date_id='".$model['id']."' status='".$model['status']."' data-toggle='tooltip' data-placement='bottom' title='点击修改状态'>推荐</button>";
                    }else{
                        return "<button id='status".$model['id']."' style='cursor:pointer' class='is_status btn btn-inverse' date_id='".$model['id']."' status='".$model['status']."' data-toggle='tooltip' data-placement='bottom' title='点击修改状态'>不推荐</button>";
                    }
                },
            ],
            //是否置顶
            [
                'attribute' => 'topping',
                'format' => 'text',
                'content' => function($model){
                    if($model['topping'] == 1){
                        return "<button id='topping".$model['id']."' style='cursor:pointer'  class='is_topping btn btn-success ' date_id='".$model['id']."' topping='".$model['topping']."' data-toggle='tooltip' data-placement='bottom' title='点击修改状态'>置顶</button>";
                    }else{
                        return "<button id='topping".$model['id']."' style='cursor:pointer' class='is_topping btn btn-inverse' date_id='".$model['id']."' topping='".$model['topping']."' data-toggle='tooltip' data-placement='bottom' title='点击修改状态'>不置顶</button>";
                    }
                },
            ],


//            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    })
    //修改状态
    $('.is_status').click(function(){
        $('[data-toggle="tooltip"]').tooltip('destroy');
        var date = {
            'id': $(this).attr("date_id"),
            'is_status': $(this).attr("status")
        };
        $.ajax({
            url:"/keep/status",
            type:"post",
            data: date,
            success:function(msg){
                var num = parseInt(msg);
                var button = document.getElementById("status"+num);
                var status = button.getAttribute('status');
                if(status == 0){
                    button.setAttribute('status','1');
                    button.setAttribute('data-toggle','tooltip');
                    button.setAttribute('data-placement','bottom');
                    $('[data-toggle="tooltip"]').tooltip();
                    button.setAttribute('class','btn btn-success');
                    button.innerText = '推荐';
                }else{
                    button.setAttribute('status','0');
                    button.setAttribute('data-toggle','tooltip');
                    button.setAttribute('data-placement','bottom');
                    $('[data-toggle="tooltip"]').tooltip();
                    button.setAttribute('class','btn btn-inverse');
                    button.innerText = '不推荐';
                }
            },
            error:function(data){
                alert('修改失败');
            }
        });
    });
    //修改状态
    $('.is_topping').click(function(){
        $('[data-toggle="tooltip"]').tooltip('destroy');
        var date = {
            'id': $(this).attr("date_id"),
            'is_topping': $(this).attr("topping")
        };
        $.ajax({
            url:"/keep/topping",
            type:"post",
            data: date,
            success:function(msg){
                var num = parseInt(msg);
                var button = document.getElementById("topping"+num);
                var topping = button.getAttribute('topping');
                if(topping == 0){
                    button.setAttribute('topping','1');
                    button.setAttribute('class','btn btn-success');
                    button.innerText = '置顶';
                }else{
                    button.setAttribute('topping','0');
                    button.setAttribute('class','btn btn-inverse');
                    button.innerText = '不置顶';
                }
            },
            error:function(data){
                alert('修改失败');
            }
        });
    });
    //查看收藏夹图片
    function select_keep(id)
    {
        var date = {
            'id' : id
        }
        $.ajax({
            url:"/keep/select_keep",
            type:"post",
            data: date,
            datatype:'json',
            success:function(msg){
                if(msg==1){
                    alert('该收藏夹暂无图片');
                }
                var el = document.getElementById('keep_img_modal');
                var childs = el.childNodes;
                for(var k = childs .length - 1; k >= 0; k--) {
                    el.removeChild(childs[k]);
                }
                var img_arr = JSON.parse(msg)
                var html = "";
                html += '<div id="keep_name" style="text-align: left;height:20px;border-radius: 50px;">1</div>';
                html += '<img id="keep_img" style="text-align: center;height:180px;max-width: 200px;max-height: 180px;"></img>';
                for(var i=0; i<img_arr.length; i++){
                    var node1 = document.getElementById("keep_img_modal");
                    var node2 = document.createElement('div');
                    node2.setAttribute("id",'keep_img_modal'+img_arr[i]['id']);
                    node1.appendChild(node2);
                    $("#keep_img_modal"+img_arr[i]['id']).html(html);
                    $("#keep_img_modal"+img_arr[i]['id']).css('width','200px');
                    $("#keep_img_modal"+img_arr[i]['id']).css('height','200px');
                    $("#keep_img_modal"+img_arr[i]['id']).css('display','inline-block');
                    $("#keep_img_modal"+img_arr[i]['id']).css('overflow','hidden');
                    $("#keep_img_modal"+img_arr[i]['id']).css('text-align','center');
                    $("#keep_img_modal"+img_arr[i]['id']).css('margin-right','10px');
                    $("#keep_img_modal"+img_arr[i]['id']).children('#keep_name').text(img_arr[i]['name']);
                    $("#keep_img_modal"+img_arr[i]['id']).children('#keep_img').attr('src','http://qiniu.zaoanart.com/'+img_arr[i]['image']+'?imageView2/2/h/500');
                }
            },
            error:function(data){
                alert('执行失败');
            }
        });
    }
</script>
