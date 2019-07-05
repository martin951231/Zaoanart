<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\MessageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '留言';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="message-index">

    <h1><?php //Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php //Html::a('Create Message', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <!-- 查看留言内容模态框 -->
    <div class="modal fade" id="labelModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document" style="width:90%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">查看标签</h4>
                </div>
                <div class="modal-body">
                    <div>
                        <span  style="margin-top:10px">自定义标签<span style="color:red">(快来添加你想要的标签吧)</span>&nbsp&nbsp：&nbsp</span><br>
                        <input type="text" style="margin-top:10px;" id="label_input">
                        <button  type="button" class="btn btn-primary" onclick="add_label_list()">添加</button>
                        <button  type="button" id="show_del_label" style="display:inline-block" class="btn btn-warning" onclick="del_label_list(event)">删除</button>
                        <button  type="button" id="hide_del_label" style="display:none" class="btn btn-default" onclick="del_label_list1(event)">取消</button>
                    </div>
                    <div style="margin-top:10px">
                        <span  style="margin-top:10px"  data-toggle='tooltip' data-placement='right' title='点击下列标签添加,点击❌删除'>常用标签<span style="color:red">(点击下方红色❌即会删除常用标签,请小心操作)</span>&nbsp&nbsp：&nbsp</span><br>
                        <div id="label_list" style="margin-top:10px;height:300px;border:double #ccc;overflow: scroll;background-color: antiquewhite;">
                        </div>
                    </div>
                    <div  style="margin-top:10px">
                        <span  style="margin-top:10px"  data-toggle='tooltip' data-placement='right' title='点击下列标签删除'>我的标签<span style="color:red">(点击下方按钮即会删除我的标签,请小心操作)</span>&nbsp&nbsp：&nbsp</span><br>
                        <div  id="mylabel" style="margin-top:10px;height:250px;border:double #ccc;overflow: scroll;background-color: antiquewhite;">
                        </div>
                    </div>
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
            'id',
            'uid',
            //留言内容
            [
                'attribute'=>'content',
                'label'=>'留言内容',
                'contentOptions' => [
                    'width'=>'150'
                ],
                'format' => 'text',
                'content'=>function($model){
                    return "<span id='upname".$model['id']."' title='双击修改' style='cursor:pointer;width:200px;display: inline-block;white-space:pre-wrap;word-break: break-all;' ondblclick='update_name(".$model->id.")' date_id='".$model['id']."' data-toggle='tooltip' data-placement='bottom' >".$model['content']."</span>";
                },
            ],
//            'content',
            //操作
            [
                'attribute' => 'content',
                'label'=>'操作',
                'format' => 'text',
                'contentOptions' => [
                    'width'=>'150'
                ],
                'content' => function($model){
                    return "<div style='text-align: center'>
                                <button id='content".$model['id']."' style='cursor:pointer'  class='is_appear btn btn-info' date_id='".$model['id']."' data-toggle='tooltip' data-placement='bottom' title='点击查看'>查看</button>
                                <button id='delete".$model['id']."' style='cursor:pointer'  class='is_appear btn btn-warning' date_id='".$model['id']."' data-toggle='tooltip' data-placement='bottom' title='点击查看'>删除</button>
                                </div> ";
                },
            ],
//            ['class' => 'yii\grid\ActionColumn'],
            'created_at',
        ],
    ]); ?>
</div>
<script type="text/javascript">
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    })


</script>
