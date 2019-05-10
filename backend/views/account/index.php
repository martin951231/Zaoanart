<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\AccountSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '用户';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="account-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('添加用户', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

            'id',
//            'account_role',
            'username',
//            'password',
            'nickname',
            // 'avatar',
            // 'ip_address',
            // 'phone',
            // 'weixin',
            // 'gender',
            // 'position',
            // 'birthday',
//             'is_deleted',
            [
                'attribute' => 'is_deleted',
                'format' => 'text',
                'content' => function($model){
                    if($model['is_deleted'] == 0){
                        return "<button id='".$model['id']."' style='cursor:pointer'  class='status  btn btn-success' date_id='".$model['id']."' status='".$model['is_deleted']."'>启用</button>";
                    }else{
                        return "<button id='".$model['id']."' style='cursor:pointer' class='status  btn btn-inverse' date_id='".$model['id']."' status='".$model['is_deleted']."'>禁用</button>";
                    }
                },
            ],
            // 'last_login_time',
            // 'created_at',
            // 'updated_at',

            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{view} {update} {delete}',
            ],
        ],
    ]); ?>

    <script type="text/javascript">
        $('.status').click(function(){
            var date = {
                'id': $(this).attr("date_id"),
                'is_deleted': $(this).attr("status")
            };
            $.ajax({
                url:"/account/status",
                type:"post",
                data: date,
                success:function(msg){
                    var num = parseInt(msg);
                    var span = document.getElementById(num);
                    var status = span.getAttribute('status');
                    if(status == 0){
                        span.setAttribute('status','1');
                        span.setAttribute('class','btn btn-inverse');
                        span.innerText = '禁用';
                    }else{
                        span.setAttribute('status','0');
                        span.setAttribute('class','btn btn-success');
                        span.innerText = '启用';
                    }
                },
                error:function(data){
                    alert('修改失败');
                }
            });
        });
    </script>
</div>

