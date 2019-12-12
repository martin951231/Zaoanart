<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\data\ActiveDataProvide;
use backend\models\Category;
use backend\models\Label;
use backend\models\Goods;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use common\models\SmallImage;
use yii\imagine\Image;
use backend\controllers\GoodsController;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use yii\widgets\LinkPager;
use yii\data\Pagination;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\GoodsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
//echo phpinfo();
$this->title = '商品';
$this->params['breadcrumbs'][] = $this->title;
?>
<!--<div style="width:500px;height=500px;background-color:666666">12</div>-->
<div class="goods-index">
    <style>
        ul{
            padding: 0px;
        }
        ul li{
            list-style: none;
        }
        .ul_li{
            display: inline-block;
            width: 25px;
            height: 15px;
            font-size: 10px;
            cursor:pointer;
        }
        #upcategory1{
            display: block;
        }
        .text_style{
            width:100px;
            height:100px;
            white-space:normal;
            word-break: break-all;
            overflow: auto;
        }
        .select_input{
            width:100px;
            display:inline;
        }
        .checkbox_size{
            zoom:300%
        }
        .modal_style{
            margin-top:5px;
            margin-left:5px
        }
        #img1{
            border: ridge;
            background-color: antiquewhite;
        }
        .label_style{
            margin-left: 10px;
            margin-top: 10px;
        }
    </style>
    <h1><?php //Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <p>
        <link href="https://cdn.bootcss.com/toastr.js/2.1.4/toastr.min.css" rel="stylesheet">
        <script src="https://cdn.bootcss.com/toastr.js/2.1.4/toastr.min.js"></script>
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
                    <?= $form->field(new Goods, 'image[]')->fileInput(['multiple' => true]);?>
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
    </p>
    <!--批量修改操作-->
    <tr>
        <td colspan="14" style="text-align: left;">
            <select id="batch_edit"  class="form-control select_input">
                <option value="0" style="font-weight:bold">选择操作</option>
<!--                <option value="1">删除</option>-->
                <option value="2">显示</option>
                <option value="3">隐藏</option>
                <option value="4">推荐</option>
                <option value="5">不推荐</option>
            </select>
        </td>
    </tr>
    <!--批量修改类型-->
    <tr>
        <td colspan="14" style="text-align: left;">
            <select id="batch_edit_category"  class="form-control select_input">
                <option value="0">选择类型</option>
                <option value="1">油画</option>
                <option value="6">---当代油画</option>
                <option value="8">---古典油画</option>
                <option value="9">---现代经典油画</option>
                <option value="19">---印象派油画</option>
                <option value="2">国画</option>
                <option value="10">---当代水墨</option>
                <option value="11">---近现代水墨</option>
                <option value="12">---古代画</option>
                <option value="18">---书法篆刻</option>
                <option value="3">综合绘画</option>
                <option value="13">---综合媒介绘画</option>
                <option value="14">---数字绘画</option>
                <option value="4">装饰画</option>
                <option value="5">其他</option>
                <option value="15">---素描水彩</option>
                <option value="16">---摄影</option>
                <option value="17">---卡通插画</option>
                <option value="20">---日本绘画</option>
                <option value="21">---地图</option>
            </select>
        </td>
    </tr>
    <!--批量修改主题-->
    <tr>
        <td colspan="14" style="text-align: left;">
            <select id="batch_edit_theme" class="form-control select_input">
                <option value="0">选择主题</option>
                <option value="1">抽象</option>
                <option value="2">风景</option>
                <option value="3">静物</option>
                <option value="4">人物</option>
                <option value="5">植物花卉</option>
                <option value="6">建筑场景</option>
                <option value="7">动物宠物</option>
                <option value="8">书法文字</option>
                <option value="9">其他</option>
                <option value="10">机械工具</option>
                <option value="11">宇宙星空</option>
            </select>
        </td>
    </tr>
    <!--批量修改溢价指数-->
    <tr>
        <td colspan="14" style="text-align: left;">
            <input id="batch_edit_premium" type="text" placeholder="溢价指数" class="form-control select_input">
<!--            <input id="batch_edit_label" type="text" placeholder="标签" class="form-control select_input">-->
            <input id="confirm_select" type="button" class="btn btn-primary" value="批量修改" onclick="confirm_select_all()">
        </td>
    </tr>
    <div>
        <span  style="cursor:pointe;display: inline-block;width: 150px;margin:5px 0px;" class='btn btn-info label_view' data-toggle='modal' data-target='#label_all_Modal' onclick="select_label_all()">批量添加标签</span>
        <span  style="cursor:pointe;display: inline-block;width: 150px;margin:5px 0px;" class='btn btn-danger label_view' data-toggle='modal'  onclick="delete_label_all()">批量删除标签</span>
    </div>

<!--    每页显示数量-->
    <div>
        <input id="pagesize" type="text" placeholder="每页显示数量" style="width:100px">
        <button type="button" class="btn btn-primary" onclick="up_pagesize(<?= Yii::$app->user->identity->id; ?>)">确认</button>
    </div>
<!--    查询图片所在页-->
    <div style="margin-top:5px">
        <input id="img_page" type="text" placeholder="跳转图片所在页" style="width:100px">
        <button type="button" class="btn btn-primary" onclick="img_page()">搜索</button>
    </div>
    <!-- 查看标签模态框 -->
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
    <!-- 批量添加标签模态框 -->
    <div class="modal fade" id="label_all_Modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document" style="width:90%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">批量添加标签</h4>
                </div>
                <div class="modal-body">
                    <div>
                        <span  style="margin-top:10px">自定义标签<span style="color:red">(快来添加你想要的标签吧)</span>&nbsp&nbsp：&nbsp</span><br>
                        <input type="text" style="margin-top:10px;" id="label_input2">
                        <button  type="button" class="btn btn-primary" onclick="add_label_list()">添加</button>
                        <button  type="button" id="show_del_labels" style="display:inline-block" class="btn btn-warning" onclick="del_label_lists(event)">删除</button>
                        <button  type="button" id="hide_del_labels" style="display:none" class="btn btn-default" onclick="del_label_list1s(event)">取消</button>
                    </div>
                    <div style="margin-top:10px">
                        <span  style="margin-top:10px"  data-toggle='tooltip' data-placement='right' title='点击下列标签添加,点击❌删除'>常用标签<span style="color:red">(点击下方红色❌即会删除常用标签,请小心操作)</span>&nbsp&nbsp：&nbsp</span><br>
                        <div id="label_list_all" style="margin-top:10px;border:double #ccc;overflow: scroll;background-color: antiquewhite;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                </div>
            </div>
        </div>
    </div>
    <!-- 批量删除标签模态框 -->
    <div class="modal fade" id="delete_all_Modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document" style="width:90%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">批量删除标签</h4>
                </div>
                <div class="modal-body">
                    <div>
                        <button  type="button" id="show_del_labels2" style="display:inline-block" class="btn btn-warning" onclick="del_label_lists2(event)">删除</button>
                        <button  type="button" id="hide_del_labels2" style="display:none" class="btn btn-default" onclick="del_label_list1s2(event)">取消</button>
                    </div>
                    <div style="margin-top:10px">
                        <span  style="margin-top:10px"  data-toggle='tooltip' data-placement='right'>现有标签<br>
                        <div id="delete_label_list_all" style="margin-top:10px;height: 500px;border:double #ccc;overflow: scroll;background-color: antiquewhite;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                </div>
            </div>
        </div>
    </div>
    <?= LinkPager::widget([
        'pagination' => $page,
        'firstPageLabel' => '首页',
        'prevPageLabel' => '上一页',
        'nextPageLabel' => '下一页',
        'lastPageLabel' => '尾页',
    ]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
         'pager'=>[
        //'options'=>['class'=>'hidden']//关闭分页
        'firstPageLabel'=>"首页",
        'prevPageLabel'=>'上一页',
        'nextPageLabel'=>'下一页',
        'lastPageLabel'=>'尾页',
        ],
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],
            [
                'class' => 'yii\grid\CheckboxColumn',
                'checkboxOptions'=>function($model){
                    return ['id' => $model->id ,'class' => 'checkbox_size'];
                }
            ],
            'id',
            //名称
            [
                'attribute'=>'name',
                'format' => 'text',
                'content'=>function($model){
        if($model->is_face == 0){
            return "<button id='upname1".$model['id']."' class='btn btn-info' style='display:block' data_id='".$model->is_face."' onclick='up_is_face(".$model->id.")'>显示</button>
                        <span id='upname".$model['id']."' title='双击修改' style='cursor:pointer;color:#FF830F;width:200px;display: inline-block;white-space:pre-wrap;word-break: break-all;' ondblclick='update_name(".$model->id.")' date_id='".$model['id']."' data-toggle='tooltip' data-placement='bottom' >".$model['name']."</span>";
        }else{
            return "<button id='upname1".$model['id']."' class='btn btn-inverse' style='display:block' data_id='".$model->is_face."' onclick='up_is_face(".$model->id.",".$model->is_face .")'>隐藏</button>
                        <span id='upname".$model['id']."' title='双击修改' style='cursor:pointer;color:#FF830F;width:200px;display: inline-block;white-space:pre-wrap   ;word-break: break-all;' ondblclick='update_name(".$model->id.")' date_id='".$model['id']."' data-toggle='tooltip' data-placement='bottom' >".$model['name']."</span>";
        }
                },
            ],
            //图片
            [
                'attribute'=>'image',
                'format' => ['image',['width'=>88]],
                'content' => function($model){
                    return '<div style="width:500px;height:500px;position: absolute;margin-left: 88px;margin-top: -206px;display:none" id="imgshow'.$model->id.'"><img src="http://qiniu.zaoanart.com/'.$model->image.'" height="500px"></div><img src="http://qiniu.zaoanart.com/'.$model->image.'?imageView2/1/w/88/h/88" onclick=find_img('.$model->id.')>';
                },
            ],
            //作者
            [
                'attribute'=>'author',
                'format' => 'text',
                'content'=>function($model){
                    if(!$model->author){$model->author = "待添加";};
                    if($model->author == "待添加"){
                        return "<span id='upauthor".$model['id']."' title='双击修改' style='cursor:pointer;color:red' ondblclick='update_author(".$model->id.")' date_id='".$model['id']."' data-toggle='tooltip' data-placement='bottom' >".$model->author."</span>";
                    }else{
                        return "<span id='upauthor".$model['id']."' title='双击修改' style='cursor:pointer;color:black' ondblclick='update_author(".$model->id.")' date_id='".$model['id']."' data-toggle='tooltip' data-placement='bottom' >".$model->author."</span>";
                    }
                },
            ],
            //类型
            [
                    'label'=>'商品分类',
                    'attribute' => 'category_name',
                    'value' => 'tsy_category.category_name',
                    'filter'=>Html::activeTextInput($searchModel,'category_name',['class'=>'form-control']),
                    'format' => ['text',['width'=>880]],
                    'content' => function($model){
                        $sort = $model['category'];
                        switch($sort){
                            case $sort == 1:return "<div id='upcategory".$model['id']."'><span id='upcategory1".$model['id']."' title='点击修改' style='cursor:pointer;' onclick='update_category(".$model->id.")' data-toggle='tooltip' data-placement='bottom' >油画</span>
<ul>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",6)'>当油</li>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",8)'>古油</li>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",9)'>现油</li>
    <br>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",19)'>印油</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",10)'>当水</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",11)'>近水</li>
    <br>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",12)'>古代</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",18)'>书法</li>
    <li class='ul_li btn-warning' onclick='fastup_cate(".$model->id.",13)'>综媒</li>
    <br>
    <li class='ul_li btn-warning' onclick='fastup_cate(".$model->id.",14)'>数字</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",15)'>素描</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",16)'>摄影</li>
    <br>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",17)'>卡通</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",20)'>日绘</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",21)'>地图</li>
</ul>
</div>";
                            case $sort == 2:return "<div id='upcategory".$model['id']."'><span id='upcategory1".$model['id']."' title='点击修改' style='cursor:pointer;' onclick='update_category(".$model->id.")' data-toggle='tooltip' data-placement='bottom' >国画</span>
<ul>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",6)'>当油</li>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",8)'>古油</li>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",9)'>现油</li>
    <br>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",19)'>印油</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",10)'>当水</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",11)'>近水</li>
    <br>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",12)'>古代</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",18)'>书法</li>
    <li class='ul_li btn-warning' onclick='fastup_cate(".$model->id.",13)'>综媒</li>
    <br>
    <li class='ul_li btn-warning' onclick='fastup_cate(".$model->id.",14)'>数字</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",15)'>素描</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",16)'>摄影</li>
    <br>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",17)'>卡通</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",20)'>日绘</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",21)'>地图</li>
</ul>
</div>";
                            case $sort == 3:return "<div id='upcategory".$model['id']."'><span id='upcategory1".$model['id']."' title='点击修改' style='cursor:pointer;' onclick='update_category(".$model->id.")' data-toggle='tooltip' data-placement='bottom' >综合绘画</span>
<ul>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",6)'>当油</li>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",8)'>古油</li>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",9)'>现油</li>
    <br>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",19)'>印油</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",10)'>当水</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",11)'>近水</li>
    <br>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",12)'>古代</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",18)'>书法</li>
    <li class='ul_li btn-warning' onclick='fastup_cate(".$model->id.",13)'>综媒</li>
    <br>
    <li class='ul_li btn-warning' onclick='fastup_cate(".$model->id.",14)'>数字</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",15)'>素描</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",16)'>摄影</li>
    <br>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",17)'>卡通</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",20)'>日绘</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",21)'>地图</li>
</ul>
</div>";
                            case $sort == 4:return "<div id='upcategory".$model['id']."'><span id='upcategory1".$model['id']."' title='点击修改' style='cursor:pointer;' onclick='update_category(".$model->id.")' data-toggle='tooltip' data-placement='bottom' >装饰画</span>
<ul>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",6)'>当油</li>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",8)'>古油</li>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",9)'>现油</li>
    <br>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",19)'>印油</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",10)'>当水</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",11)'>近水</li>
    <br>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",12)'>古代</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",18)'>书法</li>
    <li class='ul_li btn-warning' onclick='fastup_cate(".$model->id.",13)'>综媒</li>
    <br>
    <li class='ul_li btn-warning' onclick='fastup_cate(".$model->id.",14)'>数字</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",15)'>素描</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",16)'>摄影</li>
    <br>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",17)'>卡通</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",20)'>日绘</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",21)'>地图</li>
</ul>
</div>";
                            case $sort == 5:return "<div id='upcategory".$model['id']."'><span id='upcategory1".$model['id']."' title='点击修改' style='cursor:pointer;' onclick='update_category(".$model->id.")' data-toggle='tooltip' data-placement='bottom' >其他</span>
<ul>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",6)'>当油</li>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",8)'>古油</li>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",9)'>现油</li>
    <br>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",19)'>印油</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",10)'>当水</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",11)'>近水</li>
    <br>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",12)'>古代</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",18)'>书法</li>
    <li class='ul_li btn-warning' onclick='fastup_cate(".$model->id.",13)'>综媒</li>
    <br>
    <li class='ul_li btn-warning' onclick='fastup_cate(".$model->id.",14)'>数字</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",15)'>素描</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",16)'>摄影</li>
    <br>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",17)'>卡通</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",20)'>日绘</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",21)'>地图</li>
</ul>
</div>";
                            case $sort == 6:return "<div id='upcategory".$model['id']."'><span id='upcategory1".$model['id']."' title='点击修改' style='cursor:pointer;' onclick='update_category(".$model->id.")' data-toggle='tooltip' data-placement='bottom' >当代油画</span>
