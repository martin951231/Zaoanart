<?php
namespace api\modules\v1\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\QueryParamAuth;
use yii\data\ActiveDataProvider;
use Qiniu\Auth;
use backend\models\goods;
use backend\models\account;
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
                        ->select('id,image,name,img_width,img_height')
                        ->where(['is_appear' => 1])
                        ->createCommand()
                        ->getRawSql();
//                    $sql_str = $sql.' AND (`img_height` '.$contrast.' `img_width`)'.$search_condition.' AND ((`category` <> 999) or (`theme` <> 999)) ORDER BY `id` DESC LIMIT '.$pageSize.' OFFSET '.$start.'';
                    $sql_str = $sql.$contrast.$search_condition.' AND ((`category` <> 999) or (`theme` <> 999)) ORDER BY `id` DESC LIMIT '.$pageSize.' OFFSET '.$start.'';
                    $res = Yii::$app->db->createCommand($sql_str)->queryAll();
                }else{
                    //颜色不空,其他空
                    $sql = Goods::find()
                        ->select('id,image,name,img_height')
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
                        ->select('id,image,name,img_height')
                        ->where(['theme'=>$theme_id])
                        ->createCommand()
                        ->getRawSql();
                    $sql_str = $sql.$contrast.$search_condition.' AND (`is_appear`=1) AND ((`category` <> 999) or (`theme` <> 999)) ORDER BY `id` DESC LIMIT '.$pageSize.' OFFSET '.$start.'';
                    $res = Yii::$app->db->createCommand($sql_str)->queryAll();
                }else{
                    //颜色和theme不空,cate空
                    $sql = Goods::find()
                        ->select('id,image,name,img_height')
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
                                ->select('id,image,name,img_height')
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
                                    ->select('id,image,name,img_height')
                                    ->andWhere(['is_appear'=>1])
                                    ->createCommand()
                                    ->getRawSql();
                            }
                            $sql_str = $sql.$contrast.' AND ('.substr($condition,3).')'.$search_condition.' AND (`is_appear`=1) AND ((`category` <> 999) or (`theme` <> 999)) ORDER BY `id` DESC LIMIT '.$pageSize.' OFFSET '.$start.'';
                            $res = Yii::$app->db->createCommand($sql_str)->queryAll();
                        }
                    }else{
                        $sql = Goods::find()
                            ->select('id,image,name,img_height')
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
                                ->select('id,image,name,img_height')
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
                                    ->select('id,image,name,img_height')
                                    ->where(['color' => $color_id])
                                    ->createCommand()
                                    ->getRawSql();
                            }
                            $sql_str = $sql.$contrast.' AND ('.substr($condition,3).')'.$search_condition.' AND (`is_appear`=1) AND ((`category` <> 999) or (`theme` <> 999)) ORDER BY `id` DESC LIMIT '.$pageSize.' OFFSET '.$start.'';
                            $res = Yii::$app->db->createCommand($sql_str)->queryAll();
                        }
                    }else {
                        $sql = Goods::find()
                            ->select('id,image,name,img_height')
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
                                ->select('id,image,name,img_height')
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
                                    ->select('id,image,name,img_height')
                                    ->where(['theme' => $theme_id])
                                    ->createCommand()
                                    ->getRawSql();
                            }
                            $sql_str = $sql.$contrast.' AND ('.substr($condition,3).')'.$search_condition.' AND (`is_appear`=1) AND ((`category` <> 999) or (`theme` <> 999)) ORDER BY `id` DESC LIMIT '.$pageSize.' OFFSET '.$start.'';
                            $res = Yii::$app->db->createCommand($sql_str)->queryAll();
                        }
                    }else{
                        $sql = Goods::find()
                            ->select('id,image,name,img_height')
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
                                ->select('id,image,name,img_height')
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
                                    ->select('id,image,name,img_height')
                                    ->where(['theme' => $theme_id,'color' => $color_id])
                                    ->createCommand()
                                    ->getRawSql();
                            }
                            $sql_str = $sql.$contrast.' AND ('.substr($condition,3).')'.$search_condition.' AND (`is_appear`=1) AND ((`category` <> 999) or (`theme` <> 999)) ORDER BY `id` DESC LIMIT '.$pageSize.' OFFSET '.$start.'';
                            $res = Yii::$app->db->createCommand($sql_str)->queryAll();
                        }
                    }else {
                        $sql = Goods::find()
                            ->select('id,image,name,img_height')
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
                ->select('id,image,name,img_height')
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
                    ->select('id,image,name,img_height')
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
            ->select('id,image,name,img_height')
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
                ->select('id,image,name,img_height')
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
            ->select('id,image,name,theme,img_height')
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
        $labels = Goods::find()->select('label')->where(['id'=>$_GET['id']])->one();
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
        if(count($label)!=0){
            $label2 = $label;
            $label_str = '';
            for($i = 0; $i < count($label2); $i++){
                $label_str .= 'and (`label` LIKE "%,'.$label2[$i].',%") ';
            }
            $may_img_arr1 = Yii::$app->db->createCommand("select `id`,`image`,`img_height` from `tsy_goods` where `id` !=".$_GET['id']." ".$label_str)->queryAll();
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
        if(count($labels)!=0){
            $label2 = $labels;
            $label_str2 = '';
            for($q = 0; $q < count($label2); $q++){
                $label_str2 .= 'and (`label` LIKE "%,'.$label2[$q].',%") ';
            }
            $may_img_arr2 = Yii::$app->db->createCommand("select `id`,`image`,`img_height` from `tsy_goods` where `id` !=".$_GET['id']." ".$label_str2)->queryAll();
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
        $keep = keep::find()->select('id,keep_name')->where(['status'=>1])->all();
        if($keep){
            for($i = 0; $i<count($keep);$i++){
                $res2[$i] = keepimage::find()->select('id,imgid')->where(['kid'=>$keep[$i]['id']])->limit(4)->orderBy('created_at')->all();
                for ($k = 0; $k<count($res2[$i]);$k++){
                    $res3[$i][] = goods::find()->select('id,name,image')->where(['id'=>$res2[$i][$k]['imgid']])->one();
                    if($res2[$i][$k]['imgid']){
                        $res[$i][] = [
                            'keep_id' => $keep[$i]['id'],
                            'keep_name' => $keep[$i]['keep_name'],
                            'imgid' => $res2[$i][$k]['imgid'],
                            'image' => 'http://qiniu.zaoanart.com/'.$res3[$i][$k]['image'].'?imageView2/1/w/200/h/200'
                        ];
                    }
                }
            }
            sort($res);
            return $res;
        }
    }

    //获取收藏夹图片
    public function actionGetkeepimg()
    {
        $keep_id = $_GET['keep_id'];
        $pageSize = 20;
        $start = $_GET['start'];
        $good_id = Keepimage::find()
            ->select('imgid')
            ->where(['kid'=>$keep_id])
            ->limit($pageSize)
            ->offset($start)
            ->all();
        for($i=0;$i<count($good_id);$i++){
            $res = Goods::find()
                ->select('id,image,name,img_height')
                ->where(['id'=>$good_id[$i]['imgid']])
                ->andWhere(['is_appear'=>1])
                ->one();
            if($res){
                $info['image'][] = [
                    'id'=> $res['id'],
                    'image'=> 'http://qiniu.zaoanart.com/'.$res['image'],
                    'name'=> $res['name'],
                    'img_height'=> (int)$res['img_height'],
                    'checked'=>false
                ];
            }
        }
        $info['start'] = $start+$pageSize;
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
        $keep = keep::find()->select('id,keep_name')->where(['uid'=>$uid])->all();
        $res = [];
        if($keep){
            for($i = 0; $i<count($keep);$i++){
                $res2 = keepimage::find()->select('id,imgid')->where(['kid'=>$keep[$i]['id']])->limit(4)->all();
                if(!$res2){
                    $res[$i][] = [
                        'keep_id' => $keep[$i]['id'],
                        'keep_name' => $keep[$i]['keep_name'],
                        'imgid' => '',
                        'image' => ''
                    ];
                }
                for ($k = 0; $k<count($res2);$k++){
                    $res3[$i][] = goods::find()->select('id,name,image')->where(['id'=>$res2[$k]['imgid']])->one();
                    if($res2[$k]['imgid']){
                        $res[$i][] = [
                            'keep_id' => $keep[$i]['id'],
                            'keep_name' => $keep[$i]['keep_name'],
                            'imgid' => $res2[$k]['imgid'],
                            'image' => 'http://qiniu.zaoanart.com/'.$res3[$i][$k]['image'].'?imageView2/1/w/200/h/200'
                        ];
                    }
                }
            }
            sort($res);
            return $res;
        }
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
                'keep_name' => $keep_name
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