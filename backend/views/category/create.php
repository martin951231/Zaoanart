<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Category */

$this->title = '添加商品类型';
$this->params['breadcrumbs'][] = ['label' => '商品类型', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="category-create">
    <p>请输入商品类型:</p>
    <input id="cate_content" type="text" style="border: 1px solid #000;height: 30px;width: 250px;margin-bottom: 10px;">
    <p>请选择添加到:</p>
    <div id="category" style="line-height: 30px;width: 250px;" data_id="1" onclick="select_(event)">
        <div>
            <input id="cate_input" type="text" style="border: 1px solid #000;border-right: none;height: 30px;width: 220px;position: fixed;">
            <img src="../images/bottom.png" alt="" width="30px" style="float: right;background-color: #fff;border: 1px solid #000;">
        </div>
        <div id="cate_div" style="margin-top: 30px;height: 0px;overflow: hidden;">
            <?php foreach ($info as $val => $key){ ?>
                <div  style="padding-left: 30px;">
                <span class="cate_span" val=<?php echo $key['name'] ?> to_id=<?php echo $key['id'] ?> style="display:block;" onclick="select_1(event)">
                     <?php echo $key['name'] ?>
                </span>
                    <?php foreach ($key['info'] as $val2 => $key2){ ?>
                        <span class="cate_span" val=<?php echo $key2['category_name'] ?> to_id=<?php echo $key2['id'] ?> style="display:block;" onclick="select_1(event)">
                        &nbsp&nbsp&nbsp&nbsp
                            <?php echo $key2['category_name'] ?>
                    </span>
                    <?php }?>
                </div>
            <?php }?>
        </div>
    </div>

    <button class="btn btn-primary" style="margin-top:60px;" onclick="add_()">添加</button>


    <?php //$this->render('_form', [
        //'model' => $model,
    //]) ?>

</div>

<script type="text/javascript">
    function select_(e){
        if($('#category').attr('data_id') == 1){
            $('#category').attr('data_id',0)
            $('#cate_div').css('height','500px')
            $('#cate_div').css('overflow-y','scroll')
        }else{
            $('#category').attr('data_id',1)
            $('#cate_div').css('height','0px')
            $('#cate_div').css('overflow-y','hidden')
        }
    }
    function select_1(e){
        $('#cate_input').val($(e.target).attr('val'))
        $('#cate_input').attr('value',$(e.target).attr('to_id'))
    }
    function add_(){

        var date = {
            'to_id': $('#cate_input').attr('value'),
            'cate_content': $('#cate_content').val()
        };
        $.ajax({
            url:"/category/addcate",
            type:"get",
            data: date,
            success:function(msg){
                if(msg){
                    alert('添加成功')
                    location.reload();
                }
            },
            error:function(data){
                alert('修改失败');
            }
        });
    }
</script>
<style>
    .cate_span{
        cursor: pointer;
    }
    .cate_span:hover{
        color:#ff6700
    }
#cate_div{
    background-color: #fff;
    margin-top: 30px;
    position: absolute;
    width: 250px;
}
</style>