<ul>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",6)'>当油</li>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",8)'>古油</li>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",9)'>现油</li>
    <br>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",19)'>印油</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",10)'>当水</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",11)'>近水</li>
    <br>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",12)'>古代</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",18)'>书法</li>
    <li class='ul_li btn-warning' onclick='fastup_cate(".$model->id.",13)'>综媒</li>
    <br>
    <li class='ul_li btn-warning' onclick='fastup_cate(".$model->id.",14)'>数字</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",15)'>素描</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",16)'>摄影</li>
    <br>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",17)'>卡通</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",20)'>日绘</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",21)'>地图</li>
</ul>
</div>";
                            case $sort == 8:return "<div id='upcategory".$model['id']."'><span id='upcategory1".$model['id']."' title='点击修改' style='cursor:pointer;' onclick='update_category(".$model->id.")' data-toggle='tooltip' data-placement='bottom' >古典油画</span>
<ul>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",6)'>当油</li>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",8)'>古油</li>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",9)'>现油</li>
    <br>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",19)'>印油</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",10)'>当水</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",11)'>近水</li>
    <br>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",12)'>古代</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",18)'>书法</li>
    <li class='ul_li btn-warning' onclick='fastup_cate(".$model->id.",13)'>综媒</li>
    <br>
    <li class='ul_li btn-warning' onclick='fastup_cate(".$model->id.",14)'>数字</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",15)'>素描</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",16)'>摄影</li>
    <br>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",17)'>卡通</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",20)'>日绘</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",21)'>地图</li>
</ul>
</div>";
                            case $sort == 9:return "<div id='upcategory".$model['id']."'><span id='upcategory1".$model['id']."' title='点击修改' style='cursor:pointer;' onclick='update_category(".$model->id.")' data-toggle='tooltip' data-placement='bottom' >现代经典油画</span>
<ul>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",6)'>当油</li>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",8)'>古油</li>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",9)'>现油</li>
    <br>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",19)'>印油</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",10)'>当水</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",11)'>近水</li>
    <br>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",12)'>古代</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",18)'>书法</li>
    <li class='ul_li btn-warning' onclick='fastup_cate(".$model->id.",13)'>综媒</li>
    <br>
    <li class='ul_li btn-warning' onclick='fastup_cate(".$model->id.",14)'>数字</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",15)'>素描</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",16)'>摄影</li>
    <br>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",17)'>卡通</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",20)'>日绘</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",21)'>地图</li>
</ul>
</div>";
                            case $sort == 10:return "<div id='upcategory".$model['id']."'><span id='upcategory1".$model['id']."' title='点击修改' style='cursor:pointer;' onclick='update_category(".$model->id.")' data-toggle='tooltip' data-placement='bottom' >当代水墨</span>
<ul>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",6)'>当油</li>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",8)'>古油</li>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",9)'>现油</li>
    <br>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",19)'>印油</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",10)'>当水</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",11)'>近水</li>
    <br>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",12)'>古代</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",18)'>书法</li>
    <li class='ul_li btn-warning' onclick='fastup_cate(".$model->id.",13)'>综媒</li>
    <br>
    <li class='ul_li btn-warning' onclick='fastup_cate(".$model->id.",14)'>数字</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",15)'>素描</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",16)'>摄影</li>
    <br>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",17)'>卡通</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",20)'>日绘</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",21)'>地图</li>
</ul>
</div>";
                            case $sort == 11:return "<div id='upcategory".$model['id']."'><span id='upcategory1".$model['id']."' title='点击修改' style='cursor:pointer;' onclick='update_category(".$model->id.")' data-toggle='tooltip' data-placement='bottom' >近现代水墨</span>
<ul>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",6)'>当油</li>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",8)'>古油</li>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",9)'>现油</li>
    <br>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",19)'>印油</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",10)'>当水</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",11)'>近水</li>
    <br>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",12)'>古代</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",18)'>书法</li>
    <li class='ul_li btn-warning' onclick='fastup_cate(".$model->id.",13)'>综媒</li>
    <br>
    <li class='ul_li btn-warning' onclick='fastup_cate(".$model->id.",14)'>数字</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",15)'>素描</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",16)'>摄影</li>
    <br>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",17)'>卡通</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",20)'>日绘</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",21)'>地图</li>
</ul>
</div>";
                            case $sort == 12:return "<div id='upcategory".$model['id']."'><span id='upcategory1".$model['id']."' title='点击修改' style='cursor:pointer;' onclick='update_category(".$model->id.")' data-toggle='tooltip' data-placement='bottom' >古代画</span>
<ul>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",6)'>当油</li>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",8)'>古油</li>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",9)'>现油</li>
    <br>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",19)'>印油</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",10)'>当水</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",11)'>近水</li>
    <br>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",12)'>古代</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",18)'>书法</li>
    <li class='ul_li btn-warning' onclick='fastup_cate(".$model->id.",13)'>综媒</li>
    <br>
    <li class='ul_li btn-warning' onclick='fastup_cate(".$model->id.",14)'>数字</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",15)'>素描</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",16)'>摄影</li>
    <br>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",17)'>卡通</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",20)'>日绘</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",21)'>地图</li>
</ul>
</div>";
                            case $sort == 13:return "<div id='upcategory".$model['id']."'><span id='upcategory1".$model['id']."' title='点击修改' style='cursor:pointer;' onclick='update_category(".$model->id.")' data-toggle='tooltip' data-placement='bottom' >综合媒介绘画</span>
<ul>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",6)'>当油</li>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",8)'>古油</li>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",9)'>现油</li>
    <br>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",19)'>印油</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",10)'>当水</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",11)'>近水</li>
    <br>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",12)'>古代</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",18)'>书法</li>
    <li class='ul_li btn-warning' onclick='fastup_cate(".$model->id.",13)'>综媒</li>
    <br>
    <li class='ul_li btn-warning' onclick='fastup_cate(".$model->id.",14)'>数字</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",15)'>素描</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",16)'>摄影</li>
    <br>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",17)'>卡通</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",20)'>日绘</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",21)'>地图</li>
</ul>
</div>";
                            case $sort == 14:return "<div id='upcategory".$model['id']."'><span id='upcategory1".$model['id']."' title='点击修改' style='cursor:pointer;' onclick='update_category(".$model->id.")' data-toggle='tooltip' data-placement='bottom' >数字绘画</span>
<ul>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",6)'>当油</li>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",8)'>古油</li>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",9)'>现油</li>
    <br>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",19)'>印油</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",10)'>当水</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",11)'>近水</li>
    <br>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",12)'>古代</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",18)'>书法</li>
    <li class='ul_li btn-warning' onclick='fastup_cate(".$model->id.",13)'>综媒</li>
    <br>
    <li class='ul_li btn-warning' onclick='fastup_cate(".$model->id.",14)'>数字</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",15)'>素描</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",16)'>摄影</li>
    <br>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",17)'>卡通</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",20)'>日绘</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",21)'>地图</li>
</ul>
</div>";
                            case $sort == 15:return "<div id='upcategory".$model['id']."'><span id='upcategory1".$model['id']."' title='点击修改' style='cursor:pointer;' onclick='update_category(".$model->id.")' data-toggle='tooltip' data-placement='bottom' >素描水彩</span>
<ul>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",6)'>当油</li>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",8)'>古油</li>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",9)'>现油</li>
    <br>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",19)'>印油</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",10)'>当水</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",11)'>近水</li>
    <br>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",12)'>古代</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",18)'>书法</li>
    <li class='ul_li btn-warning' onclick='fastup_cate(".$model->id.",13)'>综媒</li>
    <br>
    <li class='ul_li btn-warning' onclick='fastup_cate(".$model->id.",14)'>数字</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",15)'>素描</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",16)'>摄影</li>
    <br>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",17)'>卡通</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",20)'>日绘</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",21)'>地图</li>
</ul>
</div>";
                            case $sort == 16:return "<div id='upcategory".$model['id']."'><span id='upcategory1".$model['id']."' title='点击修改' style='cursor:pointer;' onclick='update_category(".$model->id.")' data-toggle='tooltip' data-placement='bottom' >摄影</span>
<ul>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",6)'>当油</li>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",8)'>古油</li>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",9)'>现油</li>
    <br>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",19)'>印油</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",10)'>当水</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",11)'>近水</li>
    <br>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",12)'>古代</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",18)'>书法</li>
    <li class='ul_li btn-warning' onclick='fastup_cate(".$model->id.",13)'>综媒</li>
    <br>
    <li class='ul_li btn-warning' onclick='fastup_cate(".$model->id.",14)'>数字</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",15)'>素描</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",16)'>摄影</li>
    <br>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",17)'>卡通</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",20)'>日绘</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",21)'>地图</li>
</ul>
</div>";
                            case $sort == 17:return "<div id='upcategory".$model['id']."'><span id='upcategory1".$model['id']."' title='点击修改' style='cursor:pointer;' onclick='update_category(".$model->id.")' data-toggle='tooltip' data-placement='bottom' >卡通插画</span>
<ul>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",6)'>当油</li>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",8)'>古油</li>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",9)'>现油</li>
    <br>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",19)'>印油</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",10)'>当水</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",11)'>近水</li>
    <br>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",12)'>古代</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",18)'>书法</li>
    <li class='ul_li btn-warning' onclick='fastup_cate(".$model->id.",13)'>综媒</li>
    <br>
    <li class='ul_li btn-warning' onclick='fastup_cate(".$model->id.",14)'>数字</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",15)'>素描</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",16)'>摄影</li>
    <br>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",17)'>卡通</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",20)'>日绘</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",21)'>地图</li>
</ul>
</div>";
                            case $sort == 18:return "<div id='upcategory".$model['id']."'><span id='upcategory1".$model['id']."' title='点击修改' style='cursor:pointer;' onclick='update_category(".$model->id.")' data-toggle='tooltip' data-placement='bottom' >书法篆刻</span>
<ul>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",6)'>当油</li>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",8)'>古油</li>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",9)'>现油</li>
    <br>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",19)'>印油</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",10)'>当水</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",11)'>近水</li>
    <br>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",12)'>古代</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",18)'>书法</li>
    <li class='ul_li btn-warning' onclick='fastup_cate(".$model->id.",13)'>综媒</li>
    <br>
    <li class='ul_li btn-warning' onclick='fastup_cate(".$model->id.",14)'>数字</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",15)'>素描</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",16)'>摄影</li>
    <br>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",17)'>卡通</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",20)'>日绘</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",21)'>地图</li>
</ul>
</div>";
                            case $sort == 19:return "<div id='upcategory".$model['id']."'><span id='upcategory1".$model['id']."' title='点击修改' style='cursor:pointer;' onclick='update_category(".$model->id.")' data-toggle='tooltip' data-placement='bottom' >印象派油画</span>
<ul>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",6)'>当油</li>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",8)'>古油</li>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",9)'>现油</li>
    <br>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",19)'>印油</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",10)'>当水</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",11)'>近水</li>
    <br>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",12)'>古代</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",18)'>书法</li>
    <li class='ul_li btn-warning' onclick='fastup_cate(".$model->id.",13)'>综媒</li>
    <br>
    <li class='ul_li btn-warning' onclick='fastup_cate(".$model->id.",14)'>数字</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",15)'>素描</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",16)'>摄影</li>
    <br>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",17)'>卡通</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",20)'>日绘</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",21)'>地图</li>
</ul>
</div>";
                            case $sort == 20:return "<div id='upcategory".$model['id']."'><span id='upcategory1".$model['id']."' title='点击修改' style='cursor:pointer;' onclick='update_category(".$model->id.")' data-toggle='tooltip' data-placement='bottom' >日本绘画</span>
<ul>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",6)'>当油</li>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",8)'>古油</li>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",9)'>现油</li>
    <br>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",19)'>印油</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",10)'>当水</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",11)'>近水</li>
    <br>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",12)'>古代</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",18)'>书法</li>
    <li class='ul_li btn-warning' onclick='fastup_cate(".$model->id.",13)'>综媒</li>
    <br>
    <li class='ul_li btn-warning' onclick='fastup_cate(".$model->id.",14)'>数字</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",15)'>素描</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",16)'>摄影</li>
    <br>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",17)'>卡通</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",20)'>日绘</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",21)'>地图</li>
</ul>
</div>";
                            case $sort == 21:return "<div id='upcategory".$model['id']."'><span id='upcategory1".$model['id']."' title='点击修改' style='cursor:pointer;' onclick='update_category(".$model->id.")' data-toggle='tooltip' data-placement='bottom' >地图</span>
<ul>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",6)'>当油</li>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",8)'>古油</li>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",9)'>现油</li>
    <br>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",19)'>印油</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",10)'>当水</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",11)'>近水</li>
    <br>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",12)'>古代</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",18)'>书法</li>
    <li class='ul_li btn-warning' onclick='fastup_cate(".$model->id.",13)'>综媒</li>
    <br>
    <li class='ul_li btn-warning' onclick='fastup_cate(".$model->id.",14)'>数字</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",15)'>素描</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",16)'>摄影</li>
    <br>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",17)'>卡通</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",20)'>日绘</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",21)'>地图</li>
</ul>
</div>";
                            case $sort == 999:return "<div id='upcategory".$model['id']."'><span id='upcategory1".$model['id']."' title='点击修改' style='cursor:pointer;color:red' onclick='update_category(".$model->id.")' data-toggle='tooltip' data-placement='bottom' >未定义</span>
<ul>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",6)'>当油</li>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",8)'>古油</li>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",9)'>现油</li>
    <br>
    <li class='ul_li btn-success' onclick='fastup_cate(".$model->id.",19)'>印油</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",10)'>当水</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",11)'>近水</li>
    <br>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",12)'>古代</li>
    <li class='ul_li btn-info' onclick='fastup_cate(".$model->id.",18)'>书法</li>
    <li class='ul_li btn-warning' onclick='fastup_cate(".$model->id.",13)'>综媒</li>
    <br>
    <li class='ul_li btn-warning' onclick='fastup_cate(".$model->id.",14)'>数字</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",15)'>素描</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",16)'>摄影</li>
    <br>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",17)'>卡通</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",20)'>日绘</li>
    <li class='ul_li btn-danger' onclick='fastup_cate(".$model->id.",21)'>地图</li>
