<?php
namespace api\modules\v1\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\QueryParamAuth;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Transaction;
use yii\web\UploadedFile;
use yii\imagine\Image;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use yii\data\Pagination;
use backend\models\goods;
use api\modules\v1\controllers\PhphashController;
use backend\models\account;
use backend\models\attention;
use backend\models\attentionUser;
use backend\models\accountChannel;
use backend\models\category;
use backend\models\theme;
use backend\models\label;
use backend\models\keep;
use backend\models\keepimage;
use backend\models\BorderMaterial;
use backend\models\Boxseries;
use backend\models\Message;

class WeixinController extends ActiveController
{

    public $modelClass = 'api\modules\v1\models\goods';
    public $img_arr = [];
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items'
    ];

    public function actions()
    {
        $actions = parent::actions();
        // 注销系统自带的实现方法
        unset($actions['index'], $actions['update'], $actions['create'], $actions['delete'], $actions['view']);
        return $actions;
    }

    protected function findModel($id)
    {
        $modelClass = $this->modelClass;
        if (($model = $modelClass::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    //获取分类
    public function actionGetcate()
    {
        $res = category::find()
            ->select('id,category_name,face_img_wx')
            ->where(['pid'=>0])
            ->andWhere(['<>','id',999])
            ->all();
        for($i=0;$i<count($res);$i++){
            $info[] = [
                'id'=> $res[$i]['id'],
                'category_name'=> $res[$i]['category_name'],
                'face_img'=> 'http://qiniu.zaoanart.com/'.$res[$i]['face_img_wx'].'?imageView2/2/w/500',
            ];
        }
        return $info;
    }

    //获取分类2
    public function actionGetcate2()
    {
        $id = $_GET['id'];
        $cate_name = category::find()->select('id,category_name')->where(['id'=>$id])->one();
        $res = category::find()->select('id,category_name,face_img_wx')->where(['pid'=>$id])->andWhere(['<>','id',999])->all();
        if($res){
            for($i=0;$i<count($res);$i++){
                $info[] = [
                    'id'=> $res[$i]['id'],
                    'category_name'=> $res[$i]['category_name'],
                    'face_img'=> 'http://qiniu.zaoanart.com/'.$res[$i]['face_img_wx'].'?imageView2/2/w/500',
                ];
            }
            $infos['data'] = $info;
            $infos['cate_name'] = $cate_name['category_name'];
            return $infos;
        }else{
            $infos['data'] = null;
            $infos['cate_name'] = $cate_name['category_name'];
            return $infos;
        }
    }

    //获取分类3
    public function actionGetcate3()
    {
        $res = category::find()->select('id,category_name')->where(['<>','id',999])->orderBy('sort')->all();
        if($res){
            for($i=0;$i<count($res);$i++){
                $info[] = [
                    'id'=> $res[$i]['id'],
                    'category_name'=> $res[$i]['category_name'],
                ];
            }
            return $info;
        }else{
            return false;
        }
    }

    //获取主题
    public function actionGettheme2()
    {
        $res = theme::find()->select('id,theme_name')->where(['pid'=>0])->andWhere(['<>','id',999])->all();
        for($i=0;$i<count($res);$i++){
            $info[] = [
                'id'=> $res[$i]['id'],
                'theme_name'=> $res[$i]['theme_name'],
//                'face_img'=> 'http://qiniu.zaoanart.com/'.$res[$i]['theme_img_wx'],
            ];
        }
        return $info;
    }

    //获取主题
    public function actionGettheme()
    {
        $res = theme::find()->select('id,theme_name,theme_img_wx')->where(['pid'=>0])->andWhere(['<>','id',999])->all();
        for($i=0;$i<count($res);$i++){
            $info[] = [
                'id'=> $res[$i]['id'],
                'theme_name'=> $res[$i]['theme_name'],
                'face_img'=> 'http://qiniu.zaoanart.com/'.$res[$i]['theme_img_wx'].'?imageView2/2/w/500',
            ];
        }
        return $info;
    }

    //获取图片
    public function actionGetallimg()
    {
        $cate_id = $_GET['cate_id'];
        $theme_id = $_GET['theme_id'];
        $color_id = $_GET['color_id'];
        $search = $_GET['search'];
        $contrast = $_GET['contrast'];
        $pageSize = 20;
        $start = $_GET['start'];
        $res = [];
        $cate_name = $theme_name = $color = '';
        if(!$search || $search == 'null'){
            $search = null;
        }
        if(!$contrast || $contrast == 'null'){
            $contrast = 0;
        }
//        switch($contrast){
//            case 1:$contrast = '<';break;
//            case 2:$contrast = '>';break;
//            case 3:$contrast = '=';break;
//            case 0:$contrast = 'or';break;
//        }
        switch($contrast){
            case 1:$contrast = ' AND ((`img_width` >= (`img_height`*1.2)) or (`img_height` <= (`img_width`*0.8)))';break;//横图
            case 2:$contrast = ' AND ((`img_height` >= (`img_width`*1.2)) or (`img_width` <= (`img_height`*0.8)))';break;//竖图
            case 3:$contrast = ' AND ((`img_width` <= (`img_height`*1.2) AND `img_width` >= (`img_height`*0.8)) or (`img_height` <= (`img_width`*1.2) AND `img_height` >= (`img_width`*0.8)))';break;//方图
            case 0:$contrast = ' AND (`img_height` or `img_width`)';break;
        }
        if($_GET['contrast'] == 1){
            $up_contrast_search_sum = 'UPDATE `tsy_contrast` SET `search_sum_wechat`=`search_sum_wechat`+1 where `id`=1 ';
            Yii::$app->db->createCommand($up_contrast_search_sum)->execute();
        }else if($_GET['contrast'] == 2){
            $up_contrast_search_sum = 'UPDATE `tsy_contrast` SET `search_sum_wechat`=`search_sum_wechat`+1 where `id`=2';
            Yii::$app->db->createCommand($up_contrast_search_sum)->execute();
        }else if($_GET['contrast'] == 3){
            $up_contrast_search_sum = 'UPDATE `tsy_contrast` SET `search_sum_wechat`=`search_sum_wechat`+1 where `id`=3 ';
            Yii::$app->db->createCommand($up_contrast_search_sum)->execute();
        }else{
            $up_contrast_search_sum = 'UPDATE `tsy_contrast` SET `search_sum_wechat`=`search_sum_wechat`+1 where `id`=4 ';
            Yii::$app->db->createCommand($up_contrast_search_sum)->execute();
        }
//        $sql1 = 'SELECT `id`  FROM `tsy_goods` WHERE `is_appear`=1 AND ((`img_width` >= (`img_height`*1.2)) AND (`img_height` <= (`img_width`*0.8))) AND ((`category` <> 999) or (`theme` <> 999)) ORDER BY `id` DESC';
//        $sql2 = 'SELECT `id`  FROM `tsy_goods` WHERE `is_appear`=1 AND ((`img_height` >= (`img_width`*1.2)) AND (`img_width` <= (`img_height`*0.8))) AND ((`category` <> 999) or (`theme` <> 999)) ORDER BY `id` DESC';
//        $sql3 = 'SELECT `id`  FROM `tsy_goods` WHERE `is_appear`=1 AND ((`img_width` <= (`img_height`*1.2) AND `img_width` >= (`img_height`*0.8)) or (`img_height` <= (`img_width`*1.2) AND `img_height` >= (`img_width`*0.8))) AND ((`category` <> 999) or (`theme` <> 999)) ORDER BY `id` DESC';
//        $res1 = Yii::$app->db->createCommand($sql1)->queryAll();
//        $res2 = Yii::$app->db->createCommand($sql2)->queryAll();
//        $res3 = Yii::$app->db->createCommand($sql3)->queryAll();
//        $res4 = array_merge($res1,$res2);
//        $res5 = array_merge($res4,$res3);
//        $res6 = array_unique($res5,SORT_REGULAR);
//        var_dump($res6);die;

        if($cate_id){
            $cate_name = category::find()->select('category_name')->where(['id'=>$cate_id])->one();
            $cate_name = $cate_name['category_name'];
            $up_cate_search_sum = 'UPDATE `tsy_category` SET `search_sum_wechat`=`search_sum_wechat`+1 where `id`='.$cate_id.' ';
            Yii::$app->db->createCommand($up_cate_search_sum)->execute();
        }
        if($theme_id){
            $theme_name = theme::find()->select('theme_name')->where(['id'=>$theme_id])->one();
            $theme_name = $theme_name['theme_name'];
            $up_theme_search_sum = 'UPDATE `tsy_theme` SET `search_sum_wechat`=`search_sum_wechat`+1 where `id`='.$theme_id.' ';
            Yii::$app->db->createCommand($up_theme_search_sum)->execute();
        }
        if(!$search){
            $cate_res = null;
            $theme_res = null;
        }else{
            $res1 = category::find()->select('id')->where(['like','category_name',$search])->all();
            $res2 = theme::find()->select('id')->where(['like','theme_name',$search])->all();
            $res3= label::find()->select('id')->where(['like','label_name',$search])->all();
            $cate_res = null;
            $theme_res = null;
            $label_res = null;
            if($res1){
                for($y=0;$y<count($res1);$y++){
                    $cate_res .= ' or (`category`= '.$res1[$y]['id'].')';
                }
                $cate_res = ' or ('.ltrim($cate_res,' or').')';
            }
            if($res2){
                for($z=0;$z<count($res2);$z++){
                    $theme_res .= ' or (`theme`= '.$res2[$z]['id'].')';
                }
                $theme_res = ' or ('.ltrim($theme_res,' or').')';
            }
            if($res3){
                for($x=0;$x<count($res3);$x++){
                    $up_label_search_sum = 'UPDATE `tsy_label` SET `search_sum_wechat`=`search_sum_wechat`+1 where `id`='.$res3[$x]['id'].' ';
                    Yii::$app->db->createCommand($up_label_search_sum)->execute();
                    $label_res .= " or (`label` like '%,".$res3[$x]['id'].",%')";
                }
                $label_res = ''.$label_res.'';
            }
//            for($y=0;$y<count($res1);$y++){
//                $cate_res[] = $res1[$y]['id'];
//            }
//            for($z=0;$z<count($res2);$z++){
//                $theme_res[] = $res2[$z]['id'];
//            }
        }
        if($color_id){
            $up_color_search_sum = 'UPDATE `tsy_color` SET `search_sum_wechat`=`search_sum_wechat`+1 where `id`='.$color_id.' ';
            Yii::$app->db->createCommand($up_color_search_sum)->execute();
            switch ($color_id){
                case $color_id == 1 : $color = '红色';break;
                case $color_id == 2 : $color = '橙色';break;
                case $color_id == 3 : $color = '黄色';break;
                case $color_id == 4 : $color = '绿色';break;
                case $color_id == 5 : $color = '青色';break;
                case $color_id == 6 : $color = '蓝色';break;
                case $color_id == 7 : $color = '紫色';break;
                case $color_id == 8 : $color = '粉色';break;
                case $color_id == 9 : $color = '白色';break;
                case $color_id == 10 : $color = '黑色';break;
                case $color_id == 11 : $color = '灰色';break;
            }
        }
        $color1 = null;
        if(!$search){
            $color1 = null;
        }else{
            switch ($search){
                case $search == '红色' : $color1 = 1;break;
                case $search == '橙色' : $color1 = 2;break;
                case $search == '黄色' : $color1 = 3;break;
                case $search == '绿色' : $color1 = 4;break;
                case $search == '青色' : $color1 = 5;break;
                case $search == '蓝色' : $color1 = 6;break;
                case $search == '紫色' : $color1 = 7;break;
                case $search == '粉色' : $color1 = 8;break;
                case $search == '白色' : $color1 = 9;break;
                case $search == '黑色' : $color1 = 10;break;
                case $search == '灰色' : $color1 = 11;break;
                case $search == '红' : $color1 = 1;break;
                case $search == '橙' : $color1 = 2;break;
                case $search == '黄' : $color1 = 3;break;
                case $search == '绿' : $color1 = 4;break;
                case $search == '青' : $color1 = 5;break;
                case $search == '蓝' : $color1 = 6;break;
                case $search == '紫' : $color1 = 7;break;
                case $search == '粉' : $color1 = 8;break;
                case $search == '白' : $color1 = 9;break;
                case $search == '黑' : $color1 = 10;break;
                case $search == '灰' : $color1 = 11;break;
            }
        }
        if($search != 'null' && $search){
            $search_id = "(`id` LIKE '%".$search."%')";
            $search_name = " or (`name` LIKE '%".$search."%')";
            $search_category = $cate_res;
            $search_theme = $theme_res;
            $search_image = " or (`image` LIKE '%".$search."%')";
            $search_author = " or (`author` LIKE '%".$search."%')";
            $search_price = " or (`price` LIKE '%".$search."%')";
            $search_content = " or (`content` LIKE '%".$search."%')";
            if($color1){
                $search_color = " or (`color` = ".$color1.")";
            }else{
                $search_color = null;
            }
            $search_condition = ' AND ('.$search_id.$search_name.$search_category.$search_theme.$search_image.$search_author.$search_price.$search_content.$search_color.$label_res.')';
        }else{
            $search_condition = null;
        }
        $condition_str = $cate_name.','.$theme_name.','.$color.','.$search;
        $explode = array_filter(explode(',',$condition_str));
        sort($explode);
        if($cate_id == 0){
            if($theme_id == 0){
                if($color_id == 0){
                    //全空
                    $sql = Goods::find()
                        ->select('id,image,name,img_width,img_height,category,theme')
                        ->where(['is_appear' => 1])
                        ->createCommand()
                        ->getRawSql();
//                    $sql_str = $sql.' AND (`img_height` '.$contrast.' `img_width`)'.$search_condition.' AND ((`category` <> 999) or (`theme` <> 999)) ORDER BY `id` DESC LIMIT '.$pageSize.' OFFSET '.$start.'';
                    $sql_str = $sql.$contrast.$search_condition.' AND ((`category` <> 999) or (`theme` <> 999)) ORDER BY `id` DESC LIMIT '.$pageSize.' OFFSET '.$start.'';
                    $res = Yii::$app->db->createCommand($sql_str)->queryAll();
                }else{
                    //颜色不空,其他空
                    $sql = Goods::find()
                        ->select('id,image,name,img_height,category,theme')
                        ->where(['color'=>$color_id])
                        ->createCommand()
                        ->getRawSql();
                    $sql_str = $sql.$contrast.$search_condition.' AND (`is_appear`=1) AND ((`category` <> 999) or (`theme` <> 999)) ORDER BY `id` DESC LIMIT '.$pageSize.' OFFSET '.$start.'';
                    $res = Yii::$app->db->createCommand($sql_str)->queryAll();
                }
            }else{
                if($color_id == 0){
                    //颜色和cate空,theme不空
                    $sql = Goods::find()
                        ->select('id,image,name,img_height,category,theme')
                        ->where(['theme'=>$theme_id])
                        ->createCommand()
                        ->getRawSql();
                    $sql_str = $sql.$contrast.$search_condition.' AND (`is_appear`=1) AND ((`category` <> 999) or (`theme` <> 999)) ORDER BY `id` DESC LIMIT '.$pageSize.' OFFSET '.$start.'';
                    $res = Yii::$app->db->createCommand($sql_str)->queryAll();
                }else{
                    //颜色和theme不空,cate空
                    $sql = Goods::find()
                        ->select('id,image,name,img_height,category,theme')
                        ->where(['theme'=>$theme_id,'color'=>$color_id])
                        ->createCommand()
                        ->getRawSql();
                    $sql_str = $sql.$contrast.$search_condition.' AND (`is_appear`=1) AND ((`category` <> 999) or (`theme` <> 999)) ORDER BY `id` DESC LIMIT '.$pageSize.' OFFSET '.$start.'';
                    $res = Yii::$app->db->createCommand($sql_str)->queryAll();
                }
            }
        }else{
            if($theme_id == 0){
                if($color_id == 0){
                    //颜色和theme空,cate不空
                    $cate_pid = category::find()
                        ->select('pid')
                        ->where(['id'=>$cate_id])
                        ->one();
                    if($cate_pid['pid']==0){
                        $id_arr = category::find()
                            ->select('id')
                            ->where(['pid'=>$cate_id])
                            ->all();
                        if(empty($id_arr)){
                            $sql = Goods::find()
                                ->select('id,image,name,img_height,category,theme')
                                ->where(['category'=>$cate_id])
                                ->createCommand()
                                ->getRawSql();
                            $sql_str = $sql.$contrast.$search_condition.' AND (`is_appear`=1) AND ((`category` <> 999) or (`theme` <> 999)) ORDER BY `id` DESC LIMIT '.$pageSize.' OFFSET '.$start.'';
                            $res = Yii::$app->db->createCommand($sql_str)->queryAll();
                        }else{
                            $cate_id2[] = [
                                'id'=>(int)$cate_id
                            ];
                            $result = array_merge($cate_id2,$id_arr);
                            $condition = '';
                            for($i = 0;$i<count($result);$i++){
                                $condition .= ' or (`category` = '.$result[$i]['id'].')';
                                $sql = Goods::find()
                                    ->select('id,image,name,img_height,category,theme')
                                    ->andWhere(['is_appear'=>1])
                                    ->createCommand()
                                    ->getRawSql();
                            }
                            $sql_str = $sql.$contrast.' AND ('.substr($condition,3).')'.$search_condition.' AND (`is_appear`=1) AND ((`category` <> 999) or (`theme` <> 999)) ORDER BY `id` DESC LIMIT '.$pageSize.' OFFSET '.$start.'';
                            $res = Yii::$app->db->createCommand($sql_str)->queryAll();
                        }
                    }else{
                        $sql = Goods::find()
                            ->select('id,image,name,img_height,category,theme')
                            ->where(['category'=>$cate_id])
                            ->createCommand()
                            ->getRawSql();
                        $sql_str = $sql.$contrast.$search_condition.' AND (`is_appear`=1) AND ((`category` <> 999) or (`theme` <> 999)) ORDER BY `id` DESC LIMIT '.$pageSize.' OFFSET '.$start.'';
                        $res = Yii::$app->db->createCommand($sql_str)->queryAll();
                    }
                }else{
                    //颜色和cate不空,theme空
                    $cate_pid = category::find()
                        ->select('pid')
                        ->where(['id'=>$cate_id])
                        ->one();
                    if($cate_pid['pid']==0){
                        $id_arr = category::find()
                            ->select('id')
                            ->where(['pid'=>$cate_id])
                            ->all();
                        if(empty($id_arr)){
                            $sql = Goods::find()
                                ->select('id,image,name,img_height,category,theme')
                                ->where(['category'=>$cate_id,'color' => $color_id])
                                ->createCommand()
                                ->getRawSql();
                            $sql_str = $sql.$contrast.$search_condition.' AND (`is_appear`=1) AND ((`category` <> 999) or (`theme` <> 999)) ORDER BY `id` DESC LIMIT '.$pageSize.' OFFSET '.$start.'';
                            $res = Yii::$app->db->createCommand($sql_str)->queryAll();
                        }else{
                            $cate_id2[] = [
                                'id'=>(int)$cate_id
                            ];
                            $result = array_merge($cate_id2,$id_arr);
                            $condition = '';
                            for($i = 0;$i<count($result);$i++){
                                $condition .= ' or (`category` = '.$result[$i]['id'].')';
                                $sql = Goods::find()
                                    ->select('id,image,name,img_height,category,theme')
                                    ->where(['color' => $color_id])
                                    ->createCommand()
                                    ->getRawSql();
                            }
                            $sql_str = $sql.$contrast.' AND ('.substr($condition,3).')'.$search_condition.' AND (`is_appear`=1) AND ((`category` <> 999) or (`theme` <> 999)) ORDER BY `id` DESC LIMIT '.$pageSize.' OFFSET '.$start.'';
                            $res = Yii::$app->db->createCommand($sql_str)->queryAll();
                        }
                    }else {
                        $sql = Goods::find()
                            ->select('id,image,name,img_height,category,theme')
                            ->where(['category' => $cate_id, 'color' => $color_id])
                            ->createCommand()
                            ->getRawSql();
                        $sql_str = $sql.$contrast.$search_condition.' AND (`is_appear`=1) AND ((`category` <> 999) or (`theme` <> 999)) ORDER BY `id` DESC LIMIT '.$pageSize.' OFFSET '.$start.'';
                        $res = Yii::$app->db->createCommand($sql_str)->queryAll();
                    }
                }
            }else{
                if($color_id == 0){
                    //颜色空,theme和cate不空
                    $cate_pid = category::find()
                        ->select('pid')
                        ->where(['id'=>$cate_id])
                        ->one();
                    if($cate_pid['pid']==0){
                        $id_arr = category::find()
                            ->select('id')
                            ->where(['pid'=>$cate_id])
                            ->all();
                        if(empty($id_arr)){
                            $sql = Goods::find()
                                ->select('id,image,name,img_height,category,theme')
                                ->where(['category'=>$cate_id,'theme' => $theme_id])
                                ->createCommand()
                                ->getRawSql();
                            $sql_str = $sql.$contrast.$search_condition.' AND (`is_appear`=1) AND ((`category` <> 999) or (`theme` <> 999)) ORDER BY `id` DESC LIMIT '.$pageSize.' OFFSET '.$start.'';
                            $res = Yii::$app->db->createCommand($sql_str)->queryAll();
                        }else{
                            $cate_id2[] = [
                                'id'=>(int)$cate_id
                            ];
                            $result = array_merge($cate_id2,$id_arr);
                            $condition = '';
                            for($i = 0;$i<count($result);$i++){
                                $condition .= ' or (`category` = '.$result[$i]['id'].')';
                                $sql = Goods::find()
                                    ->select('id,image,name,img_height,category,theme')
                                    ->where(['theme' => $theme_id])
                                    ->createCommand()
                                    ->getRawSql();
                            }
                            $sql_str = $sql.$contrast.' AND ('.substr($condition,3).')'.$search_condition.' AND (`is_appear`=1) AND ((`category` <> 999) or (`theme` <> 999)) ORDER BY `id` DESC LIMIT '.$pageSize.' OFFSET '.$start.'';
                            $res = Yii::$app->db->createCommand($sql_str)->queryAll();
                        }
                    }else{
                        $sql = Goods::find()
                            ->select('id,image,name,img_height,category,theme')
                            ->where(['category' => $cate_id, 'theme' => $theme_id])
                            ->createCommand()
                            ->getRawSql();
                        $sql_str = $sql.$contrast.$search_condition.' AND (`is_appear`=1) AND ((`category` <> 999) or (`theme` <> 999)) ORDER BY `id` DESC LIMIT '.$pageSize.' OFFSET '.$start.'';
                        $res = Yii::$app->db->createCommand($sql_str)->queryAll();
                    }
                }else{
                    //全不空
                    $cate_pid = category::find()
                        ->select('pid')
                        ->where(['id'=>$cate_id])
                        ->one();
                    if($cate_pid['pid']==0){
                        $id_arr = category::find()
                            ->select('id')
                            ->where(['pid'=>$cate_id])
                            ->all();
                        if(empty($id_arr)){
                            $sql = Goods::find()
                                ->select('id,image,name,img_height,category,theme')
                                ->where(['category'=>$cate_id,'theme' => $theme_id,'color' => $color_id])
                                ->createCommand()
                                ->getRawSql();
                            $sql_str = $sql.$contrast.$search_condition.' AND (`is_appear`=1) AND ((`category` <> 999) or (`theme` <> 999)) ORDER BY `id` DESC LIMIT '.$pageSize.' OFFSET '.$start.'';
                            $res = Yii::$app->db->createCommand($sql_str)->queryAll();
                        }else{
                            $cate_id2[] = [
                                'id'=>(int)$cate_id
                            ];
                            $result = array_merge($cate_id2,$id_arr);
                            $condition = '';
                            for($i = 0;$i<count($result);$i++){
                                $condition .= ' or (`category` = '.$result[$i]['id'].')';
                                $sql = Goods::find()
                                    ->select('id,image,name,img_height,category,theme')
                                    ->where(['theme' => $theme_id,'color' => $color_id])
                                    ->createCommand()
                                    ->getRawSql();
                            }
                            $sql_str = $sql.$contrast.' AND ('.substr($condition,3).')'.$search_condition.' AND (`is_appear`=1) AND ((`category` <> 999) or (`theme` <> 999)) ORDER BY `id` DESC LIMIT '.$pageSize.' OFFSET '.$start.'';
                            $res = Yii::$app->db->createCommand($sql_str)->queryAll();
                        }
                    }else {
                        $sql = Goods::find()
                            ->select('id,image,name,img_height,category,theme')
                            ->where(['category' => $cate_id, 'theme' => $theme_id, 'color' => $color_id])
                            ->createCommand()
                            ->getRawSql();
                        $sql_str = $sql.$contrast.$search_condition.' AND (`is_appear`=1) AND ((`category` <> 999) or (`theme` <> 999)) ORDER BY `id` DESC LIMIT '.$pageSize.' OFFSET '.$start.'';
                        $res = Yii::$app->db->createCommand($sql_str)->queryAll();
                    }
                }
            }
        }
        if($res){
            for($i=0;$i<count($res);$i++){
                $info['image'][] = [
                    'id'=> $res[$i]['id'],
                    'cate_id'=>$cate_id,
                    'category'=>(int)$res[$i]['category'],
                    'theme'=>(int)$res[$i]['theme'],
                    'name'=> $res[$i]['name'],
                    'image'=> 'http://qiniu.zaoanart.com/'.$res[$i]['image'].'?imageView2/2/w/500',
                    'img_height'=> intval($res[$i]['img_height']),
//                    'img_height'=> $imgsize[1],
                ];
            }
            $info['start'] = $start+$pageSize;
//            $condition_str = $cate_name.''.$theme_name.''.$color;
            $infos['condition'] = implode(',',$explode);
            $infos['data'] = $info;
            return $infos;
        }else{
            $infos['condition'] = implode(',',$explode);
            $infos['data'] = false;
            return $infos;
        }
    }

    //获取分类图片
    public function actionGetcateimg()
    {
        $cate_id = $_GET['id'];
        $pageSize = 20;
        $start = $_GET['start'];
        $id_arr = category::find()->select('id')->where(['pid'=>$cate_id])->all();
        $up_cate_search_sum = 'UPDATE `tsy_category` SET `search_sum_wechat`=`search_sum_wechat`+1 where `id`='.$cate_id.' ';
        Yii::$app->db->createCommand($up_cate_search_sum)->execute();
        if(empty($id_arr)){
            $res = Goods::find()
                ->select('id,image,name,img_height,category,theme')
                ->where(['category'=>$cate_id])
                ->andWhere(['or',['<>','category',999],['<>','theme',999]])
                ->andWhere(['is_appear'=>1])
                ->orderBy(['id'=>SORT_DESC])
                ->limit($pageSize)
                ->offset($start)
                ->all();
        }else{
            $cate_id2[] = [
                'id'=>(int)$cate_id
            ];
            $result = array_merge($cate_id2,$id_arr);
            $condition = '';
            for($i = 0;$i<count($result);$i++){
                $condition .= ' or (`category` = '.$result[$i]['id'].')';
                $sql = Goods::find()
                    ->select('id,image,name,img_height,category,theme')
                    ->where(['or',['<>','category',999],['<>','theme',999]])
                    ->andWhere(['is_appear'=>1])
                    ->createCommand()
                    ->getRawSql();
            }
            $sql_str = $sql.' AND ('.substr($condition,3).') AND (`is_appear`=1) ORDER BY `id` DESC LIMIT '.$pageSize.' OFFSET '.$start.'';
            $res = Yii::$app->db->createCommand($sql_str)->queryAll();
        }
        for($i=0;$i<count($res);$i++){
//            $imgsize = getimagesize('http://qiniu.zaoanart.com/'.$res[$i]['image']);
            $info['image'][] = [
                'id'=> intval($res[$i]['id']),
                'cate_id'=>$cate_id,
                'name'=> $res[$i]['name'],
                'category'=> (int)$res[$i]['category'],
                'theme'=> (int)$res[$i]['theme'],
                'image'=> 'http://qiniu.zaoanart.com/'.$res[$i]['image'].'?imageView2/2/w/500',
                'img_height'=> intval($res[$i]['img_height']),
//                'img_height'=> $imgsize[1],
            ];
        }
        $info['start'] = $start+$pageSize;
        return $info;
    }

    //获取主题图片
    public function actionGetthemeimg()
    {
        $theme_id = $_GET['id'];
        $pageSize = 20;
        $start = $_GET['start'];
        $theme_name = Theme::find()->select('theme_name')->where(['id'=>$theme_id])->one();
        $up_theme_search_sum = 'UPDATE `tsy_theme` SET `search_sum_wechat`=`search_sum_wechat`+1 where `id`='.$theme_id.' ';
        Yii::$app->db->createCommand($up_theme_search_sum)->execute();
        $res = Goods::find()
            ->select('id,image,name,img_height,category,theme')
            ->where(['theme'=>$theme_id])
            ->andWhere(['or',['<>','category',999],['<>','theme',999]])
            ->andWhere(['is_appear'=>1])
//            ->indexBy('category')
            ->orderBy(['id'=>SORT_DESC])
            ->limit($pageSize)
            ->offset($start)
            ->all();
//            ->createCommand()
//            ->getRawSql();
        for($i=0;$i<count($res);$i++){
//            $imgsize = getimagesize('http://qiniu.zaoanart.com/'.$res[$i]['image']);
            $info['image'][] = [
                'id'=> $res[$i]['id'],
                'theme_id'=>$theme_id,
                'name'=> $res[$i]['name'],
                'category'=> (int)$res[$i]['category'],
                'theme'=> (int)$res[$i]['theme'],
                'image'=> 'http://qiniu.zaoanart.com/'.$res[$i]['image'].'?imageView2/2/w/500',
                'img_height'=> $res[$i]['img_height'],
//                'img_height'=> $imgsize[1],
            ];
        }
        $info['start'] = $start+$pageSize;
        $infos['data'] = $info;
        $infos['theme_name'] = $theme_name['theme_name'];
        return $infos;
    }

    //获取标签图片
    public function actionGetlabelimg()
    {
        $label_id = $_GET['id'];
        $pageSize = 20;
        $start = $_GET['start'];
        $label_name = label::find()->select('label_name')->where(['id'=>$label_id])->one();
        $up_theme_search_sum = 'UPDATE `tsy_label` SET `search_sum_wechat`=`search_sum_wechat`+1 where `id`='.$label_id.' ';
        Yii::$app->db->createCommand($up_theme_search_sum)->execute();
        $res = Goods::find()
            ->select('id,image,name,img_height,category,theme')
            ->where(['like','label',','.$label_id.','])
            ->andWhere(['or',['<>','category',999],['<>','theme',999]])
            ->andWhere(['is_appear'=>1])
//            ->indexBy('category')
            ->orderBy(['id'=>SORT_DESC])
            ->limit($pageSize)
            ->offset($start)
            ->all();
        for($i=0;$i<count($res);$i++){
//            $imgsize = getimagesize('http://qiniu.zaoanart.com/'.$res[$i]['image']);
            $info['image'][] = [
                'id'=> $res[$i]['id'],
                'label_id'=>$label_id,
                'name'=> $res[$i]['name'],
                'category'=> (int)$res[$i]['category'],
                'theme'=> (int)$res[$i]['theme'],
                'image'=> 'http://qiniu.zaoanart.com/'.$res[$i]['image'].'?imageView2/2/w/500',
                'img_height'=> $res[$i]['img_height'],
//                'img_height'=> $imgsize[1],
            ];
        }
        $info['start'] = $start+$pageSize;
        $infos['data'] = $info;
        $infos['label_name'] = $label_name['label_name'];
        return $infos;
    }

    //获取搜索图片
    public function actionGetsearchimg()
    {
        $param = $_GET['val'];
        $pageSize = 20;
        $start = $_GET['start'];
        $color = '无颜色';
        switch ($param){
            case $param == '红色' : $color = 1;break;
            case $param == '橙色' : $color = 2;break;
            case $param == '黄色' : $color = 3;break;
            case $param == '绿色' : $color = 4;break;
            case $param == '青色' : $color = 5;break;
            case $param == '蓝色' : $color = 6;break;
            case $param == '紫色' : $color = 7;break;
            case $param == '粉色' : $color = 8;break;
            case $param == '白色' : $color = 9;break;
            case $param == '黑色' : $color = 10;break;
            case $param == '灰色' : $color = 11;break;
            case $param == '红' : $color = 1;break;
            case $param == '橙' : $color = 2;break;
            case $param == '黄' : $color = 3;break;
            case $param == '绿' : $color = 4;break;
            case $param == '青' : $color = 5;break;
            case $param == '蓝' : $color = 6;break;
            case $param == '紫' : $color = 7;break;
            case $param == '粉' : $color = 8;break;
            case $param == '白' : $color = 9;break;
            case $param == '黑' : $color = 10;break;
            case $param == '灰' : $color = 11;break;
        }
        $res1 = category::find()->select('id')->where(['like','category_name',$param])->all();
        $res2 = theme::find()->select('id')->where(['like','theme_name',$param])->all();
        $res3 = label::find()->select('id')->where(['like','label_name',$param])->all();
        $label_res = [];
        $cate_res = [];
        $theme_res = [];
        $count1 = '';
        $count2 = '';
        for($k=0;$k<count($res3);$k++){
            $label_res[$k] = Goods::find()
                ->select('id,image,name,img_height,category,theme')
                ->where(['like','label',','.$res3[$k]['id'].','])
                ->andWhere(['is_appear'=>1])
                ->orderBy(['id'=>SORT_DESC])
//                ->limit($pageSize)
//                ->offset($start)
                ->all();
        }
        $count_label = 0;
        $label_result = [];
        for($ks=0;$ks<count($label_res);$ks++){
            $count_label += count($label_res[$ks]);
            for($ky=0;$ky<count($label_res[$ks]);$ky++){
                $label_result[] = $label_res[$ks][$ky];
            }
        }
        $label_result = array_unique($label_result,SORT_REGULAR);
//        return $label_res;
        for($y=0;$y<count($res1);$y++){
            $cate_res[] = $res1[$y]['id'];
        }
        for($z=0;$z<count($res2);$z++){
            $theme_res[] = $res2[$z]['id'];
        }
        $condition = '';
        $sql = '';
        for($q=0;$q<count($res3);$q++) {
            $condition .= ' AND (`label` not like \'%,'.$res3[$q]['id'].',%\' or `label` is null)';
        }
        $sql = Goods::find()
            ->select('id,image,name,category,theme,img_height')
            ->where(['color' => $color])
            ->orFilterWhere(['like', 'id', $param])
            ->orFilterWhere(['like', 'name', $param])
            ->orFilterWhere(['in', 'category', $cate_res])
            ->orFilterWhere(['in', 'theme', $theme_res])
            ->orFilterWhere(['like', 'image', $param])
            ->orFilterWhere(['like', 'author', $param])
            ->orFilterWhere(['like', 'price', $param])
            ->orFilterWhere(['like', 'content', $param])
            ->andWhere(['or', ['<>', 'category', 999], ['<>', 'theme', 999]])
            ->createCommand()
            ->getRawSql();
        $sql_str = $sql.$condition.' AND (`is_appear`=1) ORDER BY `id` DESC';
        $res = Yii::$app->db->createCommand($sql_str)->queryAll();
        $count2 = count($res);
        $count = $count_label + $count2;
        $end_res = [];
        if(count($label_res)>0){
            $end_res = array_merge($res,$label_result);
        }else{
            $end_res = $res;
        }
        $keysValue = [];
        foreach ($end_res as $k => $v) {
            $keysValue[$k] = $v['id'];
        }
        array_multisort($keysValue,SORT_DESC,$end_res);
        $end_result = array_filter(array_unique($end_res,SORT_REGULAR));
        $info = array_slice($end_result,$start,$pageSize,false);
        if($info){
            for($i = 0;$i<count($info);$i++){
//                $imgsize = getimagesize('http://qiniu.zaoanart.com/'.$info[$i]['image']);
                $res4['image'][] = [
                    'id' => $info[$i]['id'],
                    'name' => $info[$i]['name'],
                    'category'=> (int)$res[$i]['category'],
                    'theme'=> (int)$res[$i]['theme'],
                    'image' => 'http://qiniu.zaoanart.com/'.$info[$i]['image'].'?imageView2/2/w/500',
                    'img_height'=> intval($info[$i]['img_height']),
//                    'img_height'=> $imgsize[1],
                ];
            }
            $res4['start'] = $start+$pageSize;
            $infos['data'] = $res4;
            $infos['search'] = $param;
            return $infos;
        }
    }

    //获取图片详情
    public function actionGetimg()
    {
        $img_id = $_GET['id'];
        $up_search_sum = 'UPDATE `tsy_goods` SET `search_sum_wechat`=`search_sum_wechat`+1 where `id`='.$img_id.' ';
        Yii::$app->db->createCommand($up_search_sum)->execute();
        $res = Goods::find()
            ->select('id,name,category,theme,image,img_width')
            ->where(['id'=>$img_id])
            ->andWhere(['is_appear'=>1])
            ->one();
        if($res){
            $info = [
                'id'=>$res['id'],
                'name'=>$res['name'],
                'category'=>$res['category'],
                'theme'=>$res['theme'],
                'img_width'=>$res['img_width'],
                'image'=>'http://qiniu.zaoanart.com/'.$res['image'],
            ];
            return $info;
        }

    }

    //获取相似图片
    public function actionGetlikeimage()
    {
        $pageSize = 20;
        $start = $_GET['start'];
        $label_name_str = '';
        $label_name = [];
        $id = $_GET['id'];
        $labels = Goods::find()->select('label')->where(['id'=>$id])->one();
        $label = array_filter(array_values(array_unique(explode(",",$labels['label']))));
        sort($label);
        $label_str = '';
        for($i = 0; $i < count($label); $i++){
            $label_str .= 'and (`label` LIKE "%,'.$label[$i].',%") ';
            $label_name[] = Label::find()->select('label_name')->where(['id'=>$label[$i]])->one();
        }
        for($q = 0; $q < count($label_name); $q++){
            $label_name_str .= $label_name[$q]['label_name'].',';
        }
        $may_img_arr = [];
        $this->cycles($label,$label,$may_img_arr);
        $res = $this->img_arr;
        if($res){
            for($i = 0;$i<count($res);$i++){
                $res1['image'][] = [
                    'id' => $res[$i]['id'],
                    'category' => $res[$i]['category'],
                    'theme' => $res[$i]['theme'],
                    'image' => 'http://qiniu.zaoanart.com/'.$res[$i]['image'].'?imageView2/2/w/500',
                    'img_height' => (int)$res[$i]['img_height'],
                ];
            }
            $res1['image'] = array_slice($res1['image'],$start,$pageSize,false);
            $res1['start'] = $start+$pageSize;
            return $res1;
        }else{
            return false;
        }

        return $res;
    }
    function cycles($labels,$label,$may_img_arr)
    {
        $id = $_GET['id'];
        if(count($label)!=0){
            $label2 = $label;
            $label_str = '';
            for($i = 0; $i < count($label2); $i++){
                $label_str .= 'and (`label` LIKE "%,'.$label2[$i].',%") ';
            }
            $may_img_arr1 = Yii::$app->db->createCommand("select `id`,`image`,`img_height`,`category`,`theme` from `tsy_goods` where `id` !=".$id." ".$label_str)->queryAll();
            $res = [];
            foreach($may_img_arr as $k=>$v){
                if(!isset($res[$v['id']])){
                    $res[$v['id']]=$v;
                }
            }
            foreach($may_img_arr1 as $k=>$v){
                if(!isset($res[$v['id']])){
                    $res[$v['id']]=$v;
                }
            }
            $res = array_values($res);
            unset($label2[count($label2)-1]);
            $label2 = array_values($label2);
            $this->cycles($labels,$label2,$res);
        }else{
            $this->cycles2($labels,$label,$may_img_arr);
        }
    }
    function cycles2($labels,$label,$may_img_arr)
    {
        $id = $_GET['id'];
        if(count($labels)!=0){
            $label2 = $labels;
            $label_str2 = '';
            for($q = 0; $q < count($label2); $q++){
                $label_str2 .= 'and (`label` LIKE "%,'.$label2[$q].',%") ';
            }
            $may_img_arr2 = Yii::$app->db->createCommand("select `id`,`image`,`img_height`,`category`,`theme` from `tsy_goods` where `id` !=".$id." ".$label_str2)->queryAll();
            $res = [];
            foreach($may_img_arr as $k=>$v){
                if(!isset($res[$v['id']])){
                    $res[$v['id']]=$v;
                }
            }
            foreach($may_img_arr2 as $k=>$v){
                if(!isset($res[$v['id']])){
                    $res[$v['id']]=$v;
                }
            }
            $res = array_values($res);
            unset($label2[0]);
            $label2 = array_values($label2);
            $this->cycles2($label2,$label2,$res);
        }else{
            $this->img_arr = $may_img_arr;
        }
    }

    //获取收藏夹
    public function actionGetkeep()
    {
        $token = $_GET['token'];
        $attention_keep = [];
        if($token){
            $str = strpos($token, '$ZaoAn.u');
            $uid = intval(substr($token,0,$str));
            $attention_keep = attention::find()->select('id,kid')->where(['uid'=>$uid])->all();
        }else{
            $uid = null;
        }
        $keep = keep::find()->select('id,keep_name,uid,img_ratio')->where(['status'=>1])->all();
        if($keep){
            for($i = 0; $i<count($keep);$i++){
                $attention_num = attention::find()->select('id')->where(['kid'=>$keep[$i]['id']])->count();
                $res2[$i] = keepimage::find()->select('id,imgid')->where(['kid'=>$keep[$i]['id']])->limit(4)->orderBy('created_at')->all();
                for ($k = 0; $k<count($res2[$i]);$k++){
                    $res3[$i][] = goods::find()->select('id,name,image')->where(['id'=>$res2[$i][$k]['imgid']])->one();
                    if($res2[$i][$k]['imgid']){
                        $res[$i][] = [
                            'keep_id' => $keep[$i]['id'],
                            'uid' => $keep[$i]['uid'],
                            'keep_name' => $keep[$i]['keep_name'],
                            'img_ratio' => $keep[$i]['img_ratio'],
                            'imgid' => $res2[$i][$k]['imgid'],
                            'attention_num' => $attention_num,
                            'is_attention' => 1,//没有关注
                            'image' => 'http://qiniu.zaoanart.com/'.$res3[$i][$k]['image'].'?imageView2/1/w/200/h/200'
                        ];
                    }
                }
            }
            if($attention_keep){
                for($z=0;$z<count($attention_keep);$z++){
//                    var_dump($attention_keep[$z]['kid']);
                    for($x=0;$x<count($res);$x++){
                        for($c=0;$c<count($res[$x]);$c++){
                            if($attention_keep[$z]['kid'] == (int)$res[$x][$c]['keep_id']){
                                $res[$x][$c]['is_attention'] = 2;
                            }
                        }
                    }
                }
            }
//            for($s=0;$s<count($res);$s++){
//                $num = rand(1,3);
//                for($d=0;$d<count($res[$s]);$d++){
//                    $res[$s][$d]['img_ratio'] = $num;
//                }
//            }
            sort($res);
            return $res;
        }
    }

    //获取收藏夹图片
    public function actionGetkeepimg()
    {
        $keep_id = $_GET['keep_id'];
        $pageSize = 20;
        $token = $_GET['token'];
        $str = strpos($token, '$ZaoAn.u');
        $user_id = intval(substr($token,0,$str));
        $start = $_GET['start'];
        $uid = Keep::find()->select('uid,heat')->where(['id'=>$keep_id])->one();
        $heat = Keep::updateAll(['heat'=>(int)$uid['heat']+1],['id'=>$keep_id]);
        $icon = account::find()->select('icon')->where(['id'=>$uid['uid']])->one();
        $good_id = Keepimage::find()
            ->select('imgid')
            ->where(['kid'=>$keep_id])
            ->limit($pageSize)
            ->offset($start)
            ->all();
        $count = Keepimage::find()
            ->select('imgid')
            ->where(['kid'=>$keep_id])
            ->count();
        for($i=0;$i<count($good_id);$i++){
            $res = Goods::find()
                ->select('id,image,name,img_height,category,theme')
                ->where(['id'=>$good_id[$i]['imgid']])
                ->andWhere(['is_appear'=>1])
                ->one();
            if($res){
                $info['image'][] = [
                    'id'=> $res['id'],
                    'category'=> $res['category'],
                    'theme'=> $res['theme'],
                    'image'=> 'http://qiniu.zaoanart.com/'.$res['image'],
                    'name'=> $res['name'],
                    'img_height'=> (int)$res['img_height'],
                    'checked'=>false,
                ];
            }
        }
        $is_attention = false;
        if($user_id){
            $attention = attention::find()->select('id')->where(['uid'=>$user_id,'kid'=>$keep_id])->all();
            if($attention){
                $is_attention = true;
            }else{
                $is_attention = false;
            }
        }
        $attention_num = attention::find()->select('id')->where(['kid'=>$keep_id])->count();
        $info['start'] = $start+$pageSize;
        $info['count'] = $count;
        $info['is_attention'] = $is_attention;
        $info['attention_num'] = (int)$uid['heat'];
//        $info['icon'] = 'http://qiniu.zaoanart.com/'.$icon['icon'];
        $info['icon'] = 'http://www.zaoanart.com:8000/userIcon/'.$icon['icon'];
        return $info;
    }

    //获取装裱图框
    public function actionGetborderimg()
    {
        $res = BorderMaterial::find()->select('id,img_name,sid,border_name,preview_img,price,face_width,Thickness')->all();
        for($i=0;$i<count($res);$i++){
            $num = $res[$i]['sid'];
            $info[$num][] = [
                'id' =>$res[$i]['id'],
                'img_name' =>$res[$i]['img_name'],
                'border_name' =>$res[$i]['border_name'],
                'preview_img' =>'http://www.zaoanart.com:8000/test/preview/'.$res[$i]['preview_img'],
                'price' =>$res[$i]['price'],
                'sid' => $res[$i]['sid'],
                'face_width' =>$res[$i]['face_width'],
                'Thickness' =>$res[$i]['Thickness'],
                'border_name2' =>substr($res[$i]['border_name'],0,strpos($res[$i]['border_name'], '_')),
            ];
        }
        ksort($info);
        return $info;
    }

    //获取画框色系
    public function actionGetborderseries()
    {
        $res = Boxseries::find()->select('id,series_name')->all();
        ksort($res);
        return $res;
    }

    //装裱
    public function actionDecoration()
    {
        $border_name = 'http://qiniu.zaoanart.com/'.$_GET['border_name'];
        $img_name = $_GET['img_name'];
        $face_width = $_GET['face_width']*15;
        $box_img_width = intval($_GET['box_width']);
        $box_img_height = intval($_GET['box_height']);
        //获取装框图像大小
        $image_size = getimagesize($img_name);
        $image_size2 = getimagesize($border_name);
        $face = $image_size2[0];
        $img_width = $image_size[0];
        $img_height = $image_size[1];
        //缩小比例
        $small_bili = $img_width/$box_img_width;
        $face_widths = intval($face_width/$small_bili);
        //图片比例
        $bili = $img_width/$img_height;
        //图片保存路径
        $path = Yii::getAlias('@backend').'\web\test\\';
        //设置头部
//        header("Content-type:image/png;");
        //框宽(外框宽)
        $box_width = $box_img_width;
        //框高(外框高)
        $box_height = $box_img_height;
        //主图
        $main_img = imagecreatefromjpeg($img_name);
        //左图
        $left_img = imagecreatetruecolor($face_widths,$box_height);
        //源图像
        $root_img = imagecreatefromjpeg($border_name);
        //生成一个画板
        $new_img = imagecreatetruecolor($box_img_width,$box_img_height);
        //设置背景颜色(0.255.255, 65535)
        $color = imagecolorallocate($new_img, 255, 255, 255);
        //填充颜色
        imagefill($new_img,0,0,$color);
//        imagecolortransparent($new_img,65280);
        //复制主图
//        imagecopyresized($new_img,$main_img,$face_widths,$face_widths,0,0,$box_img_width-$face_widths*2,$box_img_height-$face_widths*2,$img_width,$img_height);
        imagecopyresampled($new_img,$main_img,$face_widths,$face_widths,0,0,$box_img_width-$face_widths*2,$box_img_height-$face_widths*2,$img_width,$img_height);
        //copy所需画框区域(拉伸)
        imagecopyresampled($left_img,$root_img,0,0,0,0,$face_widths,$box_height,$face,3600);
        //设置三角形顶点位置
        $points = [
            0,0,
            $face,0,
            $face,$face
        ];
        //设置颜色
        $blue = imagecolorallocate($left_img, 0, 255, 0);
        //画三角形
        imagefilledpolygon($left_img,$points,3,$blue);
        //设置三角形顶点位置
        $points2 = [
            0,$box_height,
            $face,$box_height,
            $face,$box_height-$face,
        ];
        //设置颜色
        $green = imagecolorallocate($left_img, 0, 255, 0);
        //画三角形
        imagefilledpolygon($left_img,$points2,3,$green);
        //设置透明色
        imagecolortransparent($left_img,65280);
        //下图
        $bottom_img = imagecreatetruecolor($box_width,$face_widths);
        //逆时针旋转90度生成下边图
        $root_img = imagerotate($root_img,90,0);
        //copy所需画框区域(拉伸)
        imagecopyresampled($bottom_img,$root_img,0,0,0,0,$box_width,$face_widths,3600,$face);
        //复制下框
        imagecopyresized($new_img,$bottom_img,0,$box_img_height-$face_widths,0,0,$box_img_width,$face_widths,$box_img_width,$face_widths);
        //垂直对称下图生成上图
        imageflip($bottom_img,IMG_FLIP_VERTICAL);
        //复制上框
        imagecopyresized($new_img,$bottom_img,0,0,0,0,$box_img_width,$face_widths,$box_img_width,$face_widths);
        //复制左框
        imagecopyresized($new_img,$left_img,0,0,0,0,$face_widths,$box_img_height,$face_widths,$box_img_height);
        //垂直对称左图生成右图
        imageflip($left_img,IMG_FLIP_HORIZONTAL);
        //复制右框
        imagecopyresized($new_img,$left_img,$box_img_width-$face_widths,0,0,0,$face_widths,$box_img_height,$face_widths,$box_img_height);
//        imagecolortransparent($new_img,65280);
        imagepng($new_img,$path.'bgimg.png');
        $data = time();
        $num = $data + rand(1,9999);
        //边框图片
        $img_info = filesize($path.'bgimg.png');
        $fp = fopen($path.'bgimg.png', "r");
        $content = fread($fp,$img_info);
        $img_str = chunk_split(base64_encode($content));
        $img_base64 = $img_str;
        //水印图片
        $shuiyin_img2 = Yii::getAlias('@backend').'\web\test\logo.png';
        $img_info1 = filesize($shuiyin_img2);
        $fp1 = fopen($shuiyin_img2, "r");
        $content1 = fread($fp1,$img_info1);
        $img_str1 = chunk_split(base64_encode($content1));
        $img_base641 = $img_str1;
        $info = [
            'url'=>$img_base64,
            'shuiyin_url'=>$img_base641
        ];
        return $info;
    }

    //登录
    public function actionLogin()
    {
        $username = $_GET['username'];
        $password = $_GET['password'];
        $res = Account::find()->select('id,password')->where(['phone'=>$username])->andWhere(['is_deleted'=>0])->one();
        $name = 'ZaoanArt';
        $data = date('Y.m.d-H:i:s');
        $rand = rand(1,9999);
        $str = md5($name.$data.$rand);
        $token = $res['id'].'$ZaoAn.u'.password_hash($str.$password,PASSWORD_DEFAULT);
        $result = Account::updateAll(['last_login_time'=>date("Y-m-d H:i:s"),'token'=>$token],['id'=>$res['id'],'phone'=>$username]);
        if($result){
            $info['token'] = $token;
            $info['user_id'] = $res['id'];
            return $info;
        }else{
            return false;
        }
    }

    //获取用户收藏夹
    public function actionGetukeep()
    {
        $token = $_GET['token'];
        $str = strpos($token, '$ZaoAn.u');
        $uid = intval(substr($token,0,$str));
        if($uid){
            $res = Keep::find()->select('id,keep_name,heat')->where(['uid'=>$uid])->all();
            if($res){
                for($i=0;$i<count($res);$i++){
                    $img = Keepimage::find()->select('imgid')->where(['kid'=>$res[$i]['id']])->orderBy(['id'=>SORT_DESC])->one();
                    $img_url = goods::find()->select('image')->where(['id'=>$img['imgid']])->one();
                    $info[] = [
                        'uid'=>$uid,
                        'id'=>$res[$i]['id'],
                        'keep_name'=>$res[$i]['keep_name'],
                        'heat'=>$res[$i]['heat'],
                        'image'=>'http://qiniu.zaoanart.com/'.$img_url['image'].'?imageView2/2/h/100'
                    ];
                }
                return $info;
            }else{
                return 1;//暂无收藏夹
            }
        }
    }

    //图片添加到收藏夹
    public function actionAddkeep()
    {
        if($_GET){
            $uid = $_GET['uid'];
            $kid = $_GET['kid'];
            $img_id = $_GET['img_id'];
        }else{
            return false;
        }
        $up_goods_keep_sum = 'UPDATE `tsy_goods` SET `keep_sum_wechat`=`keep_sum_wechat`+1 where `id`='.$img_id.' ';
        Yii::$app->db->createCommand($up_goods_keep_sum)->execute();
        $res = keep::updateAll(['updated_at'=>date("Y-m-d H:i:s")],['uid'=>$uid,'id'=>$kid]);
        $img = keepimage::find()->where(['kid'=>$kid,'imgid' => $img_id,])->all();
        if($img){
            return 1;
        }
        $res2 = Yii::$app->db->createCommand()
            ->insert('tsy_keep_image',[
                'imgid' => $img_id,
                'created_at'=>date("Y-m-d H:i:s"),
                'kid'=>$kid
            ])
            ->execute();
        if($res2){
            return 0;
        }
    }

    //获取我的收藏夹
    public function actionGetmykeep()
    {
        $token = $_GET['token'];
        $str = strpos($token, '$ZaoAn.u');
        $uid = intval(substr($token,0,$str));
        $keep = keep::find()->select('id,keep_name,img_ratio,heat')->where(['uid'=>$uid])->all();
        $res = [];
        if($keep){
            for($i = 0; $i<count($keep);$i++){
                $attention_num = attention::find()->select('id')->where(['kid'=>$keep[$i]['id']])->count();
                $res2 = keepimage::find()->select('id,imgid')->where(['kid'=>$keep[$i]['id']])->limit(4)->all();
                if(count($res2)<=0){
                    $res[$i][] = [
                        'keep_id' => $keep[$i]['id'],
                        'keep_name' => $keep[$i]['keep_name'],
                        'img_ratio' => '1',
                        'attention_num' => $attention_num,
                        'heat' => $keep[$i]['heat'],
                        'imgid' => '',
                        'image' => ''
                    ];
                }else{
                    for ($k = 0; $k<count($res2);$k++){
                        $res3[$i][] = goods::find()->select('id,name,image')->where(['id'=>$res2[$k]['imgid']])->one();
                        if($res2[$k]['imgid']){
                            $res[$i][] = [
                                'keep_id' => $keep[$i]['id'],
                                'keep_name' => $keep[$i]['keep_name'],
                                'img_ratio' => $keep[$i]['img_ratio'],
                                'attention_num' => $attention_num,
                                'heat' => $keep[$i]['heat'],
                                'imgid' => $res2[$k]['imgid'],
                                'image' => 'http://qiniu.zaoanart.com/'.$res3[$i][$k]['image'].'?imageView2/2/h/200'
                            ];
                        }
                    }
                }
            }
//            for($s=0;$s<count($res);$s++){
//                $num = rand(1,3);
//                for($d=0;$d<count($res[$s]);$d++){
//                    $res[$s][$d]['img_ratio'] = $num;
//                }
//            }
            sort($res);
            return $res;
        }
    }

    //获取用户名和头像
    public function actionGetusername()
    {
        $token = $_GET['token'];
        $str = strpos($token, '$ZaoAn.u');
        $uid = intval(substr($token,0,$str));
        $username = account::find()->select('username,icon')->where(['id'=>$uid])->one();
        $my_attention = attention::find()->select('id,kid,uid')->where(['uid'=>$uid])->count();
        $attention_user = attentionUser::find()->select('id')->where(['uid'=>$uid])->count();
        $info = [
            'username'=>$username['username'],
//            'icon'=>'http://qiniu.zaoanart.com/'.$username['icon'],
            'icon'=>'http://www.zaoanart.com:8000/userIcon/'.$username['icon'],
            'my_attention'=>$my_attention,
            'attention_user_num'=>$attention_user,
            'uid'=>$uid
        ];
        return $info;
    }

    //获取用户名和头像
    public function actionGetusername3()
    {
        $uid = $_GET['uid'];
        $username = account::find()->select('username,icon')->where(['id'=>$uid])->one();
        $my_attention = attention::find()->select('id,kid,uid')->where(['uid'=>$uid])->count();
        $attention_user = attentionUser::find()->select('id')->where(['uid'=>$uid])->count();
        $info = [
            'username'=>$username['username'],
//            'icon'=>'http://qiniu.zaoanart.com/'.$username['icon'],
            'icon'=>'http://www.zaoanart.com:8000/userIcon/'.$username['icon'],
            'my_attention'=>$my_attention,
            'attention_user_num'=>$attention_user,
            'uid'=>$uid
        ];
        return $info;
    }

    //获取用户名和头像
    public function actionGetusername2()
    {
        $attention_uid = intval($_GET['attention_uid']);
        $is_attention_user = 1;
        $attention_keep_num = 0;
        if($_GET['token']){
            $token = $_GET['token'];
            $str = strpos($token, '$ZaoAn.u');
            $user_id = intval(substr($token,0,$str));
            $attention_user = attentionUser::find()->select('id')->where(['uid'=>$user_id,'attention_uid'=>$attention_uid])->all();
            if($attention_user){
                $is_attention_user = 2;//已经关注
            }else{
                $is_attention_user = 1;//未关注
            }
        }
        $username = account::find()->select('username,icon')->where(['id'=>$attention_uid])->one();
        $my_attention = attention::find()->select('id,kid,uid')->where(['uid'=>$attention_uid])->count();
        $attention_user_num = attentionUser::find()->select('id')->where(['uid'=>$attention_uid])->count();
        $info = [
            'username'=>$username['username'],
//            'icon'=>'http://qiniu.zaoanart.com/'.$username['icon'],
            'icon'=>'http://www.zaoanart.com:8000/userIcon/'.$username['icon'],
            'my_attention'=>$my_attention,
            'is_attention_user'=>$is_attention_user,
            'attention_user_num'=>$attention_user_num
        ];
        return $info;
    }

    //添加收藏夹
    public function actionAddnewkeep()
    {
        $token = $_GET['token'];
        $str = strpos($token, '$ZaoAn.u');
        $uid = intval(substr($token,0,$str));
        $keep_name = $_GET['keep_name'];
        $res = keep::find()->select('id')->where(['keep_name'=>$keep_name])->andWhere(['uid'=>$uid])->all();
        if($res){
            return 1;//收藏夹名字重复
        }
        $res = Yii::$app->db->createCommand()
            ->insert('tsy_keep',[
                'uid' => $uid,
                'keep_name' => $keep_name,
                'created_at'=>date("Y-m-d H:i:s"),
                'img_ratio'=>$num = rand(1,3),
            ])
            ->execute();
        $kid = Yii::$app->db->getLastInsertId();
        if($kid){
            return 0;//添加成功
        }else{
            return 2;//添加失败
        }
    }

    //修改收藏夹名
    public function actionUpdatekeep()
    {
        $kid = intval($_GET['keep_id']);
        $keep_name = $_GET['keep_name'];
        $token = $_GET['token'];
        $str = strpos($token, '$ZaoAn.u');
        $uid = intval(substr($token,0,$str));
        $res = keep::find()->select('id')->where(['uid'=>$uid])->andWhere(['keep_name'=>$keep_name])->all();
        if($res){
            return 1;
        }
        $res2 = keep::updateAll(['keep_name' => $keep_name], ['id'=>$kid,'uid'=>$uid]);
        if($res2){
            return 0;
        }
    }

    //删除收藏夹
    public function actionDeletekeep()
    {
        $kid = intval($_GET['keep_id']);
        $token = $_GET['token'];
        $str = strpos($token, '$ZaoAn.u');
        $uid = intval(substr($token,0,$str));
        if($kid && $uid){
            $res = keepimage::deleteAll(['kid'=>$kid]);
            $res2 = keep::deleteAll(['id'=>$kid,'uid'=>$uid]);
            if($res || $res2){
                return 0;
            }else{
                return 1;
            }
        }
    }

    //获取用户收藏夹2
    public function actionGetukeep2()
    {
        $keep_id = intval($_GET['keep_id']);
        $token = $_GET['token'];
        $str = strpos($token, '$ZaoAn.u');
        $uid = intval(substr($token,0,$str));
        if($uid){
            $res = Keep::find()->select('id,keep_name,heat')->where(['uid'=>$uid])->andWhere(['<>','id',$keep_id])->all();
            if($res){
                for($i=0;$i<count($res);$i++){
                    $info[] = [
                        'uid'=>$uid,
                        'id'=>$res[$i]['id'],
                        'keep_name'=>$res[$i]['keep_name'],
                        'heat'=>$res[$i]['heat'],
                    ];
                }
                return $info;
            }else{
                return 1;//暂无收藏夹
            }
        }
    }

    //获取用户收藏夹3
    public function actionGetukeeps()
    {
        $keep_id = intval($_GET['keep_id']);
        $token = $_GET['token'];
        $str = strpos($token, '$ZaoAn.u');
        $uid = intval(substr($token,0,$str));
        if($uid){
            $res = Keep::find()->select('id,keep_name,heat')->where(['uid'=>$uid])->andWhere(['<>','id',$keep_id])->all();
            if($res){
                for($i=0;$i<count($res);$i++){
                    $img = Keepimage::find()->select('imgid')->where(['kid'=>$res[$i]['id']])->orderBy(['id'=>SORT_DESC])->one();
                    $img_url = goods::find()->select('image')->where(['id'=>$img['imgid']])->one();
                    $info[] = [
                        'uid'=>$uid,
                        'id'=>$res[$i]['id'],
                        'keep_name'=>$res[$i]['keep_name'],
                        'heat'=>$res[$i]['heat'],
                        'image' => 'http://qiniu.zaoanart.com/'.$img_url['image'].'?imageView2/2/h/100'
                    ];
                }
                return $info;
            }else{
                return 1;//暂无收藏夹
            }
        }
    }

    //批量删除
    public function actionDeleteimg()
    {
        $keep_id = intval($_GET['keep']);
        $token = $_GET['token'];
        $str = strpos($token, '$ZaoAn.u');
        $uid = intval(substr($token,0,$str));
        $img_id = explode(",", $_GET['img_id']);
        $img_id = array_filter($img_id);
        sort($img_id);
        if($keep_id && $uid && $img_id){
            for($i=0;$i<count($img_id);$i++){
                $res = keepimage::deleteAll(['imgid'=>intval($img_id[$i]),'kid'=>$keep_id]);
            }
            if($res){
                return 1;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    //批量移动
    public function actionMoveimg()
    {
        $token = $_GET['token'];
        $str = strpos($token, '$ZaoAn.u');
        $uid = intval(substr($token,0,$str));
        $new_keep = intval($_GET['to_keep']);
        $old_keep = intval($_GET['keep']);
        $img_id = explode(",", $_GET['img_id']);
        $img_id = array_filter($img_id);
        sort($img_id);
        $res1 = $res2 = false;
        for($i=0;$i<count($img_id);$i++){
            $res = keepimage::find()->where(['imgid'=>$img_id[$i],'kid'=>$new_keep])->one();
            if($res){
                $res1 = keepimage::deleteAll(['imgid'=>$img_id[$i],'kid'=>$old_keep]);
                continue;
            }else{
                $res2 = keepimage::updateAll(['updated_at'=>date("Y-m-d H:i:s"),'kid'=>$new_keep],['kid'=>$old_keep,'imgid'=>$img_id[$i]]);
            }
        }
        if($res1 || $res2){
            return 1;
        }else{
            return false;
        }
    }

    //批量复制
    public function actionCopyimg()
    {
        $token = $_GET['token'];
        $str = strpos($token, '$ZaoAn.u');
        $uid = intval(substr($token,0,$str));
        $new_keep = intval($_GET['to_keep']);
        $old_keep = intval($_GET['keep']);
        $img_id = explode(",", $_GET['img_id']);
        $img_id = array_filter($img_id);
        sort($img_id);
        $res = $res2 = false;
        for($i=0;$i<count($img_id);$i++){
            $res = keepimage::find()->where(['imgid'=>$img_id[$i],'kid'=>$new_keep])->all();
            if($res){
                continue;
            }else{
                $res2 = Yii::$app->db->createCommand()
                    ->insert('tsy_keep_image',[
                        'imgid' => $img_id[$i],
                        'created_at'=>date("Y-m-d H:i:s"),
                        'kid'=>$new_keep
                    ])
                    ->execute();
            }
        }
        if($res || $res2){
            return 1;
        }else{
            return 2;
        }
    }

    //提交建议
    public function actionMessage()
    {
        $token = $_GET['token'];
        $str = strpos($token, '$ZaoAn.u');
        $uid = intval(substr($token,0,$str));
        $content = $_GET['content'];
        $data = date("Y-m-d");
        $is_overflow = Message::find()->select('id')->where(['created_at2'=>$data])->all();
        if(count($is_overflow) >= 10){
            return 1;//当日留言超过10条
        }else{
            $res = Yii::$app->db->createCommand()
                ->insert('tsy_message',[
                    'uid' => $uid,
                    'content' => $content,
                    'created_at' => date("Y-m-d H:i:s"),
                    'created_at2' => $data
                ])
                ->execute();
            if($res){
                return 0;//留言成功
            }else{
                return 2;//留言失败
            }
        }
    }

    //获取微信手机号
    public function actionGetwxphone()
    {
        $code = $_GET['code'];
        $appid = "wxa0a3e0beee9f0e98";
        $secret = "76bc2ec854b8de862ffc94a2676839ba";
        $url = 'https://api.weixin.qq.com/sns/jscode2session?appid='.$appid.'&secret='.$secret.'&js_code='.$code.'&grant_type=authorization_code';
        $info = file_get_contents($url);//get请求网址，获取数据
        $jsonObj = json_decode($info);
        return $jsonObj;
    }

    //解密手机号并登录
    public function actionDecrypttel()
    {
        $aesKey = base64_decode($_GET['session_key']);
        $aesIV = base64_decode($_GET['ivdata']);
        $aesCipher = base64_decode($_GET['encrypdata']);
        $result = openssl_decrypt( $aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);
        $dataObj=json_decode($result);
        if($dataObj == null){
            return 1;//解密失败
        }
        if( $dataObj->watermark->appid != 'wxa0a3e0beee9f0e98')
        {
            return 1;//解密失败
        }
        $tel = $dataObj->phoneNumber;
        $uid = account::find()->select('id,is_deleted')->where(['phone'=>$tel])->one();
        if(!$uid){
            $result = Yii::$app->db->createCommand()
                ->insert('tsy_account',[
                    'username'=>$tel,
                    'phone'=>$tel,
                    'password'=>md5(123456),
                    'icon'=>'default_icon.jpg'
                ])
                ->execute();
            $uid = Yii::$app->db->getLastInsertId();
            $up_channel = 'UPDATE `tsy_account_channel` SET `count`=`count`+1 where `id`=2 ';
            Yii::$app->db->createCommand($up_channel)->execute();
            if($result){
                //登录
                $name = 'ZaoanArt';
                $password = md5(123456);
                $data = date('Y.m.d-H:i:s');
                $rand = rand(1,9999);
                $str = md5($name.$data.$rand);
                $token = $uid.'$ZaoAn.u'.password_hash($str.$password,PASSWORD_DEFAULT);
                $result = Account::updateAll(['last_login_time'=>date("Y-m-d H:i:s"),'token'=>$token],['id'=>$uid,'phone'=>$tel]);
                if($result){
                    return $token;
                }else{
                    return 3;
                }
            }

        }else{
            if($uid['is_deleted'] == 1){
                return 2;//该手机号被禁止登录或者被删除
            }else{
                //登录
                $name = 'ZaoanArt';
                $data = date('Y.m.d-H:i:s');
                $rand = rand(1,9999);
                $str = md5($name.$data.$rand);
                $token = $uid['id'].'$ZaoAn.u'.password_hash($str,PASSWORD_DEFAULT);
                $result = Account::updateAll(['last_login_time'=>date("Y-m-d H:i:s"),'token'=>$token],['id'=>$uid['id'],'phone'=>$tel]);
                if($result){
                    return $token;
                }else{
                    return 3;
                }
            }
        }
    }

    //生成水印参数
    public function actionJiashuiyin()
    {
        $ImageURL = 'http://qiniu.zaoanart.com/20190409074000729034.jpg';
        $shuiyin_img = 'http://www.zaoanart.com:8000/images/zaoanart_logo_shuiyin.png';
        $shuiyin_img2 = Yii::getAlias('@backend').'\web\test\logo.png';
        $img_info = filesize($shuiyin_img2);
        $fp = fopen($shuiyin_img2, "r");
        $content = fread($fp,$img_info);
        $img_str = chunk_split(base64_encode($content));
        $img_base64 = $img_str;
        return $img_base64;
        var_dump($img_base64);die;

//        $base64URL = Qiniu\base64_urlSafeEncode($ImageURL);
        $find = array('+', '/');
        $replace = array('-', '_');
        $url = str_replace($find, $replace, base64_encode($shuiyin_img2));
        var_dump(base64_encode($shuiyin_img2));die;
    }

    //记录访问
    public function actionRecord_access_wechat()
    {
        date_default_timezone_set('PRC');
        $sql = 'select `id` from `tsy_access_wechat` where TO_DAYS(`created_at`) = TO_DAYS(NOW()) AND hour(`created_at`) = hour(NOW()) ';
        $id = Yii::$app->db->createCommand($sql)->queryAll();
        if($id){
            $up_access_sum = 'UPDATE `tsy_access_wechat` SET `access_sum`=`access_sum`+1 where `id`='.$id[0]['id'].' ';
            Yii::$app->db->createCommand($up_access_sum)->execute();
        }else{
            $res = Yii::$app->db->createCommand()
                ->insert('tsy_access_wechat',[
                    'access_sum' => 1,
                    'created_at' => date("Y-m-d H:i:s"),
                ])
                ->execute();
        }
    }

    //三级页添加收藏获取分类和主题
    public function actionGetcate_theme()
    {
        $category = $_GET['category'];
        $theme = $_GET['theme'];
        $res = category::find()->select('category_name')->where(['id'=>$category])->one();
        $res2 = theme::find()->select('theme_name')->where(['id'=>$theme])->one();
        $info = [
            'category_name' => $res['category_name'],
            'theme_name' => $res2['theme_name'],
        ];
        return $info;
    }

    //三级页获取图片标签
    public function actionGetimglabel()
    {
        $id = $_GET['id'];
        $res = goods::find()
            ->select('label')
            ->where(['id'=>$id])
            ->one();
        $res_arr =  explode(",",$res['label']);
        $res_arr = array_filter($res_arr);
        sort($res_arr);
        $label_names = [];
        for($i=0;$i<count($res_arr);$i++){
            $lebrl_name = label::find()->select('id,label_name')->where(['id'=>$res_arr[$i]])->one();
            $label_names[] = [
                'id' => $lebrl_name['id'],
                'label_name' => $lebrl_name['label_name'],
            ];
        }
        return $label_names;
    }

    //添加关注
    public function actionAdd_attention()
    {
        $token = $_GET['token'];
        $str = strpos($token, '$ZaoAn.u');
        $uid = intval(substr($token,0,$str));
        $keep_id = intval($_GET['keep_id']);
        if($uid && $keep_id){
            $res = attention::find()->select('id')->where(['uid'=>$uid,'kid'=>$keep_id])->all();
            if($res){
                return 3;//该收藏夹已经添加
            }else{
                $res2 = Yii::$app->db->createCommand()
                    ->insert('tsy_attention',[
                        'uid' => $uid,
                        'kid' => $keep_id,
                        'created_at' => date("Y-m-d H:i:s"),
                    ])
                    ->execute();
                if($res2){
                    return 1;//添加关注成功
                }else{
                    return 2;//添加关注失败
                }
            }
        }
    }

    //取消关注
    public function actionDel_attention()
    {
        $token = $_GET['token'];
        $str = strpos($token, '$ZaoAn.u');
        $uid = intval(substr($token,0,$str));
        $keep_id = intval($_GET['keep_id']);
        if($uid && $keep_id){
            $res = attention::find()->select('id')->where(['uid'=>$uid,'kid'=>$keep_id])->all();
            if(!$res){
                return 3;//该收藏夹没有关注
            }else{
                $res2 = attention::deleteAll(['uid'=>$uid,'kid'=>$keep_id]);
                if($res2){
                    return 1;//取消关注成功
                }else{
                    return 2;//取消关注失败
                }
            }
        }
    }

    //获取当前用户的收藏夹
    public function actionGetcurrentkeep()
    {
        $current_uid = $_GET['uid'];
        $token = $_GET['token'];
        $attention_keep = [];
        $username = account::find()->select('username')->where(['id'=>$current_uid])->one();
        $keep = keep::find()->select('id,keep_name,uid,img_ratio')->where(['uid'=>$current_uid])->all();
        if($token){
            $str = strpos($token, '$ZaoAn.u');
            $uid = intval(substr($token,0,$str));
            $attention_keep = attention::find()->select('id,kid')->where(['uid'=>$uid])->all();
        }else{
            $uid = null;
        }
        if($keep){
            for($i = 0; $i<count($keep);$i++){
                $attention_num = attention::find()->select('id')->where(['kid'=>$keep[$i]['id']])->count();
                $res2[$i] = keepimage::find()->select('id,imgid')->where(['kid'=>$keep[$i]['id']])->limit(4)->orderBy('created_at')->all();
                for ($k = 0; $k<count($res2[$i]);$k++){
                    $res3[$i][] = goods::find()->select('id,name,image')->where(['id'=>$res2[$i][$k]['imgid']])->one();
                    if($res2[$i][$k]['imgid']){
                        $res[$i][] = [
                            'keep_id' => $keep[$i]['id'],
                            'uid' => $keep[$i]['uid'],
                            'username' => $username['username'],
                            'keep_name' => $keep[$i]['keep_name'],
                            'img_ratio' => $keep[$i]['img_ratio'],
                            'imgid' => $res2[$i][$k]['imgid'],
                            'attention_num' => $attention_num,
                            'is_attention' => 1,//没有关注
                            'image' => 'http://qiniu.zaoanart.com/'.$res3[$i][$k]['image'].'?imageView2/1/w/200/h/200'
                        ];
                    }
                }
            }
            if($attention_keep){
                for($z=0;$z<count($attention_keep);$z++){
                    for($x=0;$x<count($res);$x++){
                        for($c=0;$c<count($res[$x]);$c++){
                            if($attention_keep[$z]['kid'] == (int)$res[$x][$c]['keep_id']){
                                $res[$x][$c]['is_attention'] = 2;
                            }
                        }
                    }
                }
            }
            sort($res);
            return $res;
        }
    }

    //获取我关注的收藏夹
    public function actionGetmyfocus()
    {
//        $token = $_GET['token'];
//        $str = strpos($token, '$ZaoAn.u');
        $uid = intval($_GET['uid']);
        $attention_keep = attention::find()->select('id,kid,uid')->where(['uid'=>$uid])->all();
        $attention_num = count($attention_keep);
        if($attention_keep){
            for($i = 0; $i<count($attention_keep);$i++){
                $res2 = keepimage::find()->select('id,imgid')->where(['kid'=>$attention_keep[$i]['kid']])->limit(4)->all();
                $keep = keep::find()->select('id,keep_name,uid,img_ratio,heat')->where(['id'=>$attention_keep[$i]['kid']])->one();
                if(count($res2)<=0){
                    $res[$i][] = [
                        'keep_id' => $attention_keep[$i]['kid'],
                        'keep_name' => $keep['keep_name'],
                        'uid'=>$keep['uid'],
                        'img_ratio' => '1',
                        'attention_num' => $attention_num,
                        'heat' => $keep['heat'],
                        'imgid' => '',
                        'is_attention'=>2,
                        'image' => ''
                    ];
                }else{
                    for ($k = 0; $k<count($res2);$k++){
                        $res3[$i][] = goods::find()->select('id,name,image')->where(['id'=>$res2[$k]['imgid']])->one();
                        if($res2[$k]['imgid']){
                            $res[$i][] = [
                                'keep_id' => $attention_keep[$i]['kid'],
                                'keep_name' => $keep['keep_name'],
                                'img_ratio' => $keep['img_ratio'],
                                'uid'=>$keep['uid'],
                                'attention_num' => $attention_num,
                                'heat' => $keep['heat'],
                                'imgid' => $res2[$k]['imgid'],
                                'is_attention'=>2,
                                'image' => 'http://qiniu.zaoanart.com/'.$res3[$i][$k]['image'].'?imageView2/2/h/200'
                            ];
                        }
                    }
                }
            }
            sort($res);
            return $res;
        }
    }

    //关注用户
    public function actionAdd_attention_user()
    {
        $token = $_GET['token'];
        $str = strpos($token, '$ZaoAn.u');
        $uid = intval(substr($token,0,$str));
        $attention_uid = $_GET['uid'];
        if($uid && $attention_uid){
            $res = attentionUser::find()->select('id')->where(['uid'=>$uid,'attention_uid'=>$attention_uid])->all();
            if($res){
                return 3;//该用户已经关注
            }else{
                $res2 = Yii::$app->db->createCommand()
                    ->insert('tsy_attention_user',[
                        'uid' => $uid,
                        'attention_uid' => $attention_uid,
                        'created_at' => date("Y-m-d H:i:s"),
                    ])
                    ->execute();
                if($res2){
                    return 1;//添加关注成功
                }else{
                    return 2;//添加关注失败
                }
            }
        }
    }

    //取消关注用户
    public function actionDel_attention_user()
    {
        $token = $_GET['token'];
        $str = strpos($token, '$ZaoAn.u');
        $uid = intval(substr($token,0,$str));
        $attention_uid = $_GET['uid'];
        if($uid && $attention_uid){
            $res = attentionUser::find()->select('id')->where(['uid'=>$uid,'attention_uid'=>$attention_uid])->all();
            if(!$res){
                return 3;//该收藏夹没有关注
            }else{
                $res2 = attentionUser::deleteAll(['uid'=>$uid,'attention_uid'=>$attention_uid]);
                if($res2){
                    return 1;//取消关注成功
                }else{
                    return 2;//取消关注失败
                }
            }
        }
    }

    //获取关注的人
    public function actionGetattentionuser()
    {
        $uid = (int)$_GET['uid'];
        $token = $_GET['token'];
        $str = strpos($token, '$ZaoAn.u');
        $user_id = intval(substr($token,0,$str));
        $res = attentionUser::find()->select('id,attention_uid')->where(['uid'=>$uid])->all();
        $user_info = [];
        for($i=0;$i<count($res);$i++){
            $res2 = account::find()->select('id,username,icon')->where(['id'=>$res[$i]['attention_uid']])->one();
            $attention_keep = attention::find()->select('id')->where(['uid'=>$res2['id']])->count();
            $attention_user = attentionUser::find()->select('id')->where(['uid'=>$res2['id']])->count();
            $is_attention = attentionUser::find()->select('id')->where(['uid'=>$user_id,'attention_uid'=>$res2['id']])->all();
            if($is_attention){
                $is_attentions = true;
            }else{
                $is_attentions = false;
            }
            $user_info[] = [
                'user_id'=>$uid,
                'uid'=>$res2['id'],
                'username'=>$res2['username'],
                'attention_keep'=>$attention_keep,
                'attention_user'=>$attention_user,
                'is_attention'=>$is_attentions,
//                'image'=>'http://qiniu.zaoanart.com/'.$res2['icon'],
                'image'=>'http://www.zaoanart.com:8000/userIcon/'.$res2['icon'],
            ];
        }
        return $user_info;
    }

    //修改用户昵称
    public function actionEdit_username()
    {
        $token = $_GET['token'];
        $str = strpos($token, '$ZaoAn.u');
        $uid = intval(substr($token,0,$str));
        $username = $_GET['username'];
        $res = account::find()->select('username')->where(['id'=>$uid])->one();
        if($res['username'] == $username){
            return 1;//昵称重复
        }else{
            $res2 = account::updateAll(['username' => $username], ['id'=>$uid]);
            if($res2){
                return 2;//修改成功
            }else{
                return 3;//修改失败
            }
        }
    }

    //上传头像
    public function actionEdit_img()
    {
        $file = $_FILES['file'];
        $up_path = Yii::getAlias('@backend').'\web\userIcon';
        $up_error = $file['error'];
        $up_size = $file['size'];
        $up_tmp_name = $file['tmp_name'];
        $up_name = $file['name'];
        $up_type = $file['type'];
        $uid = $_POST['uid'];
        if($up_error>0){
            switch($up_info['error']){
                case 1:
                    $err_info="上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值";
                    break;
                case 2:
                    $err_info="上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值";
                    break;
                case 3:
                    $err_info="文件只有部分被上传";
                    break;
                case 4:
                    $err_info="没有文件被上传";
                    break;
                case 6:
                    $err_info="找不到临时文件夹";
                    break;
                case 7:
                    $err_info="文件写入失败";
                    break;
                default:
                    $err_info="未知的上传错误";
                    break;
            }
            return $err_info;
        }
        if($up_size>100000){
                return 3;
        }
        $exten_name=pathinfo($up_name,PATHINFO_EXTENSION);
        $new_name = 'icon_'.$uid.'.'.$exten_name;
        $up_to_path = $up_path.'\\'.$new_name;
        if(is_uploaded_file($up_tmp_name)){
//            if(file_exists($up_to_path)){
//                unlink($up_to_path);
//            }
            if(move_uploaded_file($up_tmp_name,$up_to_path)){
                $res = account::updateAll(['icon'=>$new_name], ['id'=>$uid]);
                return 1;//上传成功
            }else{
                return 2;//上传失败
            }
        }
    }

    //上传头像
    public function actionUp_icon()
    {
        $up_tmp_name = $_GET['up_tmp_name'];
        $up_path = $_GET['up_path'];
        $exten_name = $_GET['exten_name'];
        $uid = $_GET['uid'];
        $new_name = 'icon_'.$uid.'.'.$exten_name;
        $up_to_path = $up_path.'\\'.$new_name;
        if(file_exists($up_to_path)){
            unlink($up_to_path);
        }
        if(move_uploaded_file($up_tmp_name,$up_to_path)){
            return 1;//上传成功
        }else{
            return 2;//上传失败
        }
    }

    //验证客服消息token
    public function actionVeritytoken()
    {
//        $signature = $_GET["signature"];
//        $timestamp = $_GET["timestamp"];
//        $nonce = $_GET["nonce"];
//        $token = 'zaoanart2945296672';  //TOKEN 写自己在微信平台填入的token
//        $tmpArr = array($token,$timestamp,$nonce);
//        sort($tmpArr,SORT_STRING);
//        $tmpStr = implode($tmpArr);
//        $tmpStr = sha1($tmpStr);
//        $echoStr = $_GET['echostr'];
//        if( $tmpStr == $signature ){
//            echo $echoStr;
////            return true;
//        }else{
//            return false;
//        }
        $postStr = file_get_contents('php://input');
        if (!empty($postStr) && is_string($postStr)) {
            $postArr = json_decode($postStr, true);
            $fromUsername = $postArr['FromUserName'];   //发送者openid
            if (!empty($postArr['MsgType']) && in_array($postArr['MsgType'], array("text", "image"))) {
                //若用户反馈的是图片消息
                if($postArr['MsgType'] == "image"){
                    //微信输出的是二进制图片流，不支持小程序外部使用后，所以需要保存到自己服务。
                    self::send_message($fromUsername, "text", "您好,很高兴为您服务!下单系统正在建设中,下单请添加官方QQ:2945296672,或者联系电话:18964563738。");
                }else{
                    //文字消息
                    $content = $postArr['Content'];
                    self::send_message($fromUsername, "text", "您好,很高兴为您服务!下单系统正在建设中,下单请添加官方QQ:2945296672,或者联系电话:18964563738。");
                }
                //记录客服消息到数据库,同事发邮箱通知运营同事,根据个人具体业务做相应处理;
                // ....

            } else {//用户进入到客服消息页面
                self::send_message($fromUsername, "text", "您好,很高兴为您服务!下单系统正在建设中,下单请添加官方QQ:2945296672,或者联系电话:18964563738。");
            }
        }
        echo "success";
        exit;
    }

    //回复微信客服消息
    public function send_message($fromUsername, $msgType, $content)
    {
        $data = array(
            "touser" => $fromUsername,
            "msgtype" => $msgType,
            "text" => array("content" => $content)
        );
        $json = self::json_encode($data);  //兼容php5.4以下json格式处理
        $appid = "wxa0a3e0beee9f0e98";
        $secret = "76bc2ec854b8de862ffc94a2676839ba";
        $access_token = self::get_access_token($appid, $secret);
        /*
        * POST发送https请求客服接口api
        */
        $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=" . $access_token;
        //以'json'格式发送post的https请求
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($json)) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($curl, CURLOPT_HTTPHEADER, $headers );
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

    public  function get_access_token($appId = '', $appSecret = ''){
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appId&secret=$appSecret";
        $result = file_get_contents($url);
        $result = json_decode($result,true);
        $accesstoken = $result['access_token'];
        return $accesstoken;
    }

    //json中文处理
    function json_encode($array)
    {
        if (version_compare(PHP_VERSION, '5.4.0', '<')) {
            $str = json_encode($array);
            $str = preg_replace_callback("#\\\u([0-9a-f]{4})#i", function ($matchs) {
                return iconv('UCS-2BE', 'UTF-8', pack('H4', $matchs[1]));
            }, $str);
            return $str;
        } else {
            return json_encode($array, JSON_UNESCAPED_UNICODE);
        }
    }

    //根据图片相似度搜索1.生成图片代码
    public function actionSearch_like()
    {
        $file = $_FILES['file'];
        $up_error = $file['error'];
        $up_size = $file['size'];
        $up_tmp_name = $file['tmp_name'];
        $up_name = $file['name'];
        $up_type = $file['type'];
        $phphash = new PhphashController(null,null);
        $max_id = Goods::find()->select('id')->orderBy(['id'=>SORT_DESC])->one();
        $num = 0;
        for($i=91;$i<1500;$i++){
            if(count($num)>=50){
                break;
            }
            $hash_str = Goods::find()->select('hash_str')->where(['id'=>$i])->one();
            $res = $phphash->Isimagefilesimilar($up_tmp_name,$hash_str['hash_str']);
//            var_dump($hash_str);die;
            if($res){
                $info[] = [
                   'id' => $i
                ] ;
                $num++;
            }
        }
        var_dump($info);die;
    }

    //更新数据库数据
    public function actionUpmysql()
    {
        for($i=54635;$i<108256;$i++){
            $res = Goods::find()->select('id,image')->where(['id'=>$i])->one();
            if($res){
                $imgsize = getimagesize('http://qiniu.zaoanart.com/'.$res['image']);
                $res = Goods::updateAll(['img_width' => $imgsize[0],'img_height' => $imgsize[1]], ['id'=>$res['id']]);
            }
        }
    }

}