</ul>
</div>";
                        }
                    }
            ],
            //主题
            [
                'label'=>'商品主题',
                'attribute' => 'theme_name',
                'value' => 'tsy_theme.theme_name',
                'filter'=>Html::activeTextInput($searchModel,'theme_name',['class'=>'form-control']),
                'format' => 'text',
                'content' => function($model){
                    $theme = $model['theme'];
                    switch($theme){
                        case $theme == 1: return "
<div id='uptheme".$model['id']."'>
    <span id='uptheme1".$model['id']."' data-placement='bottom' >抽象</span>
    <ul>
        <li class='ul_li' style='cursor:pointer;background-color:red;color:#fff' onclick='fastup_theme(".$model->id.",1)'>抽象</li>
        <li class='ul_li' style='cursor:pointer;background-color:#ff9900;color:#fff' onclick='fastup_theme(".$model->id.",2)'>风景</li>
        <li class='ul_li' style='cursor:pointer;background-color:#949001;color:#fff' onclick='fastup_theme(".$model->id.",3)'>静物</li>
        <br>
        <li class='ul_li' style='cursor:pointer;background-color:#63cc04;color:#fff' onclick='fastup_theme(".$model->id.",4)'>人物</li>
        <li class='ul_li' style='cursor:pointer;background-color:#00c0ef;color:#fff' onclick='fastup_theme(".$model->id.",5)'>植物</li>
        <li class='ul_li' style='cursor:pointer;background-color:#327ef1;color:#fff' onclick='fastup_theme(".$model->id.",6)'>建筑</li>
        <br>
        <li class='ul_li' style='cursor:pointer;background-color:#3a09ff;color:#fff' onclick='fastup_theme(".$model->id.",7)'>动物</li>
        <li class='ul_li' style='cursor:pointer;background-color:#9d00ff;color:#fff' onclick='fastup_theme(".$model->id.",8)'>书法</li>
        <li class='ul_li' style='cursor:pointer;background-color:#ff00a5;color:#fff' onclick='fastup_theme(".$model->id.",9)'>其他</li>
         <br>
        <li class='ul_li' style='cursor:pointer;background-color:#774242;color:#fff' onclick='fastup_theme(".$model->id.",10)'>机械</li>
        <li class='ul_li' style='cursor:pointer;background-color:#1b0000;color:#fff' onclick='fastup_theme(".$model->id.",11)'>宇宙</li>
    </ul>
</div>";
                        case $theme == 2: return "<div id='uptheme".$model['id']."'>
    <span id='uptheme1".$model['id']."'  data-placement='bottom' >风景</span>
    <ul>
        <li class='ul_li' style='cursor:pointer;background-color:red;color:#fff' onclick='fastup_theme(".$model->id.",1)'>抽象</li>
        <li class='ul_li' style='cursor:pointer;background-color:#ff9900;color:#fff' onclick='fastup_theme(".$model->id.",2)'>风景</li>
        <li class='ul_li' style='cursor:pointer;background-color:#949001;color:#fff' onclick='fastup_theme(".$model->id.",3)'>静物</li>
        <br>
        <li class='ul_li' style='cursor:pointer;background-color:#63cc04;color:#fff' onclick='fastup_theme(".$model->id.",4)'>人物</li>
        <li class='ul_li' style='cursor:pointer;background-color:#00c0ef;color:#fff' onclick='fastup_theme(".$model->id.",5)'>植物</li>
        <li class='ul_li' style='cursor:pointer;background-color:#327ef1;color:#fff' onclick='fastup_theme(".$model->id.",6)'>建筑</li>
        <br>
        <li class='ul_li' style='cursor:pointer;background-color:#3a09ff;color:#fff' onclick='fastup_theme(".$model->id.",7)'>动物</li>
        <li class='ul_li' style='cursor:pointer;background-color:#9d00ff;color:#fff' onclick='fastup_theme(".$model->id.",8)'>书法</li>
        <li class='ul_li' style='cursor:pointer;background-color:#ff00a5;color:#fff' onclick='fastup_theme(".$model->id.",9)'>其他</li>
         <br>
        <li class='ul_li' style='cursor:pointer;background-color:#774242;color:#fff' onclick='fastup_theme(".$model->id.",10)'>机械</li>
        <li class='ul_li' style='cursor:pointer;background-color:#1b0000;color:#fff' onclick='fastup_theme(".$model->id.",11)'>宇宙</li>
    </ul>
</div>";
                        case $theme == 3: return "
 <div id='uptheme".$model['id']."'>
    <span id='uptheme1".$model['id']."' data-placement='bottom' >静物</span>
    <ul>
        <li class='ul_li' style='cursor:pointer;background-color:red;color:#fff' onclick='fastup_theme(".$model->id.",1)'>抽象</li>
        <li class='ul_li' style='cursor:pointer;background-color:#ff9900;color:#fff' onclick='fastup_theme(".$model->id.",2)'>风景</li>
        <li class='ul_li' style='cursor:pointer;background-color:#949001;color:#fff' onclick='fastup_theme(".$model->id.",3)'>静物</li>
        <br>
        <li class='ul_li' style='cursor:pointer;background-color:#63cc04;color:#fff' onclick='fastup_theme(".$model->id.",4)'>人物</li>
        <li class='ul_li' style='cursor:pointer;background-color:#00c0ef;color:#fff' onclick='fastup_theme(".$model->id.",5)'>植物</li>
        <li class='ul_li' style='cursor:pointer;background-color:#327ef1;color:#fff' onclick='fastup_theme(".$model->id.",6)'>建筑</li>
        <br>
        <li class='ul_li' style='cursor:pointer;background-color:#3a09ff;color:#fff' onclick='fastup_theme(".$model->id.",7)'>动物</li>
        <li class='ul_li' style='cursor:pointer;background-color:#9d00ff;color:#fff' onclick='fastup_theme(".$model->id.",8)'>书法</li>
        <li class='ul_li' style='cursor:pointer;background-color:#ff00a5;color:#fff' onclick='fastup_theme(".$model->id.",9)'>其他</li>
         <br>
        <li class='ul_li' style='cursor:pointer;background-color:#774242;color:#fff' onclick='fastup_theme(".$model->id.",10)'>机械</li>
        <li class='ul_li' style='cursor:pointer;background-color:#1b0000;color:#fff' onclick='fastup_theme(".$model->id.",11)'>宇宙</li>
    </ul>
 </div>";
                        case $theme == 4: return "
<div id='uptheme".$model['id']."'>
    <span id='uptheme1".$model['id']."' data-placement='bottom' >人物</span>
    <ul>
        <li class='ul_li' style='cursor:pointer;background-color:red;color:#fff' onclick='fastup_theme(".$model->id.",1)'>抽象</li>
        <li class='ul_li' style='cursor:pointer;background-color:#ff9900;color:#fff' onclick='fastup_theme(".$model->id.",2)'>风景</li>
        <li class='ul_li' style='cursor:pointer;background-color:#949001;color:#fff' onclick='fastup_theme(".$model->id.",3)'>静物</li>
        <br>
        <li class='ul_li' style='cursor:pointer;background-color:#63cc04;color:#fff' onclick='fastup_theme(".$model->id.",4)'>人物</li>
        <li class='ul_li' style='cursor:pointer;background-color:#00c0ef;color:#fff' onclick='fastup_theme(".$model->id.",5)'>植物</li>
        <li class='ul_li' style='cursor:pointer;background-color:#327ef1;color:#fff' onclick='fastup_theme(".$model->id.",6)'>建筑</li>
        <br>
        <li class='ul_li' style='cursor:pointer;background-color:#3a09ff;color:#fff' onclick='fastup_theme(".$model->id.",7)'>动物</li>
        <li class='ul_li' style='cursor:pointer;background-color:#9d00ff;color:#fff' onclick='fastup_theme(".$model->id.",8)'>书法</li>
        <li class='ul_li' style='cursor:pointer;background-color:#ff00a5;color:#fff' onclick='fastup_theme(".$model->id.",9)'>其他</li>
         <br>
        <li class='ul_li' style='cursor:pointer;background-color:#774242;color:#fff' onclick='fastup_theme(".$model->id.",10)'>机械</li>
        <li class='ul_li' style='cursor:pointer;background-color:#1b0000;color:#fff' onclick='fastup_theme(".$model->id.",11)'>宇宙</li>
    </ul>
</div>";
                        case $theme == 5: return "
<div id='uptheme".$model['id']."'>
    <span id='uptheme1".$model['id']."' data-placement='bottom' >植物花卉</span>
    <ul>
        <li class='ul_li' style='cursor:pointer;background-color:red;color:#fff' onclick='fastup_theme(".$model->id.",1)'>抽象</li>
        <li class='ul_li' style='cursor:pointer;background-color:#ff9900;color:#fff' onclick='fastup_theme(".$model->id.",2)'>风景</li>
        <li class='ul_li' style='cursor:pointer;background-color:#949001;color:#fff' onclick='fastup_theme(".$model->id.",3)'>静物</li>
        <br>
        <li class='ul_li' style='cursor:pointer;background-color:#63cc04;color:#fff' onclick='fastup_theme(".$model->id.",4)'>人物</li>
        <li class='ul_li' style='cursor:pointer;background-color:#00c0ef;color:#fff' onclick='fastup_theme(".$model->id.",5)'>植物</li>
        <li class='ul_li' style='cursor:pointer;background-color:#327ef1;color:#fff' onclick='fastup_theme(".$model->id.",6)'>建筑</li>
        <br>
        <li class='ul_li' style='cursor:pointer;background-color:#3a09ff;color:#fff' onclick='fastup_theme(".$model->id.",7)'>动物</li>
        <li class='ul_li' style='cursor:pointer;background-color:#9d00ff;color:#fff' onclick='fastup_theme(".$model->id.",8)'>书法</li>
        <li class='ul_li' style='cursor:pointer;background-color:#ff00a5;color:#fff' onclick='fastup_theme(".$model->id.",9)'>其他</li>
         <br>
        <li class='ul_li' style='cursor:pointer;background-color:#774242;color:#fff' onclick='fastup_theme(".$model->id.",10)'>机械</li>
        <li class='ul_li' style='cursor:pointer;background-color:#1b0000;color:#fff' onclick='fastup_theme(".$model->id.",11)'>宇宙</li>
    </ul>
</div>";
                        case $theme == 6: return "
<div id='uptheme".$model['id']."'>
    <span id='uptheme1".$model['id']."' data-placement='bottom' >建筑场景</span>
    <ul>
        <li class='ul_li' style='cursor:pointer;background-color:red;color:#fff' onclick='fastup_theme(".$model->id.",1)'>抽象</li>
        <li class='ul_li' style='cursor:pointer;background-color:#ff9900;color:#fff' onclick='fastup_theme(".$model->id.",2)'>风景</li>
        <li class='ul_li' style='cursor:pointer;background-color:#949001;color:#fff' onclick='fastup_theme(".$model->id.",3)'>静物</li>
        <br>
        <li class='ul_li' style='cursor:pointer;background-color:#63cc04;color:#fff' onclick='fastup_theme(".$model->id.",4)'>人物</li>
        <li class='ul_li' style='cursor:pointer;background-color:#00c0ef;color:#fff' onclick='fastup_theme(".$model->id.",5)'>植物</li>
        <li class='ul_li' style='cursor:pointer;background-color:#327ef1;color:#fff' onclick='fastup_theme(".$model->id.",6)'>建筑</li>
        <br>
        <li class='ul_li' style='cursor:pointer;background-color:#3a09ff;color:#fff' onclick='fastup_theme(".$model->id.",7)'>动物</li>
        <li class='ul_li' style='cursor:pointer;background-color:#9d00ff;color:#fff' onclick='fastup_theme(".$model->id.",8)'>书法</li>
        <li class='ul_li' style='cursor:pointer;background-color:#ff00a5;color:#fff' onclick='fastup_theme(".$model->id.",9)'>其他</li>
         <br>
        <li class='ul_li' style='cursor:pointer;background-color:#774242;color:#fff' onclick='fastup_theme(".$model->id.",10)'>机械</li>
        <li class='ul_li' style='cursor:pointer;background-color:#1b0000;color:#fff' onclick='fastup_theme(".$model->id.",11)'>宇宙</li>
    </ul>
</div>";
                        case $theme == 7: return "
<div id='uptheme".$model['id']."'>
    <span id='uptheme1".$model['id']."' data-placement='bottom' >动物宠物</span>
    <ul>
        <li class='ul_li' style='cursor:pointer;background-color:red;color:#fff' onclick='fastup_theme(".$model->id.",1)'>抽象</li>
        <li class='ul_li' style='cursor:pointer;background-color:#ff9900;color:#fff' onclick='fastup_theme(".$model->id.",2)'>风景</li>
        <li class='ul_li' style='cursor:pointer;background-color:#949001;color:#fff' onclick='fastup_theme(".$model->id.",3)'>静物</li>
        <br>
        <li class='ul_li' style='cursor:pointer;background-color:#63cc04;color:#fff' onclick='fastup_theme(".$model->id.",4)'>人物</li>
        <li class='ul_li' style='cursor:pointer;background-color:#00c0ef;color:#fff' onclick='fastup_theme(".$model->id.",5)'>植物</li>
        <li class='ul_li' style='cursor:pointer;background-color:#327ef1;color:#fff' onclick='fastup_theme(".$model->id.",6)'>建筑</li>
        <br>
        <li class='ul_li' style='cursor:pointer;background-color:#3a09ff;color:#fff' onclick='fastup_theme(".$model->id.",7)'>动物</li>
        <li class='ul_li' style='cursor:pointer;background-color:#9d00ff;color:#fff' onclick='fastup_theme(".$model->id.",8)'>书法</li>
        <li class='ul_li' style='cursor:pointer;background-color:#ff00a5;color:#fff' onclick='fastup_theme(".$model->id.",9)'>其他</li>
         <br>
        <li class='ul_li' style='cursor:pointer;background-color:#774242;color:#fff' onclick='fastup_theme(".$model->id.",10)'>机械</li>
        <li class='ul_li' style='cursor:pointer;background-color:#1b0000;color:#fff' onclick='fastup_theme(".$model->id.",11)'>宇宙</li>
    </ul>
</div>";
                        case $theme == 8: return "
<div id='uptheme".$model['id']."'>
    <span id='uptheme1".$model['id']."' data-placement='bottom' >书法文字</span>
    <ul>
        <li class='ul_li' style='cursor:pointer;background-color:red;color:#fff' onclick='fastup_theme(".$model->id.",1)'>抽象</li>
        <li class='ul_li' style='cursor:pointer;background-color:#ff9900;color:#fff' onclick='fastup_theme(".$model->id.",2)'>风景</li>
        <li class='ul_li' style='cursor:pointer;background-color:#949001;color:#fff' onclick='fastup_theme(".$model->id.",3)'>静物</li>
        <br>
        <li class='ul_li' style='cursor:pointer;background-color:#63cc04;color:#fff' onclick='fastup_theme(".$model->id.",4)'>人物</li>
        <li class='ul_li' style='cursor:pointer;background-color:#00c0ef;color:#fff' onclick='fastup_theme(".$model->id.",5)'>植物</li>
        <li class='ul_li' style='cursor:pointer;background-color:#327ef1;color:#fff' onclick='fastup_theme(".$model->id.",6)'>建筑</li>
        <br>
        <li class='ul_li' style='cursor:pointer;background-color:#3a09ff;color:#fff' onclick='fastup_theme(".$model->id.",7)'>动物</li>
        <li class='ul_li' style='cursor:pointer;background-color:#9d00ff;color:#fff' onclick='fastup_theme(".$model->id.",8)'>书法</li>
        <li class='ul_li' style='cursor:pointer;background-color:#ff00a5;color:#fff' onclick='fastup_theme(".$model->id.",9)'>其他</li>
         <br>
        <li class='ul_li' style='cursor:pointer;background-color:#774242;color:#fff' onclick='fastup_theme(".$model->id.",10)'>机械</li>
        <li class='ul_li' style='cursor:pointer;background-color:#1b0000;color:#fff' onclick='fastup_theme(".$model->id.",11)'>宇宙</li>
    </ul>
</div>";
                        case $theme == 9: return "
<div id='uptheme".$model['id']."'>
    <span id='uptheme1".$model['id']."' data-placement='bottom' >其他</span>
    <ul>
        <li class='ul_li' style='cursor:pointer;background-color:red;color:#fff' onclick='fastup_theme(".$model->id.",1)'>抽象</li>
        <li class='ul_li' style='cursor:pointer;background-color:#ff9900;color:#fff' onclick='fastup_theme(".$model->id.",2)'>风景</li>
        <li class='ul_li' style='cursor:pointer;background-color:#949001;color:#fff' onclick='fastup_theme(".$model->id.",3)'>静物</li>
        <br>
        <li class='ul_li' style='cursor:pointer;background-color:#63cc04;color:#fff' onclick='fastup_theme(".$model->id.",4)'>人物</li>
        <li class='ul_li' style='cursor:pointer;background-color:#00c0ef;color:#fff' onclick='fastup_theme(".$model->id.",5)'>植物</li>
        <li class='ul_li' style='cursor:pointer;background-color:#327ef1;color:#fff' onclick='fastup_theme(".$model->id.",6)'>建筑</li>
        <br>
        <li class='ul_li' style='cursor:pointer;background-color:#3a09ff;color:#fff' onclick='fastup_theme(".$model->id.",7)'>动物</li>
        <li class='ul_li' style='cursor:pointer;background-color:#9d00ff;color:#fff' onclick='fastup_theme(".$model->id.",8)'>书法</li>
        <li class='ul_li' style='cursor:pointer;background-color:#ff00a5;color:#fff' onclick='fastup_theme(".$model->id.",9)'>其他</li>
         <br>
        <li class='ul_li' style='cursor:pointer;background-color:#774242;color:#fff' onclick='fastup_theme(".$model->id.",10)'>机械</li>
        <li class='ul_li' style='cursor:pointer;background-color:#1b0000;color:#fff' onclick='fastup_theme(".$model->id.",11)'>宇宙</li>
    </ul>
</div>";
                        case $theme == 10: return "
<div id='uptheme".$model['id']."'>
    <span id='uptheme1".$model['id']."' data-placement='bottom' >机械工具</span>
    <ul>
        <li class='ul_li' style='cursor:pointer;background-color:red;color:#fff' onclick='fastup_theme(".$model->id.",1)'>抽象</li>
        <li class='ul_li' style='cursor:pointer;background-color:#ff9900;color:#fff' onclick='fastup_theme(".$model->id.",2)'>风景</li>
        <li class='ul_li' style='cursor:pointer;background-color:#949001;color:#fff' onclick='fastup_theme(".$model->id.",3)'>静物</li>
        <br>
        <li class='ul_li' style='cursor:pointer;background-color:#63cc04;color:#fff' onclick='fastup_theme(".$model->id.",4)'>人物</li>
        <li class='ul_li' style='cursor:pointer;background-color:#00c0ef;color:#fff' onclick='fastup_theme(".$model->id.",5)'>植物</li>
        <li class='ul_li' style='cursor:pointer;background-color:#327ef1;color:#fff' onclick='fastup_theme(".$model->id.",6)'>建筑</li>
        <br>
        <li class='ul_li' style='cursor:pointer;background-color:#3a09ff;color:#fff' onclick='fastup_theme(".$model->id.",7)'>动物</li>
        <li class='ul_li' style='cursor:pointer;background-color:#9d00ff;color:#fff' onclick='fastup_theme(".$model->id.",8)'>书法</li>
        <li class='ul_li' style='cursor:pointer;background-color:#ff00a5;color:#fff' onclick='fastup_theme(".$model->id.",9)'>其他</li>
         <br>
        <li class='ul_li' style='cursor:pointer;background-color:#774242;color:#fff' onclick='fastup_theme(".$model->id.",10)'>机械</li>
        <li class='ul_li' style='cursor:pointer;background-color:#1b0000;color:#fff' onclick='fastup_theme(".$model->id.",11)'>宇宙</li>
    </ul>
</div>";
                        case $theme == 11: return "
<div id='uptheme".$model['id']."'>
    <span id='uptheme1".$model['id']."' data-placement='bottom' >宇宙星空</span>
    <ul>
        <li class='ul_li' style='cursor:pointer;background-color:red;color:#fff' onclick='fastup_theme(".$model->id.",1)'>抽象</li>
        <li class='ul_li' style='cursor:pointer;background-color:#ff9900;color:#fff' onclick='fastup_theme(".$model->id.",2)'>风景</li>
        <li class='ul_li' style='cursor:pointer;background-color:#949001;color:#fff' onclick='fastup_theme(".$model->id.",3)'>静物</li>
        <br>
        <li class='ul_li' style='cursor:pointer;background-color:#63cc04;color:#fff' onclick='fastup_theme(".$model->id.",4)'>人物</li>
        <li class='ul_li' style='cursor:pointer;background-color:#00c0ef;color:#fff' onclick='fastup_theme(".$model->id.",5)'>植物</li>
        <li class='ul_li' style='cursor:pointer;background-color:#327ef1;color:#fff' onclick='fastup_theme(".$model->id.",6)'>建筑</li>
        <br>
        <li class='ul_li' style='cursor:pointer;background-color:#3a09ff;color:#fff' onclick='fastup_theme(".$model->id.",7)'>动物</li>
        <li class='ul_li' style='cursor:pointer;background-color:#9d00ff;color:#fff' onclick='fastup_theme(".$model->id.",8)'>书法</li>
        <li class='ul_li' style='cursor:pointer;background-color:#ff00a5;color:#fff' onclick='fastup_theme(".$model->id.",9)'>其他</li>
         <br>
        <li class='ul_li' style='cursor:pointer;background-color:#774242;color:#fff' onclick='fastup_theme(".$model->id.",10)'>机械</li>
        <li class='ul_li' style='cursor:pointer;background-color:#1b0000;color:#fff' onclick='fastup_theme(".$model->id.",11)'>宇宙</li>
    </ul>
</div>";
                        case $theme == 999: return "
<div id='uptheme".$model['id']."'>
    <span id='uptheme1".$model['id']."' data-placement='bottom' style='color:red'>未定义</span>
    <ul>
        <li class='ul_li' style='cursor:pointer;background-color:red;color:#fff' onclick='fastup_theme(".$model->id.",1)'>抽象</li>
        <li class='ul_li' style='cursor:pointer;background-color:#ff9900;color:#fff' onclick='fastup_theme(".$model->id.",2)'>风景</li>
        <li class='ul_li' style='cursor:pointer;background-color:#949001;color:#fff' onclick='fastup_theme(".$model->id.",3)'>静物</li>
        <br>
        <li class='ul_li' style='cursor:pointer;background-color:#63cc04;color:#fff' onclick='fastup_theme(".$model->id.",4)'>人物</li>
        <li class='ul_li' style='cursor:pointer;background-color:#00c0ef;color:#fff' onclick='fastup_theme(".$model->id.",5)'>植物</li>
        <li class='ul_li' style='cursor:pointer;background-color:#327ef1;color:#fff' onclick='fastup_theme(".$model->id.",6)'>建筑</li>
        <br>
        <li class='ul_li' style='cursor:pointer;background-color:#3a09ff;color:#fff' onclick='fastup_theme(".$model->id.",7)'>动物</li>
        <li class='ul_li' style='cursor:pointer;background-color:#9d00ff;color:#fff' onclick='fastup_theme(".$model->id.",8)'>书法</li>
        <li class='ul_li' style='cursor:pointer;background-color:#ff00a5;color:#fff' onclick='fastup_theme(".$model->id.",9)'>其他</li>
         <br>
        <li class='ul_li' style='cursor:pointer;background-color:#774242;color:#fff' onclick='fastup_theme(".$model->id.",10)'>机械</li>
        <li class='ul_li' style='cursor:pointer;background-color:#1b0000;color:#fff' onclick='fastup_theme(".$model->id.",11)'>宇宙</li>
    </ul>
</div>";
                    }
                }
            ],
            //创作时间
            [
                'attribute'=>'time',
                'format' => 'text',
                'content'=>function($model){
                    if(!$model->time){$model->time = "未设置";};
                    if($model->time == "未设置"){
                        return "<span id='uptime".$model['id']."' title='双击修改' style='cursor:pointer;color:red' ondblclick='update_time(".$model->id.")' date_id='".$model['id']."' data-toggle='tooltip' data-placement='bottom' >".$model->time."</span>";
                    }else{
                        return "<span id='uptime".$model['id']."' title='双击修改' style='cursor:pointer;color:black' ondblclick='update_time(".$model->id.")' date_id='".$model['id']."' data-toggle='tooltip' data-placement='bottom' >".$model->time."</span>";
                    }
                },
            ],
            //颜色
            [
                'attribute'=>'color',
                'format' => 'text',
                'contentOptions' => [
                    'width'=>'150px'
                ],
                'content'=>function($model){
                    switch($model){
                        case $model->color==1 :return "<div style='border:solid 2px #000;background-color:rgb(255,0,0);width:60px;height:20px;' id='upcolor".$model['id']."' data_id='1' 红>
<br>
<ul>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='1' style='border:1px solid #ccc;background-color:rgb(255,0,0)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='2' style='border:1px solid #ccc;background-color:rgb(255,150,0)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='3' style='border:1px solid #ccc;background-color:rgb(255,255,0)'></li>
    <br>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='4' style='border:1px solid #ccc;background-color:rgb(0,255,0)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='5' style='border:1px solid #ccc;background-color:rgb(0,255,255)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='6' style='border:1px solid #ccc;background-color:rgb(0,0,255)'></li>
    <br>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='7' style='border:1px solid #ccc;background-color:rgb(100,50,150)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='8' style='border:1px solid #ccc;background-color:rgb(255,150,255)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='9' style='border:1px solid #ccc;background-color:rgb(255,255,255)'></li>
    <br>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='10' style='border:1px solid #ccc;background-color:rgb(0,0,0)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='11' style='border:1px solid #ccc;background-color:rgb(120,120,120)'></li>
</ul>
</div>";break;
                        case $model->color==2 :return "<div style='border:solid 2px #000;background-color:rgb(255,150,0);width:60px;height:20px;' id='upcolor".$model['id']."' 橙>
<br>
<ul>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='1' style='border:1px solid #ccc;background-color:rgb(255,0,0)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='2' style='border:1px solid #ccc;background-color:rgb(255,150,0)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='3' style='border:1px solid #ccc;background-color:rgb(255,255,0)'></li>
    <br>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='4' style='border:1px solid #ccc;background-color:rgb(0,255,0)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='5' style='border:1px solid #ccc;background-color:rgb(0,255,255)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='6' style='border:1px solid #ccc;background-color:rgb(0,0,255)'></li>
    <br>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='7' style='border:1px solid #ccc;background-color:rgb(100,50,150)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='8' style='border:1px solid #ccc;background-color:rgb(255,150,255)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='9' style='border:1px solid #ccc;background-color:rgb(255,255,255)'></li>
    <br>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='10' style='border:1px solid #ccc;background-color:rgb(0,0,0)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='11' style='border:1px solid #ccc;background-color:rgb(120,120,120)'></li>
</ul>
</div>";break;
                        case $model->color==3 :return "<div style='border:solid 2px #000;background-color:rgb(255,255,0);width:60px;height:20px;' id='upcolor".$model['id']."' 黄>
<br>
<ul>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='1' style='border:1px solid #ccc;background-color:rgb(255,0,0)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='2' style='border:1px solid #ccc;background-color:rgb(255,150,0)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='3' style='border:1px solid #ccc;background-color:rgb(255,255,0)'></li>
    <br>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='4' style='border:1px solid #ccc;background-color:rgb(0,255,0)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='5' style='border:1px solid #ccc;background-color:rgb(0,255,255)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='6' style='border:1px solid #ccc;background-color:rgb(0,0,255)'></li>
    <br>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='7' style='border:1px solid #ccc;background-color:rgb(100,50,150)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='8' style='border:1px solid #ccc;background-color:rgb(255,150,255)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='9' style='border:1px solid #ccc;background-color:rgb(255,255,255)'></li>
    <br>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='10' style='border:1px solid #ccc;background-color:rgb(0,0,0)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='11' style='border:1px solid #ccc;background-color:rgb(120,120,120)'></li>
</ul>
</div>";break;
                        case $model->color==4 :return "<div style='border:solid 2px #000;background-color:rgb(0,255,0);width:60px;height:20px;' id='upcolor".$model['id']."' 绿>
<br>
<ul>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='1' style='border:1px solid #ccc;background-color:rgb(255,0,0)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='2' style='border:1px solid #ccc;background-color:rgb(255,150,0)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='3' style='border:1px solid #ccc;background-color:rgb(255,255,0)'></li>
    <br>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='4' style='border:1px solid #ccc;background-color:rgb(0,255,0)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='5' style='border:1px solid #ccc;background-color:rgb(0,255,255)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='6' style='border:1px solid #ccc;background-color:rgb(0,0,255)'></li>
    <br>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='7' style='border:1px solid #ccc;background-color:rgb(100,50,150)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='8' style='border:1px solid #ccc;background-color:rgb(255,150,255)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='9' style='border:1px solid #ccc;background-color:rgb(255,255,255)'></li>
    <br>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='10' style='border:1px solid #ccc;background-color:rgb(0,0,0)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='11' style='border:1px solid #ccc;background-color:rgb(120,120,120)'></li>
</ul>
</div>";break;
                        case $model->color==5 :return "<div style='border:solid 2px #000;background-color:rgb(0,255,255);width:60px;height:20px;' id='upcolor".$model['id']."' 青>
<br>
<ul>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='1' style='border:1px solid #ccc;background-color:rgb(255,0,0)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='2' style='border:1px solid #ccc;background-color:rgb(255,150,0)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='3' style='border:1px solid #ccc;background-color:rgb(255,255,0)'></li>
    <br>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='4' style='border:1px solid #ccc;background-color:rgb(0,255,0)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='5' style='border:1px solid #ccc;background-color:rgb(0,255,255)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='6' style='border:1px solid #ccc;background-color:rgb(0,0,255)'></li>
    <br>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='7' style='border:1px solid #ccc;background-color:rgb(100,50,150)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='8' style='border:1px solid #ccc;background-color:rgb(255,150,255)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='9' style='border:1px solid #ccc;background-color:rgb(255,255,255)'></li>
    <br>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='10' style='border:1px solid #ccc;background-color:rgb(0,0,0)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='11' style='border:1px solid #ccc;background-color:rgb(120,120,120)'></li>
</ul>
</div>";break;
                        case $model->color==6 :return "<div style='border:solid 2px #000;background-color:rgb(0,0,255);width:60px;height:20px;' id='upcolor".$model['id']."' 蓝>
<br>
<ul>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='1' style='border:1px solid #ccc;background-color:rgb(255,0,0)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='2' style='border:1px solid #ccc;background-color:rgb(255,150,0)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='3' style='border:1px solid #ccc;background-color:rgb(255,255,0)'></li>
    <br>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='4' style='border:1px solid #ccc;background-color:rgb(0,255,0)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='5' style='border:1px solid #ccc;background-color:rgb(0,255,255)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='6' style='border:1px solid #ccc;background-color:rgb(0,0,255)'></li>
    <br>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='7' style='border:1px solid #ccc;background-color:rgb(100,50,150)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='8' style='border:1px solid #ccc;background-color:rgb(255,150,255)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='9' style='border:1px solid #ccc;background-color:rgb(255,255,255)'></li>
    <br>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='10' style='border:1px solid #ccc;background-color:rgb(0,0,0)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='11' style='border:1px solid #ccc;background-color:rgb(120,120,120)'></li>
</ul>
</div>";break;
                        case $model->color==7 :return "<div style='border:solid 2px #000;background-color:rgb(100,50,150);width:60px;height:20px;' id='upcolor".$model['id']."' 紫>
<br>
<ul>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='1' style='border:1px solid #ccc;background-color:rgb(255,0,0)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='2' style='border:1px solid #ccc;background-color:rgb(255,150,0)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='3' style='border:1px solid #ccc;background-color:rgb(255,255,0)'></li>
    <br>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='4' style='border:1px solid #ccc;background-color:rgb(0,255,0)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='5' style='border:1px solid #ccc;background-color:rgb(0,255,255)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='6' style='border:1px solid #ccc;background-color:rgb(0,0,255)'></li>
    <br>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='7' style='border:1px solid #ccc;background-color:rgb(100,50,150)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='8' style='border:1px solid #ccc;background-color:rgb(255,150,255)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='9' style='border:1px solid #ccc;background-color:rgb(255,255,255)'></li>
    <br>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='10' style='border:1px solid #ccc;background-color:rgb(0,0,0)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='11' style='border:1px solid #ccc;background-color:rgb(120,120,120)'></li>
</ul>
</div>";break;
                        case $model->color==8 :return "<div style='border:solid 2px #000;background-color:rgb(255,150,255);width:60px;height:20px;' id='upcolor".$model['id']."' 粉>
<br>
<ul>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='1' style='border:1px solid #ccc;background-color:rgb(255,0,0)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='2' style='border:1px solid #ccc;background-color:rgb(255,150,0)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='3' style='border:1px solid #ccc;background-color:rgb(255,255,0)'></li>
    <br>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='4' style='border:1px solid #ccc;background-color:rgb(0,255,0)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='5' style='border:1px solid #ccc;background-color:rgb(0,255,255)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='6' style='border:1px solid #ccc;background-color:rgb(0,0,255)'></li>
    <br>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='7' style='border:1px solid #ccc;background-color:rgb(100,50,150)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='8' style='border:1px solid #ccc;background-color:rgb(255,150,255)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='9' style='border:1px solid #ccc;background-color:rgb(255,255,255)'></li>
    <br>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='10' style='border:1px solid #ccc;background-color:rgb(0,0,0)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='11' style='border:1px solid #ccc;background-color:rgb(120,120,120)'></li>
</ul>
</div>";break;
                        case $model->color==9 :return "<div style='border:solid 2px #000;background-color:rgb(255,255,255);width:60px;height:20px;' id='upcolor".$model['id']."' 白>
<br>
<ul>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='1' style='border:1px solid #ccc;background-color:rgb(255,0,0)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='2' style='border:1px solid #ccc;background-color:rgb(255,150,0)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='3' style='border:1px solid #ccc;background-color:rgb(255,255,0)'></li>
    <br>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='4' style='border:1px solid #ccc;background-color:rgb(0,255,0)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='5' style='border:1px solid #ccc;background-color:rgb(0,255,255)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='6' style='border:1px solid #ccc;background-color:rgb(0,0,255)'></li>
    <br>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='7' style='border:1px solid #ccc;background-color:rgb(100,50,150)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='8' style='border:1px solid #ccc;background-color:rgb(255,150,255)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='9' style='border:1px solid #ccc;background-color:rgb(255,255,255)'></li>
    <br>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='10' style='border:1px solid #ccc;background-color:rgb(0,0,0)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='11' style='border:1px solid #ccc;background-color:rgb(120,120,120)'></li>
</ul>
</div>";break;
                        case $model->color==10 :return "<div style='border:solid 2px #000;background-color:rgb(0,0,0);width:60px;height:20px;' id='upcolor".$model['id']."' 黑>
<br>
<ul>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='1' style='border:1px solid #ccc;background-color:rgb(255,0,0)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='2' style='border:1px solid #ccc;background-color:rgb(255,150,0)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='3' style='border:1px solid #ccc;background-color:rgb(255,255,0)'></li>
    <br>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='4' style='border:1px solid #ccc;background-color:rgb(0,255,0)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='5' style='border:1px solid #ccc;background-color:rgb(0,255,255)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='6' style='border:1px solid #ccc;background-color:rgb(0,0,255)'></li>
    <br>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='7' style='border:1px solid #ccc;background-color:rgb(100,50,150)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='8' style='border:1px solid #ccc;background-color:rgb(255,150,255)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='9' style='border:1px solid #ccc;background-color:rgb(255,255,255)'></li>
    <br>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='10' style='border:1px solid #ccc;background-color:rgb(0,0,0)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='11' style='border:1px solid #ccc;background-color:rgb(120,120,120)'></li>
</ul>
</div>";break;
                        case $model->color==11 :return "<div style='border:solid 2px #000;background-color:rgb(120,120,120);width:60px;height:20px;' id='upcolor".$model['id']."' 其他>
<br>
<ul>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='1' style='border:1px solid #ccc;background-color:rgb(255,0,0)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='2' style='border:1px solid #ccc;background-color:rgb(255,150,0)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='3' style='border:1px solid #ccc;background-color:rgb(255,255,0)'></li>
    <br>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='4' style='border:1px solid #ccc;background-color:rgb(0,255,0)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='5' style='border:1px solid #ccc;background-color:rgb(0,255,255)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='6' style='border:1px solid #ccc;background-color:rgb(0,0,255)'></li>
    <br>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='7' style='border:1px solid #ccc;background-color:rgb(100,50,150)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='8' style='border:1px solid #ccc;background-color:rgb(255,150,255)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='9' style='border:1px solid #ccc;background-color:rgb(255,255,255)'></li>
    <br>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='10' style='border:1px solid #ccc;background-color:rgb(0,0,0)'></li>
    <li class='ul_li btn-success' onclick='update_color1(".$model->id.",event)' data_id='11' style='border:1px solid #ccc;background-color:rgb(120,120,120)'></li>
</ul>
</div>";break;
                    }
                },
                'filter' => ['1'=>'', '2'=>'', '3'=>'', '4'=>'', '5'=>'', '6'=>'', '7'=>'', '8'=>'', '9'=>'', '10'=>'' ,'11'=>''],
            ],
            //最大高度
            [
                'attribute'=>'max_length',
                'format' => 'text',
                'content'=>function($model){
                    if(!$model->max_length){$model->max_length = "未设置";};
                    if($model->max_length == "未设置"){
                        return "<span id='upmax_length".$model['id']."' title='双击修改' style='cursor:pointer;color:red' ondblclick='update_max_length(".$model->id.")' date_id='".$model['id']."' data-toggle='tooltip' data-placement='bottom' >".$model->max_length."</span>";
                    }else{
                        return "<span id='upmax_length".$model['id']."' title='双击修改' style='cursor:pointer;color:black' ondblclick='update_max_length(".$model->id.")' date_id='".$model['id']."' data-toggle='tooltip' data-placement='bottom' >".$model->max_length."</span>";
                    }
                },
            ],
            //最大宽度
            [
                'attribute'=>'max_width',
                'format' => 'text',
                'content'=>function($model){
                    if(!$model->max_width){$model->max_width = "未设置";};
                    if($model->max_width == "未设置"){
                        return "<span id='upmax_width".$model['id']."' title='双击修改' style='cursor:pointer;color:red' ondblclick='update_max_width(".$model->id.")' date_id='".$model['id']."' data-toggle='tooltip' data-placement='bottom' >".$model->max_width."</span>";
                    }else{
                        return "<span id='upmax_width".$model['id']."' title='双击修改' style='cursor:pointer;color:black' ondblclick='update_max_width(".$model->id.")' date_id='".$model['id']."' data-toggle='tooltip' data-placement='bottom' >".$model->max_width."</span>";
                    }
                },
            ],
            //溢价指数
            [
                'attribute'=>'premium',
                'format' => 'text',
                'content'=>function($model){
                    if(!$model->premium){$model->premium = 1;};
                        return "<span id='uppremium".$model['id']."' title='双击修改' style='cursor:pointer;' ondblclick='update_premium(".$model->id.")' date_id='".$model['id']."' data-toggle='tooltip' data-placement='bottom' >".$model->premium."</span>";
                },
            ],
            //描述
            [
                'attribute'=>'content',
                'format' => 'text',
                'content'=>function($model){
                    if(!$model->content){$model->content = "暂无描述";};
                    if($model->content == "暂无描述"){
                        return "<div id='upcontent".$model['id']."' class='text_style' title='双击修改' style='cursor:pointer;color:red;' ondblclick='update_content(".$model->id.")' date_id='".$model['id']."'>".$model->content."</div>";
                    }else{
                        return "<div id='upcontent".$model['id']."' class='text_style' title='双击修改' style='cursor:pointer;color:black;' ondblclick='update_content(".$model->id.")' date_id='".$model['id']."'>".$model->content."</div>";
                    }
                },
                'contentOptions' => [
                    'width'=>'100'
                ],
            ],
            //标签
            [
                'attribute'=>'label',
                'format' => 'text',
                'content'=>function($model){
                        return "<span id='label".$model['id']."' data_id='".$model->id."' style='left:40%;cursor:pointe;' class='btn btn-info label_view' title='点击查看和修改标签' onclick='select_label(".$model->id.")' data-toggle='modal' data-target='#labelModal'>点击查看</span>";
                },
                'contentOptions' => [
                    'width'=>'50px'
                ],
            ],
            //状态
            [
                'attribute' => 'is_appear',
                'format' => 'text',
                'content' => function($model){
                    if($model['is_appear'] == 1){
                        return "<button id='is_appear".$model['id']."' style='cursor:pointer'  class='is_appear btn btn-success' date_id='".$model['id']."' status='".$model['is_appear']."' data-toggle='tooltip' data-placement='bottom' title='点击修改状态'>显示</button>";
                    }else{
                        return "<button id='is_appear".$model['id']."' style='cursor:pointer' class='is_appear btn btn-inverse' date_id='".$model['id']."' status='".$model['is_appear']."' data-toggle='tooltip' data-placement='bottom' title='点击修改状态'>隐藏</button>";
                    }
                },
            ],
            //是否为登录图
            [
                'attribute' => 'is_login',
                'format' => 'text',
                'content' => function($model){
                    if($model['is_login'] == 1){
                        return "<button id='is_login".$model['id']."' style='cursor:pointer'  class='is_login btn btn-info ' date_id='".$model['id']."' status='".$model['is_login']."' data-toggle='tooltip' data-placement='bottom' title='点击修改状态'>是</button>";
                    }else{
                        return "<button id='is_login".$model['id']."' style='cursor:pointer' class='is_login btn btn-warning' date_id='".$model['id']."' status='".$model['is_login']."' data-toggle='tooltip' data-placement='bottom' title='点击修改状态'>否</button>";
                    }
                },
            ],
            //推荐
            [
                'attribute' => 'is_recommend',
                'format' => 'text',
                'content' => function($model){
                    if($model['is_recommend'] == 1){
                        return "<button id='is_recommend".$model['id']."' style='cursor:pointer'  class='is_recommend btn btn-info ' date_id='".$model['id']."' status='".$model['is_recommend']."' data-toggle='tooltip' data-placement='bottom' title='点击修改状态'>推荐</button>";
                    }else{
                        return "<button id='is_recommend".$model['id']."' style='cursor:pointer' class='is_recommend btn btn-warning' date_id='".$model['id']."' status='".$model['is_recommend']."' data-toggle='tooltip' data-placement='bottom' title='点击修改状态'>不推荐</button>";
                    }
                },
            ],
//            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
<script src="https://unpkg.com/qiniu-js@2.2.2/dist/qiniu.min.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.9.1.js"></script>
<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<script type="text/javascript">
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    })
    window.onload=function (){
        $("select[name='GoodsSearch[color]'] option[value='1']").css('background','rgb(255,0,0)');
        $("select[name='GoodsSearch[color]'] option[value='2']").css('background','rgb(255,150,0)');
        $("select[name='GoodsSearch[color]'] option[value='3']").css('background','rgb(255,255,0)');
        $("select[name='GoodsSearch[color]'] option[value='4']").css('background','rgb(0,255,0)');
        $("select[name='GoodsSearch[color]'] option[value='5']").css('background','rgb(0,255,255)');
        $("select[name='GoodsSearch[color]'] option[value='6']").css('background','rgb(0,0,255)');
        $("select[name='GoodsSearch[color]'] option[value='7']").css('background','rgb(100,50,150)');
        $("select[name='GoodsSearch[color]'] option[value='8']").css('background','rgb(255,150,255)');
        $("select[name='GoodsSearch[color]'] option[value='9']").css('background','rgb(255,255,255)');
        $("select[name='GoodsSearch[color]'] option[value='10']").css('background','rgb(0,0,0)');
        $("select[name='GoodsSearch[color]'] option[value='11']").css('background','rgb(120,120,120)');
    }
    toastr.options = {
        closeButton: true,
        debug: false,
        positionClass: "toast-top-center",
        onclick: null,
        showDuration: "300",
        hideDuration: "1000",
        timeOut: "1500",
        progressBar: false,
        showEasing: "swing",
        hideEasing: "linear",
        showMethod: "fadeIn",
        hideMethod: "fadeOut"
    };
    function modal_show(){
        $("#myModal").modal('show')
        var el = document.getElementById('create_info');
        var childs = el.childNodes;
        for(var k = childs .length - 1; k >= 0; k--) {
            el.removeChild(childs[k]);
        }
        $("#goods-image").val('');
    }
    //修改状态
    $('.is_appear').click(function(){
        $('[data-toggle="tooltip"]').tooltip('destroy');
        var date = {
            'id': $(this).attr("date_id"),
            'is_appear': $(this).attr("status")
        };
        $.ajax({
            url:"/goods/status",
            type:"post",
            data: date,
            success:function(msg){
                var num = parseInt(msg);
                var button = document.getElementById("is_appear"+num);
                var status = button.getAttribute('status');
                if(status == 0){
                    button.setAttribute('status','1');
                    button.setAttribute('data-toggle','tooltip');
                    button.setAttribute('data-placement','bottom');
                    $('[data-toggle="tooltip"]').tooltip();
                    button.setAttribute('class','btn btn-success');
                    button.innerText = '显示';
                }else{
                    button.setAttribute('status','0');
                    button.setAttribute('data-toggle','tooltip');
                    button.setAttribute('data-placement','bottom');
                    $('[data-toggle="tooltip"]').tooltip();
                    button.setAttribute('class','btn btn-inverse');
                    button.innerText = '隐藏';
                }
            },
            error:function(data){
                alert('修改失败');
            }
        });
    });
    //是否推荐
    $('.is_recommend').click(function(){
        $('[data-toggle="tooltip"]').tooltip('destroy');
        var date = {
            'id': $(this).attr("date_id"),
            'is_recommend': $(this).attr("status")
        };
        $.ajax({
            url:"/goods/recommend",
            type:"post",
            data: date,
            success:function(msg){
                var num = parseInt(msg);
                var button = document.getElementById("is_recommend"+num);
                var status = button.getAttribute('status');
                if(status == 0){
                    button.setAttribute('status','1');
                    button.setAttribute('class','btn btn-info');
                    button.setAttribute('data-toggle','tooltip');
                    button.setAttribute('data-placement','bottom');
                    $('[data-toggle="tooltip"]').tooltip();
                    button.innerText = '推荐';
                }else{
                    button.setAttribute('status','0');
                    button.setAttribute('class','btn btn-warning');
                    button.setAttribute('data-toggle','tooltip');
                    button.setAttribute('data-placement','bottom');
                    $('[data-toggle="tooltip"]').tooltip();
                    button.innerText = '不推荐';
                }
            },
            error:function(data){
                alert('修改失败');
            }
        });
    });
    //是否为登陆图
    $('.is_login').click(function(){
        $('[data-toggle="tooltip"]').tooltip('destroy');
        var date = {
            'id': $(this).attr("date_id"),
            'is_login': $(this).attr("status")
        };
        $.ajax({
            url:"/goods/islogin",
            type:"post",
            data: date,
            success:function(msg){
                var num = parseInt(msg);
                var button = document.getElementById("is_login"+num);
                var status = button.getAttribute('status');
                if(status == 0){
                    button.setAttribute('status','1');
                    button.setAttribute('class','btn btn-info');
                    button.setAttribute('data-toggle','tooltip');
                    button.setAttribute('data-placement','bottom');
                    $('[data-toggle="tooltip"]').tooltip();
                    button.innerText = '是';
                }else{
                    button.setAttribute('status','0');
                    button.setAttribute('class','btn btn-warning');
                    button.setAttribute('data-toggle','tooltip');
                    button.setAttribute('data-placement','bottom');
                    $('[data-toggle="tooltip"]').tooltip();
                    button.innerText = '否';
                }
            },
            error:function(data){
                alert('修改失败');
            }
        });
    });
    //修改名称
    function update_name(id){
        $('[data-toggle="tooltip"]').tooltip('destroy');
        var obj = document.getElementById("upname"+id);
        var Newobj = document.createElement('input');
        Newobj.value=obj.innerText;
        Newobj.setAttribute("type","input");
        Newobj.setAttribute("id","upname"+id);
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
            if(e.keyCode == 13){
                $.ajax({
                    url:"/goods/updatename",
                    type:"post",
                    data: date,
                    dataType: 'json',
                    success:function(msg){
                        var txt = document.getElementById("upname"+id);
                        var Newtext = document.createElement('span');
                        Newtext.setAttribute("type","span");
                        Newtext.setAttribute("id",'upname'+msg.id);
                        Newtext.setAttribute("value",Newobj.value);
                        Newtext.setAttribute('data-toggle','tooltip');
                        Newtext.setAttribute('data-placement','bottom');
                        Newtext.setAttribute("ondblclick", "update_name("+msg.id+")");
                        Newtext.style = 'cursor:pointer';
                        Newtext.innerHTML = Newobj.value;
                        Newtext.style = 'max-width:150px';
                        Newtext.style = 'white-space: pre-line';
                        Newtext.style.color = '#FF830F';
                        Newtext.title = '双击修改';
                        txt.parentNode.appendChild(Newtext);
                        txt.parentNode.removeChild(txt);
                        $('[data-toggle="tooltip"]').tooltip();
                    },
                    error:function(data){
                        alert('修改失败,商品名称不能为空');
                        location.reload();
                    }
                });
            }
        });
    };
    //修改作者
    function update_author(id){
        $('[data-toggle="tooltip"]').tooltip('destroy');
        var obj1 = document.getElementById("upauthor"+id);
        var Newobj1 = document.createElement('input');
        Newobj1.value=obj1.innerText;
        Newobj1.setAttribute("type","input");
        Newobj1.setAttribute("id","upauthor"+id);
        Newobj1.setAttribute("author",obj1.innerText);
        Newobj1.setAttribute("value",obj1.innerText);
        Newobj1.style = 'max-width:100px';
        obj1.parentNode.appendChild(Newobj1);
        obj1.parentNode.removeChild(obj1);
        addEventListener("keypress", function (e) {
            var date = {
                'id': id,
                'author': Newobj1.value,
            };
            if(e.keyCode == 13){
                $.ajax({
                    url:"/goods/updateauthor",
                    type:"post",
                    data: date,
                    dataType: 'json',
                    success:function(msg){
                        var txt1 = document.getElementById("upauthor"+id);
                        var Newtext1 = document.createElement('span');
                        Newtext1.setAttribute("type","span");
                        Newtext1.setAttribute("id",'upauthor'+msg.id);
                        Newtext1.setAttribute("value",Newobj1.value);
                        Newtext1.setAttribute('data-toggle','tooltip');
                        Newtext1.setAttribute('data-placement','bottom');
                        Newtext1.setAttribute("ondblclick", "update_author("+msg.id+")");
                        Newtext1.style = 'cursor:pointer';
                        Newtext1.title = '双击修改';
                        if(Newobj1.value == ""){
                            console.log(Newobj1.value);
                            Newtext1.innerHTML = "待添加";
                        }else{
                            Newtext1.innerHTML = Newobj1.value;
                            console.log(Newobj1.value);
                        }
                        Newobj1.value == "待添加" || Newobj1.value == "" ? Newtext1.style.color = "red" : Newtext1.style.color = "black";
                        txt1.parentNode.appendChild(Newtext1);
                        txt1.parentNode.removeChild(txt1);
                        $('[data-toggle="tooltip"]').tooltip();
                    },
                    error:function(data){
                        alert('修改失败');
                        location.reload();
                    }
                });
            }
        });
    };
    //修改分类
    function update_category(id){
        $('[data-toggle="tooltip"]').tooltip('destroy');
        var obj3 = document.getElementById("upcategory1"+id);
        var ul_li = $("#upcategory"+id+" ul");
        var Newobj3 = document.createElement('select');
        var Nullobj = document.createElement('option');
        Nullobj.value = " ";
        Nullobj.innerHTML = "请选择";
        $.ajax({
            url:"/category/selectcategory",
            type:"post",
            dataType: 'json',
            success:function(msg){
                for(var i = 0;i<msg.length;i++){
                    // if(msg[i].pid == 0){
                        var option = document.createElement('option');
                        option.value = msg[i].id;
                        option.name = msg[i].category_name;
                        option.innerHTML = msg[i].category_name;
                        Newobj3.appendChild(option);
                        // category2(msg);
                    // }
                }
            },
            error:function(data){
                alert('修改失败');
                location.reload();
            }
        });
        Newobj3.value=obj3.innerText;
        Newobj3.setAttribute("id","upcategory1"+id);
        Newobj3.setAttribute("category",obj3.innerText);
        Newobj3.setAttribute("value",obj3.innerText);
        Newobj3.setAttribute("onchange", "update_category1("+id+")");
        Newobj3.appendChild(Nullobj);
        obj3.parentNode.insertBefore(Newobj3,ul_li['0']);
        // obj3.parentNode.appendChild(Newobj3);
        obj3.parentNode.removeChild(obj3);
    }
    //修改分类
    function update_category1(id){
        var pid = document.getElementById("upcategory1"+id).value;
        var data = {
            'id' : id,
            'pid' : pid
        };
        $.ajax({
            url:"/goods/updatecategory",
            type:"post",
            data:data,
            dataType: 'json',
            success:function(msg){
                var name = msg.category_name;
                var select = document.getElementById("upcategory"+msg.id);
                var select1 = document.getElementById("upcategory1"+msg.id);
                var ul_li = $("#upcategory"+msg.id+" ul");
                var span_category = document.createElement('span');
                span_category.setAttribute("type","span");
                span_category.setAttribute("value",name);
                span_category.innerHTML = name;
                span_category.setAttribute("id",'upcategory1'+msg.id);
                span_category.setAttribute("onclick", "update_category("+msg.id+")");
                span_category.setAttribute('data-toggle','tooltip');
                span_category.setAttribute('data-placement','bottom');
                span_category.title = '点击修改';
                if(msg.pid == 999){
                    span_category.style = 'cursor:pointer;color:red';
                }else{
                    span_category.style = 'cursor:pointer;color:black';
                }
                select.insertBefore(span_category,ul_li['0']);
                // select.appendChild(span_category);
                select1.parentNode.removeChild(select1);
                $('[data-toggle="tooltip"]').tooltip();
            },
            error:function(data){
                alert('修改失败');
                location.reload();
            }
        });
    }
    //修改主题
    function update_theme(id){
        $('[data-toggle="tooltip"]').tooltip('destroy');
        var obj4 = document.getElementById("uptheme"+id);
        var Newobj4 = document.createElement('select');
        var Nullobj1 = document.createElement('option');
        Nullobj1.value = " ";
        Nullobj1.innerHTML = "请选择";
        $.ajax({
            url:"/theme/selecttheme",
            type:"post",
            dataType: 'json',
            success:function(msg){
                for(var i = 0;i<msg.length;i++){
                    var option = document.createElement('option');
                    option.value = msg[i].id;
                    option.name = msg[i].theme_name;
                    option.innerHTML = msg[i].theme_name;
                    Newobj4.appendChild(option);
                }
            },
            error:function(data){
                alert('修改失败');
                location.reload();
            }
        });
        Newobj4.value=obj4.innerText;
        Newobj4.setAttribute("id","uptheme"+id);
        Newobj4.setAttribute("theme",obj4.innerText);
        Newobj4.setAttribute("value",obj4.innerText);
        Newobj4.setAttribute("onchange", "update_theme1("+id+")");
        Newobj4.appendChild(Nullobj1);
        obj4.parentNode.appendChild(Newobj4);
        obj4.parentNode.removeChild(obj4);
    }
    //修改主题
    function update_theme1(id){
        var pid = document.getElementById("uptheme"+id).value;
        var data = {
            'id' : id,
            'pid' : pid
        };
        $.ajax({
            url:"/goods/updatetheme",
            type:"post",
            data:data,
            dataType: 'json',
            success:function(msg){
                var name = msg.theme_name;
                var select = document.getElementById("uptheme"+msg.id);
                var span_theme = document.createElement('span');
                span_theme.setAttribute("type","span");
                span_theme.setAttribute("value",name);
                span_theme.innerHTML = name;
                span_theme.setAttribute("id",'uptheme'+msg.id);
                span_theme.setAttribute("onclick", "update_theme("+msg.id+")");
                span_theme.setAttribute('data-toggle','tooltip');
                span_theme.setAttribute('data-placement','bottom');
                span_theme.style = 'cursor:pointer';
                span_theme.title = '点击修改';
                select.parentNode.appendChild(span_theme);
                select.parentNode.removeChild(select);
                $('[data-toggle="tooltip"]').tooltip();
            },
            error:function(data){
                alert('修改失败');
                location.reload();
            }
        });
    }
    //修改创作年份
    function update_time(id){
        $('[data-toggle="tooltip"]').tooltip('destroy');
        var obj5 = document.getElementById("uptime"+id);
        var Newobj5 = document.createElement('input');
        Newobj5.value=obj5.innerText;
        Newobj5.setAttribute("type","input");
        Newobj5.setAttribute("id","uptime"+id);
        Newobj5.setAttribute("time",obj5.innerText);
        Newobj5.setAttribute("value",obj5.innerText);
        Newobj5.style = 'max-width:100px';
        obj5.parentNode.appendChild(Newobj5);
        obj5.parentNode.removeChild(obj5);
        addEventListener("keypress", function (e) {
            var date = {
                'id': id,
                'time': Newobj5.value,
            };
            if(e.keyCode == 13){
                $.ajax({
                    url:"/goods/updatetime",
                    type:"post",
                    data: date,
                    dataType: 'json',
                    success:function(msg){
                        var txt5 = document.getElementById("uptime"+id);
                        var Newtext5 = document.createElement('span');
                        Newtext5.setAttribute("type","span");
                        Newtext5.setAttribute("id",'uptime'+msg.id);
                        Newtext5.setAttribute("value",Newobj5.value);
                        Newtext5.setAttribute("ondblclick", "update_time("+msg.id+")");
                        Newtext5.setAttribute('data-toggle','tooltip');
                        Newtext5.setAttribute('data-placement','bottom');
                        Newtext5.style = 'cursor:pointer';
                        Newtext5.title = '双击修改';
                        if(Newobj5.value == ""){
                            Newtext5.innerHTML = "未设置";
                        }else{
                            Newtext5.innerHTML = Newobj5.value;
                        }
                        Newobj5.value == "未设置" || Newobj5.value == "" ? Newtext5.style.color = "red" : Newtext5.style.color = "black";
                        txt5.parentNode.appendChild(Newtext5);
                        txt5.parentNode.removeChild(txt5);
                        $('[data-toggle="tooltip"]').tooltip();
                    },
                    error:function(data){
                        alert('修改失败');
                        location.reload();
                    }
                });
            }
        });
    };
    //修改描述
    function update_content(id){
        $('[data-toggle="tooltip"]').tooltip('destroy');
        var obj6 = document.getElementById("upcontent"+id);
        var Newobj6 = document.createElement('textarea');
        Newobj6.value=obj6.innerText;
        Newobj6.setAttribute("id","upcontent"+id);
        Newobj6.setAttribute("content",obj6.innerText);
        Newobj6.setAttribute("value",obj6.innerText);
        obj6.parentNode.appendChild(Newobj6);
        obj6.parentNode.removeChild(obj6);
        addEventListener("keypress", function (e) {
            var date = {
                'id': id,
                'content': Newobj6.value.trim(),
            };
            if(e.keyCode == 13){
                $.ajax({
                    url:"/goods/updatecontent",
                    type:"post",
                    data: date,
                    dataType: 'json',
                    success:function(msg){
                        var txt6 = document.getElementById("upcontent"+id);
                        var Newtext6 = document.createElement('div');
                        Newtext6.setAttribute("id",'upcontent'+msg.id);
                        Newtext6.setAttribute("value",Newobj6.value);
                        Newtext6.setAttribute("ondblclick", "update_content("+msg.id+")");
                        Newtext6.style = 'cursor:pointer';
                        Newtext6.classList.add("text_style");
                        txt6.parentNode.appendChild(Newtext6);
                        txt6.parentNode.removeChild(txt6);
                        var val = Newtext6.getAttribute("value").trim();
                        if(val == ""){
                            Newtext6.innerText = "暂无描述";
                        }else{
                            Newtext6.innerText = val;
                        }
                        val == "暂无描述" || val.length == 0 ? Newtext6.style.color = "red" : Newtext6.style.color = "black";

                    },
                    error:function(data){
                        alert('修改失败');
                        location.reload();
                    }
                });
            }
        });
    };
    //修改最大高度
    function update_max_length(id){
        $('[data-toggle="tooltip"]').tooltip('destroy');
        var image_id = {
            'id' : id
        }
        $.ajax({
            url:"/goods/get_image_info",
            type:"post",
            data: image_id,
            dataType: 'json',
            success:function(msg){
                image_width1 = msg['0'].width;
                image_height1 = msg['0'].height;
            },
            error:function(data){
                alert('修改失败');
                // location.reload();
            }
        });
        var obj7 = document.getElementById("upmax_length"+id);
        var Newobj7 = document.createElement('input');
        Newobj7.value=obj7.innerText;
        Newobj7.setAttribute("type","input");
        Newobj7.setAttribute("id","upmax_length"+id);
        Newobj7.setAttribute("max_length",obj7.innerText);
        Newobj7.style = 'width:80px';
        obj7.parentNode.appendChild(Newobj7);
        obj7.parentNode.removeChild(obj7);
        addEventListener("keypress", function (e) {
            var date = {
                'id': id,
                'max_length': Newobj7.value,
                'max_width': (Newobj7.value*image_width1)/image_height1.toFixed(2),
            };
            if(e.keyCode == 13){
                $.ajax({
                    url:"/goods/updatemax_length",
                    type:"post",
                    data: date,
                    dataType: 'json',
                    success:function(msg){
                        var txt7 = document.getElementById("upmax_length"+id);
                        var txt7a = document.getElementById("upmax_width"+id);
                        var Newtext7 = document.createElement('span');
                        Newtext7.setAttribute("type","span");
                        Newtext7.setAttribute("id",'upmax_length'+msg.id);
                        Newtext7.setAttribute("value",Newobj7.value);
                        Newtext7.setAttribute('data-toggle','tooltip');
                        Newtext7.setAttribute('data-placement','bottom');
                        Newtext7.setAttribute("ondblclick", "update_max_length("+msg.id+")");
                        Newtext7.style = 'cursor:pointer';
                        Newtext7.title = '双击修改';
                        if(Newobj7.value == ""){
                            Newtext7.innerHTML = "未设置";
                        }else{
                            Newtext7.innerHTML =  parseInt(Newobj7.value).toFixed(2);
                            txt7a.innerHTML =  parseInt((Newobj7.value*image_width1)/image_height1).toFixed(2);
                        }
                        Newobj7.value == "未设置" || Newobj7.value == "" ? Newtext7.style.color = "red" : Newtext7.style.color = "black";
                        txt7.parentNode.appendChild(Newtext7);
                        txt7.parentNode.removeChild(txt7);
                        $('[data-toggle="tooltip"]').tooltip();
                    },
                    error:function(data){
                        alert('修改失败');
                        location.reload();
                    }
                });
            }
        });
    };
    //修改最大宽度
    function update_max_width(id){
        $('[data-toggle="tooltip"]').tooltip('destroy');
        var image_id = {
            'id' : id
        }
        $.ajax({
            url:"/goods/get_image_info",
            type:"post",
            data: image_id,
            dataType: 'json',
            success:function(msg){
                image_width2 = msg['0'].width;
                image_height2 = msg['0'].height;
            },
            error:function(data){
                alert('修改失败');
                // location.reload();
            }
        });
        var obj8 = document.getElementById("upmax_width"+id);
        var Newobj8 = document.createElement('input');
        Newobj8.value=obj8.innerText;
        Newobj8.setAttribute("type","input");
        Newobj8.setAttribute("id","upmax_width"+id);
        Newobj8.setAttribute("max_width",obj8.innerText);
        Newobj8.style = 'width:80px';
        obj8.parentNode.appendChild(Newobj8);
        obj8.parentNode.removeChild(obj8);
        addEventListener("keypress", function (e) {
            var date = {
                'id': id,
                'max_width': Newobj8.value,
                'max_height': parseInt((Newobj8.value*image_height2)/image_width2).toFixed(2)
            };
            if(e.keyCode == 13){
                $.ajax({
                    url:"/goods/updatemax_width",
                    type:"post",
                    data: date,
                    dataType: 'json',
                    success:function(msg){
                        var txt8 = document.getElementById("upmax_width"+id);
                        var txt8a = document.getElementById("upmax_length"+id);
                        var Newtext8 = document.createElement('span');
                        Newtext8.setAttribute("type","span");
                        Newtext8.setAttribute("id",'upmax_width'+msg.id);
                        Newtext8.setAttribute("value",Newobj8.value);
                        Newtext8.setAttribute("ondblclick", "update_max_width("+msg.id+")");
                        Newtext8.setAttribute('data-toggle','tooltip');
                        Newtext8.setAttribute('data-placement','bottom');
                        Newtext8.style = 'cursor:pointer';
                        Newtext8.title = '双击修改';
                        if(Newobj8.value == ""){
                            Newtext8.innerHTML = "未设置";
                        }else{
                            Newtext8.innerHTML =  parseInt(Newobj8.value).toFixed(2);
                            txt8a.innerHTML =  parseInt((Newobj8.value*image_height2)/image_width2).toFixed(2);
                        }
                        Newobj8.value == "未设置" || Newobj8.value == "" ? Newtext8.style.color = "red" : Newtext8.style.color = "black";
                        txt8.parentNode.appendChild(Newtext8);
                        txt8.parentNode.removeChild(txt8);
                        $('[data-toggle="tooltip"]').tooltip();
                    },
                    error:function(data){
                        alert('修改失败');
                        location.reload();
                    }
                });
            }
        });
    };
    //批量修改
    function confirm_select_all(){
        $('[data-toggle="tooltip"]').tooltip('destroy');
        var status_edit_code = $('#batch_edit option:selected').val();
        var status_category_code = $('#batch_edit_category option:selected').val();
        var status_theme_code = $('#batch_edit_theme option:selected').val();
        var edit_info = $('#batch_edit_premium').val();
        var edit_label = $('#batch_edit_label').val();
        var key = $("#w0").yiiGridView("getSelectedRows");
        key.length == 0? keys = "0": keys = key;
        var date = {
            'id' : keys,
            'status_edit_code' : status_edit_code,
            'status_category_code' : status_category_code,
            'status_theme_code' : status_theme_code,
            'edit_info' : edit_info
        }
        $.ajax({
            url:"/goods/update_all",
            type:"post",
            data: date,
            success:function(msg){
                alert(msg);
                location.reload();
            },
            error:function(data){
                alert('修改失败');
                location.reload();
            }
        });
    }
    //修改溢价指数
    function update_premium(id){
        $('[data-toggle="tooltip"]').tooltip('destroy');
        var obj9 = document.getElementById("uppremium"+id);
        var Newobj9 = document.createElement('input');
        Newobj9.value=obj9.innerText;
        Newobj9.setAttribute("type","input");
        Newobj9.setAttribute("id","uppremium"+id);
        Newobj9.setAttribute("premium",obj9.innerText);
        Newobj9.setAttribute("value",obj9.innerText);
        Newobj9.style = 'max-width:100px';
        obj9.parentNode.appendChild(Newobj9);
        obj9.parentNode.removeChild(obj9);
        addEventListener("keypress", function (e) {
            var date = {
                'id': id,
                'premium': Newobj9.value,
            };
            if(e.keyCode == 13){
                $.ajax({
                    url:"/goods/updatepremium",
                    type:"post",
                    data: date,
                    dataType: 'json',
                    success:function(msg){
                        var txt9 = document.getElementById("uppremium"+id);
                        var Newtext9 = document.createElement('span');
                        Newtext9.setAttribute("type","span");
                        Newtext9.setAttribute("id",'uppremium'+msg.id);
                        Newtext9.setAttribute("value",Newobj9.value);
                        Newtext9.setAttribute("ondblclick", "update_premium("+msg.id+")");
                        Newtext9.setAttribute('data-toggle','tooltip');
                        Newtext9.setAttribute('data-placement','bottom');
                        Newtext9.style = 'cursor:pointer';
                        Newtext9.title = '双击修改';
                        if(Newobj9.value == ""){
                            var num = 1;
                            Newtext9.innerHTML = num.toFixed(2);
                        }else{
                            Newtext9.innerHTML = parseInt(Newobj9.value).toFixed(2);
                        }
                        txt9.parentNode.appendChild(Newtext9);
                        txt9.parentNode.removeChild(txt9);
                        $('[data-toggle="tooltip"]').tooltip();
                    },
                    error:function(data){
                        alert('修改失败');
                        location.reload();
                    }
                });
            }
        });
    };
    //批量上传
    function uploads(){
        $('[data-toggle="tooltip"]').tooltip('destroy');
        var submit_form = new FormData($("#upload")['0']);
        $.ajax({
            url:"/goods/uploadsimage",
            type:"post",
            data: submit_form,
            cache: false,
            processData: false,
            contentType: false,
            success:function(msg){
                if(msg){
                   var html = "";
                   html += '<div id="img1" style="max-width:570px;max-height:550px;overflow:scroll">';
                   html += '<p></p>';
                   html += '<img src="" alt=""/ class="" style="height: auto;padding-right: 17px;float:right;max-width:270px">';
                   html += '<dl>';
                   html += ' <dd> <span>作&nbsp&nbsp&nbsp&nbsp者:</span> <input id="create_author" type="text" class="author form-control select_input modal_style"> </dd>';
                   // html += ' <dd> <span>名&nbsp&nbsp&nbsp&nbsp称:</span> <input id="create_name" type="text" class="name form-control select_input modal_style">  </dd>';
                   html += ' <dd> <span>年&nbsp&nbsp&nbsp&nbsp份:</span> <input id="create_year" type="text" class="year form-control select_input modal_style"> </dd>';
                   html += ' <dd> <span>溢价数:</span> <input id="create_more" value="1.00" type="text" class="more form-control select_input modal_style"> </dd>';
                   html += ' <dd> <span>最大宽:</span> <input id="create_max-width" placeholder="单位cm" type="text" class="max-width form-control select_input modal_style"> </dd>';
                   html += ' <dd> <span>最大高:</span> <input id="create_max-height" placeholder="单位cm" type="text" class="max-height form-control select_input modal_style"> </dd>';
                   html += ' <dd> <span>最小宽:</span> <input id="create_min-width" value="0" type="text" class="min-width form-control select_input modal_style"> </dd>';
                   html += ' <dd> <span>最小高:</span> <input id="create_min_height" value="0" type="text" class="min-height form-control select_input modal_style"> </dd>';
                   html += ' <dd> <span>类&nbsp&nbsp&nbsp&nbsp型:</span> <select id="batch_edit_category2"  class="form-control select_input modal_style">\n' +
                       '                <option value="0">选择类型</option>\n' +
                       '                <option value="1">油画</option>\n' +
                       '                <option value="6">---当代油画</option>\n' +
                       '                <option value="7">---古典油画</option>\n' +
                       '                <option value="8">---现代经典油画</option>\n' +
                       '                <option value="18">---印象派油画</option>\n' +
                       '                <option value="2">国画</option>\n' +
                       '                <option value="9">---当代水墨</option>\n' +
                       '                <option value="10">---近现代水墨</option>\n' +
                       '                <option value="11">---古代画</option>\n' +
                       '                <option value="17">---书法篆刻</option>\n' +
                       '                <option value="3">综合绘画</option>\n' +
                       '                <option value="12">---综合媒介绘画</option>\n' +
                       '                <option value="13">---数字绘画</option>\n' +
                       '                <option value="4">装饰画</option>\n' +
                       '                <option value="5">其他</option>\n' +
                       '                <option value="14">---素描水彩</option>\n' +
                       '                <option value="15">---摄影</option>\n' +
                       '                <option value="16">---卡通插画</option>\n' +
                       '                <option value="19">---日本绘画</option>\n' +
                       '                <option value="20">---地图</option>\n' +
                       '            </select></dd>';
                   html += ' <dd> <span>主&nbsp&nbsp&nbsp&nbsp题:</span> <select id="batch_edit_theme2" class="form-control select_input modal_style">\n' +
                       '                <option value="0">选择主题</option>\n' +
                       '                <option value="1">抽象</option>\n' +
                       '                <option value="2">风景</option>\n' +
                       '                <option value="3">静物</option>\n' +
                       '                <option value="4">人物</option>\n' +
                       '                <option value="5">植物花卉</option>\n' +
                       '                <option value="6">建筑场景</option>\n' +
                       '                <option value="7">动物宠物</option>\n' +
                       '                <option value="8">书法文字</option>\n' +
                       '                <option value="9">其他</option>\n' +
                       '                <option value="10">机械工具</option>\n' +
                       '                <option value="11">宇宙星空</option>\n' +
                       '            </select></dd>';
                   html += ' <dd class="rule"> <span>尺&nbsp&nbsp&nbsp&nbsp寸:</span> <input id="create_rule_len" type="text" class="l form-control select_input modal_style" placeholder="长/cm"><input  id="create_rule_wid" type="text" class="w form-control select_input modal_style" placeholder="宽/cm"></dd>';
                   html += ' <input type="hidden" id="create_id" class ="create_id" value=""/>';
                   html += ' </dd>';
                   html += ' <dd> <span>备&nbsp&nbsp&nbsp&nbsp注:</span> <textarea name="create_content" id="create_content" class="form-control select_input modal_style" style="width:475px"></textarea> </dd>';
                   html += ' </dl>';
                   html += ' </div>';
                   var num = JSON.parse(msg);
                   var ids = new Array();
                   for(var x in num){

                       var node1 = document.getElementById("create_info");
                       var node2 = document.createElement('div');
                       var id = num[x].id;
                       var image_length = num[x].image_length;
                       var image_width = num[x].image_width;
                           node2.setAttribute("id",'create_info'+x);
                           node1.parentNode.appendChild(node2);
                           $("#create_info"+x).html(html);
                           $("#create_info"+x+" img").attr('src',"/goods/"+num[x].image);
                           $('#create_id').attr('id',"create_id"+id);
                           $("#create_author").attr('id',"create_author"+id);
                           // $("#create_name").attr('id',"create_name"+id);
                           $("#create_year").attr('id',"create_year"+id);
                           $("#create_more").attr('id',"create_more"+id);
                           $("#create_max-width").attr('id',"create_max-width"+id);
                           $("#create_max-height").attr('id',"create_max-height"+id);
                           $("#create_min-width").attr('id',"create_min-width"+id);
                           $("#create_min_height").attr('id',"create_min_height"+id);
                           $("#batch_edit_category2").attr('id',"batch_edit_category2"+id);
                           $("#batch_edit_theme2").attr('id',"batch_edit_theme2"+id);
                           $("#create_rule_len").attr('id',"create_rule_len"+id);
                           $("#create_rule_wid").attr('id',"create_rule_wid"+id);
                           $("#create_content").attr('id',"create_content"+id);
                           ids.push(id);
                   }
                   $('#modal_submit').bind("click",function(){
                       var infos = new Object();
                       for(var num2 = 0; num2<ids.length ; num2++){
                           var id2 = ids[num2];
                           var author = $("#create_author"+id2).val();
                           // var name = $("#create_name"+id2).val();
                           var year = $("#create_year"+id2).val();
                           var more = $("#create_more"+id2).val();
                           var max_width = $("#create_max-width"+id2).val();
                           var max_height = $("#create_max-height"+id2).val();
                           var min_width = $("#create_min-width"+id2).val();
                           var min_height = $("#create_min_height"+id2).val();
                           var category2 = $("#batch_edit_category2"+id2).val();
                           var theme2 = $("#batch_edit_theme2"+id2).val();
                           var rule_len = $("#create_rule_len"+id2).val();
                           var rule_wid = $("#create_rule_wid"+id2).val();
                           var content = $("#create_content"+id2).val();
                           var info = {
                               'id' : id2,
                               'author' : author,
                               // 'name' : name,
                               'year' : year,
                               'more' : more,
                               'max_width' : max_width,
                               'max_height' : max_height,
                               'min_width' : min_width,
                               'min_height' : min_height,
                               'category2' : category2,
                               'theme2' : theme2,
                               'rule_len' : rule_len,
                               'rule_wid' : rule_wid,
                               'content' : content,
                               'image_height' : image_length,
                               'image_width' : image_width
                       }
                           infos[num2] = info;
                       }
                       $.ajax({
                           url:"/goods/create_all",
                           type:"post",
                           data: infos,
                           success:function(msg1){
                               alert(msg1);
                               location.reload();
                           },
                           error:function(data1){
                                alert("添加失败");
                               location.reload();
                           }
                       });
                   });
               }
            },
            error:function(data){
                alert('执行失败');
            }
        });
        get_pro = window.setInterval('fetch_progress()', 1000);
    }
    //修改颜色
    function update_color(id){
        $('[data-toggle="tooltip"]').tooltip('destroy');
        var obj10 = document.getElementById("upcolor"+id);
        var Newobj10 = document.createElement('select');
        Newobj10.style = "width:80px;";
        var span = document.createElement('span');
        for(var i = 0;i<=11;i++){
            var Nullobj2 = document.createElement('option');
            Nullobj2.value = i;
            switch(i){
                case 0: Nullobj2.innerHTML = "请选择";break;
                case 1: Nullobj2.style = "background-color:rgb(255,0,0);";break;
                case 2: Nullobj2.style = "background-color:rgb(255,150,0);";break;
                case 3: Nullobj2.style = "background-color:rgb(255,255,0);";break;
                case 4: Nullobj2.style = "background-color:rgb(0,255,0);";break;
                case 5: Nullobj2.style = "background-color:rgb(0,255,255);";break;
                case 6: Nullobj2.style = "background-color:rgb(0,0,255);";break;
                case 7: Nullobj2.style = "background-color:rgb(100,50,150);";break;
                case 8: Nullobj2.style = "background-color:rgb(255,150,255);";break;
                case 9: Nullobj2.style = "background-color:rgb(255,255,255);";break;
                case 10: Nullobj2.style = "background-color:rgb(0,0,0);";break;
                case 11: Nullobj2.style = "background-color:rgb(120,120,120);";break;
            };
            Newobj10.appendChild(Nullobj2);
        }
        Newobj10.setAttribute("id","upcolor"+id);
        Newobj10.setAttribute("onchange", "update_color1("+id+")");
        obj10.parentNode.appendChild(Newobj10);
        obj10.parentNode.removeChild(obj10);
    }
    //修改颜色
    function update_color1(id,e){
        var color = parseInt($(e.target).attr('data_id'))
        var data = {
            'id' : id,
            'color' : color
        };
        if(color==0){
            console.log("请选择要改的颜色");
        }else{
            $.ajax({
                url:"/goods/update_color",
                type:"post",
                data:data,
                dataType: 'json',
                success:function(msg){
                     if(msg.color == 1){
                         $('#upcolor'+msg.id).css('background-color','rgb(255,0,0)')
                    }if(msg.color == 2){
                        $('#upcolor'+msg.id).css('background-color','rgb(255,150,0)')
                    }if(msg.color == 3) {
                        $('#upcolor'+msg.id).css('background-color','rgb(255,255,0)')
                    }if(msg.color == 4) {
                        $('#upcolor'+msg.id).css('background-color','rgb(0,255,0)')
                    }if(msg.color == 5) {
                        $('#upcolor'+msg.id).css('background-color','rgb(0,255,255)')
                    }if(msg.color == 6) {
                        $('#upcolor'+msg.id).css('background-color','rgb(0,0,255)')
                    }if(msg.color == 7) {
                        $('#upcolor'+msg.id).css('background-color','rgb(100,50,150)')
                    }if(msg.color == 8) {
                        $('#upcolor'+msg.id).css('background-color','rgb(255,150,255)')
                    }if(msg.color == 9) {
                        $('#upcolor'+msg.id).css('background-color','rgb(255,255,255)')
                    }if(msg.color == 10) {
                        $('#upcolor'+msg.id).css('background-color','rgb(0,0,0)')
                    }if(msg.color == 11) {
                        $('#upcolor'+msg.id).css('background-color','rgb(120,120,120)')
                    }
                },
                error:function(data){
                    alert('修改失败');
                    location.reload();
                }
            });
        }
    }
    //查看标签
    function select_label(id){
        var data = {
            'id' : id,
        };
        //标签库常用标签
        $.ajax({
            url:"/label/select_label",
            type:"post",
            data:data,
            dataType: 'json',
            success:function(msg){
                if(msg){
                    var html = "";
                    for(var num=0;num<msg.length;num++){
                        html += "<div style='display:-webkit-inline-box;' ><button class='btn btn-default label_style' id='label_list"+msg[num].id+"' onclick='add_mylabel("+msg[num].id+","+id+")'>"+msg[num].label_name+"</button><span class='label_span' style='cursor:pointer;display:none' onclick='delede_labelist("+msg[num].id+","+id+");'>❌</span></div>";
                    }
                    $("#label_list").html(html);
                }
            },
            error:function(msg){
                console.log(msg);
            }
        });
        //我的标签
        $.ajax({
            url:"/goods/select_label",
            type:"post",
            data:data,
            dataType: 'json',
            success:function(msg){
                if(msg){
                    var html = "";
                    for(var i=0;i<msg.length;i++){
                        if(msg[i].label_name){
                            html += "<button class='btn btn-default label_style'  data-toggle='tooltip' data-placement='bottom' title='点击删除' onclick='delete_mylabel("+id+","+msg[i].id+")' id='mylabel"+msg[i].id+"'>"+msg[i].label_name+"</button>";
                        }else{
                            html += "";
                        }
                    }
                    $("#mylabel").html(html);
                    $('[data-toggle="tooltip"]').tooltip();
                }
            },
            error:function(msg){
                console.log(msg);
            }
        });

    }
    //添加常用标签
    function add_label_list(){
        var label = $("#label_input").val();
        if(label.length == 0){
            var label = $("#label_input2").val();
        }
        var data = {
            'label':label,
        };
        $.ajax({
            url:"/label/add_label",
            type:"post",
            data:data,
            dataType: 'json',
            success:function(msg){
                if(msg){
                    var html = "<div style='display:-webkit-inline-box;' ><button class='btn btn-default label_style' id='label_list"+msg['0'].id+"' onclick='add_mylabel("+msg['0'].id+")'>"+msg['0'].label_name+"</button><span class='label_span_all' style='cursor:pointer' onclick='delede_labelist("+msg['0'].id+");'>❌</span></div>";
                    var html2 = "<div style='display:-webkit-inline-box;' ><button class='btn btn-default label_style' id='label_list_all"+msg['0'].id+"' onclick='add_mylabel_all("+msg['0'].id+")'>"+msg['0'].label_name+"</button><span class='label_span_all' style='cursor:pointer' onclick='delede_labelist("+msg['0'].id+");'>❌</span></div>";
                    $("#label_list").append(html);
                    $("#label_list_all").append(html2);
                }
            },
            error:function(msg){
                alert(msg.responseText);
            }
        });
    }
    //显示删除标签按钮
    function del_label_list(e){
        $(e.target).css('display','none')
        $("#hide_del_label").css('display','inline-block')
        $(".label_span").css('display','inline-block')
    }
    //显示取消删除标签按钮
    function del_label_list1(e){
        $(e.target).css('display','none')
        $("#show_del_label").css('display','inline-block')
        $(".label_span").css('display','none')
    }
    //批量显示删除标签按钮
    function del_label_lists(e){
        $(e.target).css('display','none')
        $("#hide_del_labels").css('display','inline-block')
        $(".label_span_all").css('display','inline-block')
    }
    //批量显示取消删除标签按钮
    function del_label_list1s(e){
        $(e.target).css('display','none')
        $("#show_del_labels").css('display','inline-block')
        $(".label_span_all").css('display','none')
    }
    //批量显示删除标签按钮2
    function del_label_lists2(e){
        $(e.target).css('display','none')
        $("#hide_del_labels2").css('display','inline-block')
        $(".label_span_all2").css('display','inline-block')
    }
    //批量显示取消删除标签按钮2
    function del_label_list1s2(e){
        $(e.target).css('display','none')
        $("#show_del_labels2").css('display','inline-block')
        $(".label_span_all2").css('display','none')
    }
    //添加我的标签
    function add_mylabel(label_id,id){
        var data = {
            'id':id,
            'label_id':label_id,
        };
        var label = $("#mylabel button");
        var labels = [];
        var res = '';
        for(var len=0; len<label.length;len++){
            labels.push(label[len].getAttribute("id"));
        }
        for(var k = 0;k<labels.length;k++){
            if(labels[k]=='mylabel'+label_id){
                res = '1';
                break;
            }
        }
        if(res==1){
            alert("不可重复添加,请重新选择,或者自定义标签");
            return;
        }
        $.ajax({
            url:"/goods/add_mylabel",
            type:"post",
            data:data,
            dataType: 'json',
            success:function(msg){
                if(msg){
                    var html = "<button class='btn btn-default label_style' data-toggle='tooltip' data-placement='bottom' title='点击删除'  onclick='delete_mylabel("+id+","+msg['0'].id+")' id='mylabel"+msg['0'].id+"'>"+msg['0'].label_name+"</button>";
                        $("#mylabel").append(html);
                    $('[data-toggle="tooltip"]').tooltip();
                }
            },
            error:function(msg){
                console.log(msg);
            }
        });
    }
    //删除我的标签
    function delete_mylabel(id,label_id){
        $('[data-toggle="tooltip"]').tooltip('hide');
        var data = {
            'id':id,
            'label_id':label_id
        };
        $.ajax({
            url:"/goods/delete_mylabel",
            type:"post",
            data:data,
            dataType: 'json',
            success:function(msg){
                if(msg){
                    $("#mylabel"+msg['0'].del_labelid).remove();
                }
            },
            error:function(msg){
                console.log(msg);
            }
        });
    }
    //删除常用标签
    function delede_labelist(label_id){
        var is_delete = confirm('是否删除常用标签');
        var data = {
            'label_id':label_id
        };
        if (is_delete == true) {
            $.ajax({
                url:"/label/delete_labelist",
                type:"post",
                data:data,
                dataType: 'json',
                success:function(msg){
                    if(msg){
                        $("#label_list"+msg['0'].del_labelid).parent().remove();
                        $("#mylabel"+msg['0'].del_labelid).remove();
                        $("#label_list_all"+msg['0'].del_labelid).parent().remove();
                    }
                },
                error:function(msg){
                    alert(msg);
                    console.log(msg);
                }
            });
        }
    }
    //文件上传七牛云
    function UpladFile(){
        $("#close_id").css("display",'none')
        var el = document.getElementById('create_info');
        var childs = el.childNodes;
            for(var k = childs .length - 1; k >= 0; k--) {
                el.removeChild(childs[k]);
            }
        var objFile = document.getElementById("goods-image");
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
                url:"/goods/gettoken",
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
                                    //获取主色调
                                    $.ajax({
                                        url:img_url,
                                        type:"post",
                                        dataType: 'json',
                                        success:function(msg){
                                            var color = msg.RGB
                                            var data = {
                                                'color': color
                                            }
                                            //获取颜色值
                                            $.ajax({
                                                url:'/goods/getcolor',
                                                type:"post",
                                                data:data,
                                                dataType: 'json',
                                                success:function(msg){
                                                    var color_val = msg;
                                                    var data2 = {
                                                        'color' : color_val,
                                                        'img_name' : img_name,
                                                        'new_name' : new_name
                                                    }
                                                    //添加数据库
                                                    $.ajax({
                                                        url:'/goods/addimg',
                                                        type:"post",
                                                        data:data2,
                                                        dataType: 'json',
                                                        success:function(msg){
                                                            var success_dom = document.getElementById('create_info'+i);
                                                            success_dom.remove();
                                                            res.push(i)
                                                            if(res.length == msg1.length){
                                                                alert('添加成功')
                                                                $("#close_id").css("display",'')
                                                                $("#myModal").modal('hide')
                                                            }
                                                        },
                                                        error:function(msg){
                                                            console.log(msg);
                                                        }
                                                    });
                                                },
                                                error:function(msg){
                                                    console.log(msg);
                                                }
                                            });
                                        },
                                        error:function(msg){
                                            console.log(msg);
                                        }
                                    });
                                    // console && console.log(blkRet);
                                    // $("#dialog").html(xhr.responseText).dialog();
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
    //批量查看标签
    function select_label_all(){
        $.ajax({
            url:"/label/select_label",
            type:"post",
            dataType: 'json',
            success:function(msg){
                if(msg){
                    var html = "";
                    for(var num=0;num<msg.length;num++){
                        html += "<div style='display:-webkit-inline-box;' ><button class='btn btn-default label_style' id='label_list_all"+msg[num].id+"' onclick='add_mylabel_all("+msg[num].id+")'>"+msg[num].label_name+"</button><span  style='cursor:pointer;display:none' class='label_span_all' onclick='delede_labelist("+msg[num].id+");'>❌</span></div>";
                    }
                    $("#label_list_all").html(html);
                }
            },
            error:function(msg){
                console.log(msg);
            }
        });
    }
    //批量添加标签
    function add_mylabel_all(id){
        var key = $("#w0").yiiGridView("getSelectedRows");
        key.length == 0? keys = "0": keys = key;
        var data = {
            'label_id' : id,
            'id' : keys
        }
        $.ajax({
            url:"/label/add_label_all",
            type:"post",
            data:data,
            dataType: 'json',
            success:function(msg){
                if(msg == 1){
                    alert('修改成功');
                }
            },
            error:function(msg){
                console.log(msg);
            }
        });
    }
    //批量删除标签
    function delete_label_all(){
        var key = $("#w0").yiiGridView("getSelectedRows");
        key.length == 0? keys = null: keys = key;
        var data = {
            'id' : keys
        }
        if(!keys){
            toastr.error('请选择要修改的图片')
            $('#delete_all_Modal').modal('hide');
            return false
        }else{
            $('#delete_all_Modal').modal('show');
        }
        $.ajax({
            url:"/label/select_label2",
            type:"post",
            data:data,
            dataType: 'json',
            success:function(msg){
                if(msg){
                    var html = "";
                    for(var num=0;num<msg.length;num++){
                        html += "<div style='display:-webkit-inline-box;' ><button class='btn btn-default label_style' id='delete_label_list_all"+msg[num].id+"'>"+msg[num].label_name+"</button><span  style='cursor:pointer;display:none' class='label_span_all2' onclick='delete_all_list("+msg[num].id+");'>❌</span></div>";
                    }
                    $("#delete_label_list_all").html(html);
                }
            },
            error:function(msg){
                console.log(msg);
            }
        });
    }
    //批量删除标签
    function delete_all_list(label_id){
        var is_delete = confirm('是否要批量删除标签');
        if(is_delete){
            toastr.success('删除中')
            var key = $("#w0").yiiGridView("getSelectedRows");
            key.length == 0? keys = null: keys = key;
            var data = {
                'label_id' : label_id,
                'id' : keys
            }
            $.ajax({
                url:"/label/delete_label_all",
                type:"get",
                data:data,
                dataType: 'json',
                success:function(msg){
                    if(msg==1){
                        alert('删除成功');
                        $("#delete_label_list_all"+label_id).parent().remove();
                    }else{
                        alert('删除失败');
                    }
                },
                error:function(msg){
                    console.log(msg);
                }
            });
        }
    }
    //后台查看图片
    function find_img(img){
        $("#imgshow"+img).animate({width:'toggle'})
    }
    //修改每页显示数量
    function up_pagesize(uid){
        if($("#pagesize").val() == ""){
            alert('请填写要显示的数量');
        }else{
            var data = {
                'uid' : uid,
                'pagesize' : $("#pagesize").val()
            }
            $.ajax({
                url:"/goods/up_pagesize",
                type:"post",
                data:data,
                dataType: 'json',
                success:function(msg){
                    if(msg == 1){
                        location.reload();
                    }else{
                        alert('失败');
                    }
                },
                error:function(msg){
                    console.log(msg);
                }
            });
        }
    }
    //快速修改分类
    function fastup_cate(id,cate_id){
        var data = {
            'id' : id,
            'cate_id' : cate_id
        }
        $.ajax({
            url:"/goods/fastup_cate",
            type:"post",
            data:data,
            dataType: 'json',
            success:function(msg){
                $("#upcategory1"+msg.id).html(msg.category_name)
                $("#upcategory1"+msg.id).css('color','black')
            },
            error:function(msg){
                console.log(msg);
            }
        });
    }
    //快速修改主题
    function fastup_theme(id,theme_id){
        var data = {
            'id' : id,
            'theme_id' : theme_id
        }
        $.ajax({
            url:"/goods/fastup_theme",
            type:"post",
            data:data,
            dataType: 'json',
            success:function(msg){
                $("#uptheme1"+msg.id).html(msg.theme_name)
                $("#uptheme1"+msg.id).css('color','black')
            },
            error:function(msg){
                console.log(msg);
            }
        });
    }
    //修改是否显示图片名
    function up_is_face(id){
        var is_face_id = $('#upname1'+id).attr('data_id');
        var face_id = '';
        var data = {
            'id' : id,
            'face_id' : face_id = is_face_id==0?1:0
        }
        $.ajax({
            url:"/goods/up_id_face",
            type:"post",
            data:data,
            dataType: 'json',
            success:function(msg){
                $('#upname1'+id).attr('data_id',face_id);
                is_face_id==0?$('#upname1'+id).html('隐藏'):$('#upname1'+id).html('显示')
                is_face_id==0?$("#upname1"+id).attr('class','btn btn-inverse'):$("#upname1"+id).attr('class','btn btn-info')
            },
            error:function(msg){
                console.log(msg);
            }
        });
    }
    //查询图片所在页并跳转
    function img_page(){
        var id = $('#img_page').val()
        var data = {
            'id' : id
        }
        $.ajax({
            url:"/goods/find_img_page",
            type:"post",
            data:data,
            dataType: 'json',
            success:function(msg){
                if(msg ==1){
                    alert('暂无该图片')
                }else{
                    window.location.href=msg.url;
                }
            },
            error:function(msg){
                console.log(msg);
            }
        });
    }

</script>












