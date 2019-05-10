<?php
namespace api\modules\v1\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\QueryParamAuth;
use yii\data\ActiveDataProvider;
use backend\models\goods;
use backend\models\category;
use backend\models\theme;
use backend\models\account;
use backend\models\label;
use backend\models\Shopcar;
use yii\imagine\Image;

class GoodsController extends ActiveController
{
    public $modelClass = 'api\modules\v1\models\Goods';
    public $img_arr = [];
    public function actions()
    {
        $actions = parent::actions();

        // 注销系统自带的实现方法
        unset($actions['index'], $actions['update'], $actions['create'], $actions['delete'], $actions['view']);
        header('Access-Control-Allow-Origin:*');
        return $actions;
    }
    public function actionFindimage(){
        $res = Goods::find()->select('id,image,name')->limit(8)->orderBy(['created_at' => SORT_DESC,])->all();
        return $res;
    }
    public function actionFindgoodsa(){
        $category = $_GET['category_id'];
        $pageSize = $_GET['pageSize'];
        $currentPage = $_GET['currentPage'];
        $start = ($currentPage-1) * $pageSize;
        $info = Goods::find()->select('id,image,name')->where(['category'=>$category])->all();
        $res = array_slice($info,$start,$pageSize,false);
        $res2 = category::find()->select('category_name')->where(['id'=>$category])->one();
        if($res && $res2 ){
            for($i = 0; $i<count($res);$i++){
                    $res3[] = [
                        'id' => $res[$i]['id'],
                        'image' => $res[$i]['image'],
                        'name' => $res[$i]['name'],
                        'category_name' => $res2['category_name'],
                        'count' => count($info)
                    ];
            }
            return $res3;
        }
        return false;
    }
    public function actionFindgoodsb(){
        $theme = $_GET['theme_id'];
        $pageSize = $_GET['pageSize'];
        $currentPage = $_GET['currentPage'];
        $start = ($currentPage-1) * $pageSize;
        $info = Goods::find()->select('id,image,name')->where(['theme'=>$theme])->all();
        $res = array_slice($info,$start,$pageSize,false);
        $res2 = theme::find()->select('theme_name')->where(['id'=>$theme])->one();
        if($res){
            for($i = 0 ; $i<count($res);$i++){
                $res3[] = [
                    'id' => $res[$i]['id'],
                    'image' => $res[$i]['image'],
                    'name' => $res[$i]['name'],
                    'theme_name' => $res2['theme_name'],
                    'count' => count($info)
                ];
            }
            return $res3;
        }
        return false;
    }
    public function actionFindgoodsc(){
        $color = $_GET['color_id'];
        $pageSize = $_GET['pageSize'];
        $currentPage = $_GET['currentPage'];
        $start = ($currentPage-1) * $pageSize;
        $info = Goods::find()->select('id,image,name')->where(['color'=>$color])->all();
        $res = array_slice($info,$start,$pageSize,false);
        if($res){
            for($i = 0;$i<count($res);$i++){
                $res2[] = [
                    'id' => $res[$i]['id'],
                    'image' => $res[$i]['image'],
                    'name' => $res[$i]['name'],
                    'count' => count($info)
                ];
            }
            return $res2;
        }
    }
    //标签查询
    public function actionFindgoodsd(){
        $label = $_GET['label_id'];
        $id = $_GET['id'];
        $pageSize = $_GET['pageSize'];
        $currentPage = $_GET['currentPage'];
        $start = ($currentPage-1) * $pageSize;
        $count = '';
        switch($_GET['contrast']){
            case -1:$contrast = '<';break;
            case 0:$contrast = '=';break;
            case 1:$contrast = '>';break;
            case 2:$contrast = 'or';break;
        }
        if($id){
            $info = Goods::find()
                ->select('id,image,name')
                ->where(['<>','id',$id])
                ->andWhere(['like','label',$label])
                ->andWhere('`max_length`'.$contrast.'`max_width`')
                ->andWhere(['is_appear'=>1])
                ->orderBy(['id'=>SORT_DESC])
                ->limit($pageSize)
                ->offset($start)
                ->all();
            $count = Goods::find()
                ->select('id,image,name')
                ->where(['<>','id',$id])
                ->andWhere(['like','label',$label])
                ->andWhere('`max_length`'.$contrast.'`max_width`')
                ->andWhere(['is_appear'=>1])
                ->count();
            $res = array_slice($info,0,$pageSize,false);
            $res2 = label::find()->select('label_name')->where(['id'=>$label])->one();
            if($res) {
                for ($i = 0; $i < count($res); $i++) {
                    $arr[] = [
                        'id' => $res[$i]['id'],
                        'image' => $res[$i]['image'],
                        'name' => $res[$i]['name'],
                        'label_name' => $res2['label_name'],
                        'count' => $count
                    ];
                }
                return $arr;
            }else{
                return false;
            }
        }else{
            $info = Goods::find()
                ->select('id,image,name')
                ->Where(['like','label',$label])
                ->andWhere('`max_length`'.$contrast.'`max_width`')
                ->andWhere(['is_appear'=>1])
                ->limit($pageSize)
                ->offset($start)
                ->all();
            $count = Goods::find()
                ->select('id,image,name')
                ->Where(['like','label',$label])
                ->andWhere('`max_length`'.$contrast.'`max_width`')
                ->andWhere(['is_appear'=>1])
                ->count();
            $res = array_slice($info,0,$pageSize,false);
            $res2 = label::find()->select('label_name')->where(['id'=>$label])->one();
            if($res){
                for($i = 0;$i<count($res);$i++){
                    $arr[] = [
                        'id' => $res[$i]['id'],
                        'image' => $res[$i]['image'],
                        'name' => $res[$i]['name'],
                        'label_name' => $res2['label_name'],
                        'count' => $count
                    ];
                }
                return $arr;
            }else{
                return false;
            }
        }
    }
    public function actionFindgoodsall(){
        $id = $_GET['id'];
        $res = Goods::find()->select('id,image,name,max_length,max_width,content,color,label,category,theme,is_face')->where(['id'=>$id])->one();
        $res2 = category::find()->select('category_name')->where(['id'=>$res['category']])->one();
        $res3 = theme::find()->select('theme_name')->where(['id'=>$res['theme']])->one();
        $label = array_filter(explode(',',$res['label']));
        sort($label);
        $label_list = [];
        for($i = 0;$i<count($label);$i++){
            $data = label::find()->select('label_name,id')->where(['id'=>$label[$i]])->one();
            $label_name = $data['label_name'];
            if($label_name){
                $label_list[] = [
                    'id'=>$data['id'],
                    'label_name'=> $label_name
                ];
            }else{
                $label_list[] = [];
            }
        }
        if($res){
                $info =[
                    'info'=>[
                        'id' => $res['id'],
                        'image' => $res['image'],
                        'name' => $res['name'],
                        'max_length' => $res['max_length'],
                        'max_width' => $res['max_width'],
                        'content' => $res['content'],
                        'color' => $res['color'],
                        'label' => $res['label'],
                        'is_face' => $res['is_face'],
                        'category_name' => $res2['category_name'],
                        'theme_name' => $res3['theme_name'],
                    ],
                    'label_list'=>$label_list
                ];
            return $info;
        }
    }
    public function actionFindgoods_catagory(){
        $id = $_GET['id'];
        $res = Goods::find()->select('category,theme')->where(['id'=>$id])->one();
        $res1 = Goods::find()->select('id,image,name,category,theme')->limit(10)->where(['category'=>$res['category']])->orderBy(['id'=>SORT_DESC])->all();
        $res2 = Goods::find()->select('id,image,name,category,theme')->limit(10)->where(['theme'=>$res['theme']])->orderBy(['id'=>SORT_DESC])->all();
        for($i = 0;$i<count($res1);$i++){
            $res3[] = category::find()->select('category_name')->where(['id'=>$res1[$i]['category']])->one();
            $goods1[] = [
                'id'=>$res1[$i]['id'],
                'image'=>$res1[$i]['image'],
                'name'=>$res1[$i]['name'],
                'category_name'=>$res3[$i]['category_name'],
                'theme'=>$res1[$i]['theme'],
                'category'=>$res1[$i]['category'],
            ];
        }
        for($k = 0;$k<count($res2);$k++){
            $res4[] = theme::find()->select('theme_name')->where(['id'=>$res2[$k]['theme']])->one();
            $goods2[] = [
                'id'=>$res2[$k]['id'],
                'image'=>$res2[$k]['image'],
                'name'=>$res2[$k]['name'],
                'theme_name'=>$res4[$k]['theme_name'],
                'theme'=>$res2[$k]['theme'],
                'category'=>$res2[$k]['category'],
            ];
        }
        $goods_list = [
            'category' => $goods1,
            'theme' => $goods2,
        ];
        return $goods_list;
    }
    //搜索查询
    public function actionFindgoodsown(){
        $param = urldecode($_GET['term']);
        $pageSize = $_GET['pageSize'];
        $currentPage = $_GET['currentPage'];
        $start = ($currentPage-1) * $pageSize;
        $color = '无颜色';
        $contrast = '';
        switch($_GET['contrast']){
            case -1:$contrast = '<';break;
            case 0:$contrast = '=';break;
            case 1:$contrast = '>';break;
            case 2:$contrast = 'or';break;
        }
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
            case $param == '其他' : $color = 11;break;
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
                ->select('id,image,name')
                ->where(['like','label',','.$res3[$k]['id'].','])
                ->andWhere('`max_length`'.$contrast.'`max_width`')
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
            ->select('id,image,name,theme')
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
            ->andWhere('`max_length`'.$contrast.'`max_width`')
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
                $res4[] = [
                    'id' => $info[$i]['id'],
                    'image' => $info[$i]['image'],
                    'name' => $info[$i]['name'],
                    'count' => $count
                ];
            }
            return $res4;
        }
    }
    //二级页类别查询
    public function actionCategory_find()
    {
        $cate_id = $_GET['cate_id'];
        $theme_id = $_GET['theme_id'];
        $color_id = $_GET['color_id'];
        $pageSize = $_GET['pageSize'];
        $currentPage = $_GET['currentPage'];
        $start = ($currentPage-1) * $pageSize;
        $contrast = '';
        switch($_GET['contrast']){
            case -1:$contrast = '<';break;
            case 0:$contrast = '=';break;
            case 1:$contrast = '>';break;
            case 2:$contrast = 'or';break;
        }
        $res = [];
        $count = 0;
        if($cate_id == 0){
            if($theme_id == 0){
                if($color_id == 0){
                    //全空
                    $res = Goods::find()
                        ->select('id,image,name')
                        ->where(['or',['<>','category',999],['<>','theme',999]])
                        ->andWhere('`max_length`'.$contrast.'`max_width`')
                        ->andWhere(['is_appear'=>1])
                        ->orderBy(['id'=>SORT_DESC])
                        ->limit($pageSize)
                        ->offset($start)
                        ->all();
                    $count = Goods::find()
                        ->where(['or',['<>','category',999],['<>','theme',999]])
                        ->andWhere('`max_length`'.$contrast.'`max_width`')
                        ->andWhere(['is_appear'=>1])
                        ->count();
                }else{
                    //颜色不空,其他空
                    $res = Goods::find()
                        ->select('id,image,name')
                        ->where(['color'=>$color_id])
                        ->andWhere(['or',['<>','category',999],['<>','theme',999]])
                        ->andWhere('`max_length`'.$contrast.'`max_width`')
                        ->andWhere(['is_appear'=>1])
                        ->orderBy(['id'=>SORT_DESC])
                        ->limit($pageSize)
                        ->offset($start)
                        ->all();
                    $count = Goods::find()
                        ->where(['color'=>$color_id])
                        ->andWhere(['or',['<>','category',999],['<>','theme',999]])
                        ->andWhere('`max_length`'.$contrast.'`max_width`')
                        ->andWhere(['is_appear'=>1])
                        ->count();
                }
            }else{
                if($color_id == 0){
                    //颜色和cate空,theme不空
                    $res = Goods::find()
                        ->select('id,image,name')
                        ->where(['theme'=>$theme_id])
                        ->andWhere(['or',['<>','category',999],['<>','theme',999]])
                        ->andWhere('`max_length`'.$contrast.'`max_width`')
                        ->andWhere(['is_appear'=>1])
                        ->orderBy(['id'=>SORT_DESC])
                        ->limit($pageSize)
                        ->offset($start)
                        ->all();
                    $count = Goods::find()
                        ->where(['theme'=>$theme_id])
                        ->andWhere(['or',['<>','category',999],['<>','theme',999]])
                        ->andWhere('`max_length`'.$contrast.'`max_width`')
                        ->andWhere(['is_appear'=>1])
                        ->count();
                }else{
                    //颜色和theme不空,cate空
                    $res = Goods::find()
                        ->select('id,image,name')
                        ->where(['theme'=>$theme_id,'color'=>$color_id])
                        ->andWhere(['or',['<>','category',999],['<>','theme',999]])
                        ->andWhere('`max_length`'.$contrast.'`max_width`')
                        ->andWhere(['is_appear'=>1])
                        ->orderBy(['id'=>SORT_DESC])
                        ->limit($pageSize)
                        ->offset($start)
                        ->all();
                    $count = Goods::find()
                        ->where(['theme'=>$theme_id,'color'=>$color_id])
                        ->andWhere(['or',['<>','category',999],['<>','theme',999]])
                        ->andWhere('`max_length`'.$contrast.'`max_width`')
                        ->andWhere(['is_appear'=>1])
                        ->count();
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
                            $res = Goods::find()
                                ->select('id,image,name')
                                ->where(['category'=>$cate_id])
                                ->andWhere(['or',['<>','category',999],['<>','theme',999]])
                                ->andWhere('`max_length`'.$contrast.'`max_width`')
                                ->andWhere(['is_appear'=>1])
                                ->orderBy(['id'=>SORT_DESC])
                                ->limit($pageSize)
                                ->offset($start)
                                ->all();
                            $count = Goods::find()
                                ->where(['category'=>$cate_id])
                                ->andWhere(['or',['<>','category',999],['<>','theme',999]])
                                ->andWhere('`max_length`'.$contrast.'`max_width`')
                                ->andWhere(['is_appear'=>1])
                                ->count();
                        }else{
                            $cate_id2[] = [
                                'id'=>(int)$cate_id
                            ];
                            $result = array_merge($cate_id2,$id_arr);
                            $condition = '';
                            for($i = 0;$i<count($result);$i++){
                                $condition .= ' or (`category` = '.$result[$i]['id'].')';
                                $sql = Goods::find()
                                    ->select('id,image,name')
                                    ->where(['or',['<>','category',999],['<>','theme',999]])
                                    ->andWhere('`max_length`'.$contrast.'`max_width`')
                                    ->andWhere(['is_appear'=>1])
                                    ->createCommand()
                                    ->getRawSql();
                                $count_ = Goods::find()
                                    ->where(['category'=>$result[$i]['id']])
                                    ->andWhere(['or',['<>','category',999],['<>','theme',999]])
                                    ->andWhere('`max_length`'.$contrast.'`max_width`')
                                    ->andWhere(['is_appear'=>1])
                                    ->count();
                                $count += $count_;
                            }
                            $sql_str = $sql.' AND ('.substr($condition,3).') AND (`is_appear`=1) ORDER BY `id` DESC LIMIT '.$pageSize.' OFFSET '.$start.'';
                            $res = Yii::$app->db->createCommand($sql_str)->queryAll();
                        }
                    }else{
                        $res = Goods::find()
                            ->select('id,image,name')
                            ->where(['category'=>$cate_id])
                            ->andWhere(['or',['<>','category',999],['<>','theme',999]])
                            ->andWhere('`max_length`'.$contrast.'`max_width`')
                            ->andWhere(['is_appear'=>1])
                            ->andWhere('`max_length`'.$contrast.'`max_width`')
                            ->orderBy(['id'=>SORT_DESC])
                            ->limit($pageSize)
                            ->offset($start)
                            ->all();
                        $count = Goods::find()
                            ->where(['category'=>$cate_id])
                            ->andWhere(['or',['<>','category',999],['<>','theme',999]])
                            ->andWhere('`max_length`'.$contrast.'`max_width`')
                            ->andWhere('`max_length`'.$contrast.'`max_width`')
                            ->andWhere(['is_appear'=>1])
                            ->count();
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
                            $res = Goods::find()
                                ->select('id,image,name')
                                ->where(['category'=>$cate_id,'color' => $color_id])
                                ->andWhere(['or',['<>','category',999],['<>','theme',999]])
                                ->andWhere('`max_length`'.$contrast.'`max_width`')
                                ->andWhere(['is_appear'=>1])
                                ->orderBy(['id'=>SORT_DESC])
                                ->limit($pageSize)
                                ->offset($start)
                                ->all();
                            $count = Goods::find()
                                ->where(['category'=>$cate_id,'color' => $color_id])
                                ->andWhere(['or',['<>','category',999],['<>','theme',999]])
                                ->andWhere('`max_length`'.$contrast.'`max_width`')
                                ->andWhere(['is_appear'=>1])
                                ->count();
                        }else{
                            $cate_id2[] = [
                                'id'=>(int)$cate_id
                            ];
                            $result = array_merge($cate_id2,$id_arr);
                            $condition = '';
                            for($i = 0;$i<count($result);$i++){
                                $condition .= ' or (`category` = '.$result[$i]['id'].')';
                                $sql = Goods::find()
                                    ->select('id,image,name')
                                    ->where(['or',['<>','category',999],['<>','theme',999]])
                                    ->andWhere('`max_length`'.$contrast.'`max_width`')
                                    ->andWhere(['is_appear'=>1])
                                    ->andWhere(['color' => $color_id])
                                    ->createCommand()
                                    ->getRawSql();
                                $count_ = Goods::find()
                                    ->where(['category'=>$result[$i]['id'],'color' => $color_id])
                                    ->andWhere(['or',['<>','category',999],['<>','theme',999]])
                                    ->andWhere('`max_length`'.$contrast.'`max_width`')
                                    ->andWhere(['is_appear'=>1])
                                    ->count();
                                $count += $count_;
                            }
                            $sql_str = $sql.' AND ('.substr($condition,3).') AND (`is_appear`=1) ORDER BY `id` DESC LIMIT '.$pageSize.' OFFSET '.$start.'';
                            $res = Yii::$app->db->createCommand($sql_str)->queryAll();
                        }
                    }else {
                        $res = Goods::find()
                            ->select('id,image,name')
                            ->where(['category' => $cate_id, 'color' => $color_id])
                            ->andWhere(['or',['<>','category',999],['<>','theme',999]])
                            ->andWhere('`max_length`'.$contrast.'`max_width`')
                            ->andWhere(['is_appear'=>1])
                            ->orderBy(['id'=>SORT_DESC])
                            ->limit($pageSize)
                            ->offset($start)
                            ->all();
                        $count = Goods::find()
                            ->where(['category' => $cate_id, 'color' => $color_id])
                            ->andWhere(['or',['<>','category',999],['<>','theme',999]])
                            ->andWhere('`max_length`'.$contrast.'`max_width`')
                            ->andWhere(['is_appear'=>1])
                            ->count();
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
                            $res = Goods::find()
                                ->select('id,image,name')
                                ->where(['category'=>$cate_id,'theme' => $theme_id])
                                ->andWhere(['or',['<>','category',999],['<>','theme',999]])
                                ->andWhere('`max_length`'.$contrast.'`max_width`')
                                ->andWhere(['is_appear'=>1])
                                ->orderBy(['id'=>SORT_DESC])
                                ->limit($pageSize)
                                ->offset($start)
                                ->all();
                            $count = Goods::find()
                                ->where(['category'=>$cate_id,'theme' => $theme_id])
                                ->andWhere(['or',['<>','category',999],['<>','theme',999]])
                                ->andWhere('`max_length`'.$contrast.'`max_width`')
                                ->andWhere(['is_appear'=>1])
                                ->count();
                        }else{
                            $cate_id2[] = [
                                'id'=>(int)$cate_id
                            ];
                            $result = array_merge($cate_id2,$id_arr);
                            $condition = '';
                            for($i = 0;$i<count($result);$i++){
                                $condition .= ' or (`category` = '.$result[$i]['id'].')';
                                $sql = Goods::find()
                                    ->select('id,image,name')
                                    ->where(['or',['<>','category',999],['<>','theme',999]])
                                    ->andWhere('`max_length`'.$contrast.'`max_width`')
                                    ->andWhere(['is_appear'=>1])
                                    ->andWhere(['theme' => $theme_id])
                                    ->createCommand()
                                    ->getRawSql();
                                $count_ = Goods::find()
                                    ->where(['category'=>$result[$i]['id'],'theme' => $theme_id])
                                    ->andWhere(['or',['<>','category',999],['<>','theme',999]])
                                    ->andWhere('`max_length`'.$contrast.'`max_width`')
                                    ->andWhere(['is_appear'=>1])
                                    ->count();
                                $count += $count_;
                            }
                            $sql_str = $sql.' AND ('.substr($condition,3).') AND (`is_appear`=1) ORDER BY `id` DESC LIMIT '.$pageSize.' OFFSET '.$start.'';
                            $res = Yii::$app->db->createCommand($sql_str)->queryAll();
                        }
                    }else {
                        $res = Goods::find()
                            ->select('id,image,name')
                            ->where(['category' => $cate_id, 'theme' => $theme_id])
                            ->andWhere(['or',['<>','category',999],['<>','theme',999]])
                            ->andWhere('`max_length`'.$contrast.'`max_width`')
                            ->andWhere(['is_appear'=>1])
                            ->orderBy(['id'=>SORT_DESC])
                            ->limit($pageSize)
                            ->offset($start)
                            ->all();
                        $count = Goods::find()
                            ->where(['category' => $cate_id, 'theme' => $theme_id])
                            ->andWhere(['or',['<>','category',999],['<>','theme',999]])
                            ->andWhere('`max_length`'.$contrast.'`max_width`')
                            ->andWhere(['is_appear'=>1])
                            ->count();
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
                            $res = Goods::find()
                                ->select('id,image,name')
                                ->where(['category'=>$cate_id,'theme' => $theme_id,'color' => $color_id])
                                ->andWhere(['or',['<>','category',999],['<>','theme',999]])
                                ->andWhere('`max_length`'.$contrast.'`max_width`')
                                ->andWhere(['is_appear'=>1])
                                ->orderBy(['id'=>SORT_DESC])
                                ->limit($pageSize)
                                ->offset($start)
                                ->all();
                            $count = Goods::find()
                                ->where(['category'=>$cate_id,'theme' => $theme_id,'color' => $color_id])
                                ->andWhere(['or',['<>','category',999],['<>','theme',999]])
                                ->andWhere('`max_length`'.$contrast.'`max_width`')
                                ->andWhere(['is_appear'=>1])
                                ->count();
                        }else{
                            $cate_id2[] = [
                                'id'=>(int)$cate_id
                            ];
                            $result = array_merge($cate_id2,$id_arr);
                            $condition = '';
                            for($i = 0;$i<count($result);$i++){
                                $condition .= ' or (`category` = '.$result[$i]['id'].')';
                                $sql = Goods::find()
                                    ->select('id,image,name')
                                    ->where(['or',['<>','category',999],['<>','theme',999]])
                                    ->andWhere('`max_length`'.$contrast.'`max_width`')
                                    ->andWhere(['is_appear'=>1])
                                    ->andWhere(['theme' => $theme_id])
                                    ->andWhere(['color' => $color_id])
                                    ->createCommand()
                                    ->getRawSql();
                                $count_ = Goods::find()
                                    ->where(['category'=>$result[$i]['id'],'theme' => $theme_id,'color' => $color_id])
                                    ->andWhere(['or',['<>','category',999],['<>','theme',999]])
                                    ->andWhere('`max_length`'.$contrast.'`max_width`')
                                    ->andWhere(['is_appear'=>1])
                                    ->count();
                                $count += $count_;
                            }
                            $sql_str = $sql.' AND ('.substr($condition,3).') AND (`is_appear`=1) ORDER BY `id` DESC LIMIT '.$pageSize.' OFFSET '.$start.'';
                            $res = Yii::$app->db->createCommand($sql_str)->queryAll();
                        }
                    }else {
                        $res = Goods::find()
                            ->select('id,image,name')
                            ->where(['category' => $cate_id, 'theme' => $theme_id, 'color' => $color_id])
                            ->andWhere(['or',['<>','category',999],['<>','theme',999]])
                            ->andWhere('`max_length`'.$contrast.'`max_width`')
                            ->andWhere(['is_appear'=>1])
                            ->orderBy(['id'=>SORT_DESC])
                            ->limit($pageSize)
                            ->offset($start)
                            ->all();
                        $count = Goods::find()
                            ->where(['category' => $cate_id, 'theme' => $theme_id, 'color' => $color_id])
                            ->andWhere(['or',['<>','category',999],['<>','theme',999]])
                            ->andWhere('`max_length`'.$contrast.'`max_width`')
                            ->andWhere(['is_appear'=>1])
                            ->count();
                    }
                }
            }
        }
        $info = array_slice($res,0,$pageSize,false);
        if($info){
            for($i = 0;$i<count($info);$i++){
                $res1[] = [
                    'id' => $info[$i]['id'],
                    'image' => $info[$i]['image'],
                    'name' => $info[$i]['name'],
                    'count' => $count
                ];
            }
            return $res1;
        }else{
            return false;
        }
    }
    //装裱画框
    public function actionDecoration()
    {
        $info = $_GET;
        $img_name = $_GET['img_name'];
        $img_name = explode('/',$img_name);
        if(count($img_name)>1){
            $res = $this->Decoration2($info);
            return $res;
        }else{
            $res = $this->Decoration1($info);
            return $res;
        }
    }
    //装裱画框(A类)
    function Decoration1($info)
    {
        $box_img_width1 = $info['box_img_width1'];
        $box_img_height1 = $info['box_img_height1'];
        $img_width1 = $info['img_width1'];
        $img_height1 = $info['img_height1'];
        $face_width1 = $info['face_width']*29;
        $small_face = $info['small_face'];
        $c_width = $info['need_width'];
        $c_height = $info['need_height'];
        $img_name = $info['img_name'];
        //源图片名
        //$img_name = 'sucai2.jpg';
        //目标图片名
//        $toimg_name = '149561562350036926.jpg';
        //图片保存路径
        $path = Yii::getAlias('@backend').'\web\test\\';
        //图片获取路径
        $get_path = 'http://qiniu.zaoanart.com/';
        //设置头部
//        header("Content-type:image/png;");
        //装框图像路径
//            $decora_img = Yii::getAlias('@backend').'\web\goods\\'.$toimg_name;
        //获取装框图像大小
        $image_size = getimagesize($get_path.$img_name);
        $bili = $img_width1/$img_height1;
        //图片宽(画芯宽)
        $img_width = $img_width1;
        //图片高(画芯高)
        $img_height = $img_height1;
        //面宽(前端给)
        $face_width = $face_width1;
        //图片实际宽度
        $face_widths = $image_size[0];
        //框宽(外框宽)
        $box_width = $box_img_width1;
        //框高(外框高)
        $box_height = $box_img_height1;
        //左图
        $left_img = imagecreatetruecolor($face_width,$box_height);
        //源图像
        $root_img = imagecreatefromjpeg($get_path.$img_name);
        //copy所需画框区域(拉伸)
        imagecopyresampled($left_img,$root_img,0,0,0,0,$face_width,$box_height,$face_widths,3600);
        //设置三角形顶点位置
        $points = [
            0,0,
            $face_width,0,
            $face_width,$face_width
        ];
        //设置颜色
        $blue = imagecolorallocate($left_img, 0, 255, 0);
        //画三角形
        imagefilledpolygon($left_img,$points,3,$blue);
        //设置三角形顶点位置
        $points2 = [
            0,$box_height,
            $face_width,$box_height,
            $face_width,$box_height-$face_width,
        ];
        //设置颜色
        $green = imagecolorallocate($left_img, 0, 255, 0);
        //画三角形
        imagefilledpolygon($left_img,$points2,3,$green);
        //设置透明色
        imagecolortransparent($left_img,65280);
        //下图
        $bottom_img = imagecreatetruecolor($box_width,$face_width);
        //逆时针旋转90度生成下边图
        $root_img = imagerotate($root_img,90,0);
        //copy所需画框区域(拉伸)
        imagecopyresampled($bottom_img,$root_img,0,0,0,0,$box_width,$face_width,3600,$face_widths);
        //生成一个画板
//        $new_img = imagecreatetruecolor($box_width,$box_height);
        $new_img = imagecreatetruecolor($c_width,$c_height);
        //设置背景颜色(0.255.255, 65535)
        $color = imagecolorallocate($new_img, 0, 255, 0);
        //填充颜色
        imagefill($new_img,0,0,$color);
        imagecolortransparent($new_img,65280);
        //复制下框
        imagecopyresized($new_img,$bottom_img,0,$c_height-$small_face+1,0,0,$c_width,$small_face,$box_width,$face_width);
        //垂直对称下图生成上图
        imageflip($bottom_img,IMG_FLIP_VERTICAL);
        //复制上框
        imagecopyresized($new_img,$bottom_img,0,0,0,0,$c_width,$small_face,$box_width,$face_width);
        //复制左框
        imagecopyresized($new_img,$left_img,0,0,0,0,$small_face,$c_height,$face_width,$box_height);
        //垂直对称左图生成右图
        imageflip($left_img,IMG_FLIP_HORIZONTAL);
        //复制右框
        imagecopyresized($new_img,$left_img,$c_width-$small_face+1,0,0,0,$small_face,$c_height,$face_width,$box_height);
        imagecolortransparent($new_img,65280);
//        imagepng($new_img);die;
        imagepng($new_img,$path.'bgimg.png',9);
        $img_info = filesize($path.'bgimg.png');
        $fp = fopen($path.'bgimg.png', "r");
        $content = fread($fp,$img_info);
        $img_str = chunk_split(base64_encode($content));
        $img_base64 = 'data:image/png;base64,'.$img_str;
        return $img_base64;

        //生成左图
//        $left_img = $this->left_img($path,$get_path,$img_name,$img_width,$img_height,$face_width,$box_width,$box_height,$t1,$small_face,$c_height,$face_widths);
//        //生成下图
//        $bottom_img = $this->bottom_img($path,$get_path,$img_name,$img_width,$img_height,$face_width,$box_width,$box_height,$small_face,$c_width,$face_widths);
//        //复制左框
//        imagecopymerge($new_img,$left_img,0,0,0,0,$face_width,$box_height,100);
//        //垂直对称下图生成上图
//        imageflip($bottom_img,IMG_FLIP_VERTICAL);
//        //复制上框
//        imagecopymerge($new_img,$bottom_img,0,0,0,0,$box_width,$face_width,100);
//        //垂直对称左图生成右图
//        imageflip($left_img,IMG_FLIP_HORIZONTAL);
//        //复制右框
//        imagecopymerge($new_img,$left_img,$box_width-$face_width,0,0,0,$face_width,$box_height,100);
//        //复制下框
//        imagecopymerge($new_img,$bottom_img,0,$box_height-$face_width,0,0,$box_width,$face_width,100);
//        //复制左框
//        imagecopymerge($new_img,$left_img,0,0,0,0,$face_width,$box_height,100);
//        //垂直对称下图生成上图
//        imageflip($bottom_img,IMG_FLIP_VERTICAL);
//        //复制上框
//        imagecopymerge($new_img,$bottom_img,0,0,0,0,$box_width,$face_width,100);
//        //垂直对称左图生成右图
//        imageflip($left_img,IMG_FLIP_HORIZONTAL);
//        //复制右框
//        imagecopymerge($new_img,$left_img,$box_width-$face_width,0,0,0,$face_width,$box_height,100);
        //缩放图片
//        $newimg = imagescale($new_img,$c_width,$c_height,IMG_NEAREST_NEIGHBOUR);
        //填充透明色
//        imagecolortransparent($new_img,65535);
//        imagecolortransparent($new_img,65280);
//        imagepng($new_img);die;
//        imagepng($new_img,$path.'bgimg.png',9);
//        $t1 = microtime(true);
//        $img_info = filesize($path.'bgimg.png');
//        $fp = fopen($path.'bgimg.png', "r");
//        $content = fread($fp,$img_info);
//        $img_str = chunk_split(base64_encode($content));
//        $img_base64 = 'data:image/png;base64,'.$img_str;
//        $t2 = microtime(true);
//        return $img_base64;
    }
    //装裱画框(B类)
    function Decoration2()
    {
        $box_img_width1 = $_GET['box_img_width1'];
        $box_img_height1 = $_GET['box_img_height1'];
        $img_width1 = $_GET['img_width1'];
        $img_height1 = $_GET['img_height1'];
        $face_width1 = $_GET['face_width']*29;
        $small_face = $_GET['small_face'];
        $c_width = $_GET['need_width'];
        $c_height = $_GET['need_height'];
        $img_name = $_GET['img_name'];
        $img_name = explode('/',$img_name);
        $img_nameA = $img_name[0];
        $img_nameB = $img_name[1];
        //图片保存路径
        $path = Yii::getAlias('@backend').'\web\test\\';
        //图片获取路径
        $get_path = 'http://qiniu.zaoanart.com/';
        //设置头部
        header("Content-type:image/png;");
        //获取装框图像大小
        $image_sizeA = getimagesize($get_path.$img_nameA);
        $image_sizeB = getimagesize($get_path.$img_nameB);
        $bili = $img_width1/$img_height1;
        //图片宽(画芯宽)
        $img_width = $img_width1;
        //图片高(画芯高)
        $img_height = $img_height1;
        //面宽(前端给)
        $face_width = $face_width1;
        //图片实际宽度
        $face_widths = $image_sizeA[0];
        //框宽(外框宽)
        $box_width = $box_img_width1;
        //框高(外框高)
        $box_height = $box_img_height1;
        //左图
        $left_img = imagecreatetruecolor($face_width,$box_height);
        //源图像
        $root_imgA = imagecreatefromjpeg($get_path.$img_nameA);
        //copy所需画框区域(拉伸)
        imagecopyresampled($left_img,$root_imgA,0,0,0,0,$face_width,$box_height,$face_widths,3600);
        //设置三角形顶点位置
        $points = [
            0,0,
            $face_width,0,
            $face_width,$face_width
        ];
        //设置颜色
        $blue = imagecolorallocate($left_img, 0, 255, 0);
        //画三角形
        imagefilledpolygon($left_img,$points,3,$blue);
        //设置三角形顶点位置
        $points2 = [
            0,$box_height,
            $face_width,$box_height,
            $face_width,$box_height-$face_width,
        ];
        //设置颜色
        $green = imagecolorallocate($left_img, 0, 255, 0);
        //画三角形
        imagefilledpolygon($left_img,$points2,3,$green);
        //设置透明色
        imagecolortransparent($left_img,65280);
        //右图
        $right_img = imagecreatetruecolor($face_width,$box_height);
        //源图像
        $root_imgB = imagecreatefromjpeg($get_path.$img_nameB);
        //copy所需画框区域(拉伸)
        imagecopyresampled($right_img,$root_imgB,0,0,0,0,$face_width,$box_height,$face_widths,3600);
        //设置三角形顶点位置
        $points = [
            0,$face_width,
            $face_width,0,
            0,0
        ];
        //设置颜色
        $blue = imagecolorallocate($right_img, 0, 255, 0);
        //画三角形
        imagefilledpolygon($right_img,$points,3,$blue);
        //设置三角形顶点位置
        $points2 = [
            0,$box_height,
            0,$box_height-$face_width,
            $face_width,$box_height,
        ];
        //设置颜色
        $green = imagecolorallocate($right_img, 0, 255, 0);
        //画三角形
        imagefilledpolygon($right_img,$points2,3,$green);
        //设置透明色
        imagecolortransparent($right_img,65280);
        //垂直对称生成右边图
        //imageflip($right_img,IMG_FLIP_VERTICAL);
        //下图
        $bottom_img = imagecreatetruecolor($box_width,$face_width);
        //顺时针旋转90度生成下边图
        $bottom_img = imagerotate($root_imgB,-90,0);
        //水平对称生成下边图
        imageflip($bottom_img,IMG_FLIP_HORIZONTAL);
        //上图
        $top_img = imagecreatetruecolor($box_width,$face_width);
        //顺时针旋转90度生成上边图
        $top_img = imagerotate($root_imgA,-90,0);
        //水平对称生成上边图
        imageflip($top_img,IMG_FLIP_HORIZONTAL);
        //生成一个画板
//        $new_img = imagecreatetruecolor($box_width,$box_height);
        $new_img = imagecreatetruecolor($c_width,$c_height);
        //设置背景颜色(0.255.255, 65535)
        $color = imagecolorallocate($new_img, 0, 255, 0);
        //填充颜色
        imagefill($new_img,0,0,$color);
        imagecolortransparent($new_img,65280);
        //复制下框
        imagecopyresized($new_img,$bottom_img,0,$c_height-$small_face+1,0,0,$c_width,$small_face,$box_width,$face_widths);
        //复制上框
        imagecopyresized($new_img,$top_img,0,0,0,0,$c_width,$small_face,$box_width,$face_widths);
        //复制左框
        imagecopyresized($new_img,$left_img,0,0,0,0,$small_face,$c_height,$face_width,$box_height);
        //复制右框
        imagecopyresized($new_img,$right_img,$c_width-$small_face+1,0,0,0,$small_face,$c_height,$face_width,$box_height);
        imagecolortransparent($new_img,65280);
        imagepng($new_img,$path.'bgimg.png',9);
        $img_info = filesize($path.'bgimg.png');
        $fp = fopen($path.'bgimg.png', "r");
        $content = fread($fp,$img_info);
        $img_str = chunk_split(base64_encode($content));
        $img_base64 = 'data:image/png;base64,'.$img_str;
        return $img_base64;
    }
    //生成左图
    function left_img($path,$get_path,$img_name,$img_width,$img_height,$face_width,$box_width,$box_height,$t1,$small_face,$c_height,$face_widths)
    {
        //目标图像
        $dst_im = imagecreatetruecolor($face_width,$box_height);
        //源图像
        $root_img = imagecreatefromjpeg($get_path.$img_name);
        //copy所需画框区域(拉伸)
        imagecopyresampled($dst_im,$root_img,0,0,0,0,$face_width,$box_height,$face_widths,3600);
        //设置三角形顶点位置
            $points = [
                0,0,
                $face_width,0,
                $face_width,$face_width
            ];
            //设置颜色
            $blue = imagecolorallocate($dst_im, 0, 255, 0);
            //画三角形
            $root_img = imagefilledpolygon($dst_im,$points,3,$blue);
            //设置三角形顶点位置
            $points2 = [
                0,$box_height,
                $face_width,$box_height,
                $face_width,$box_height-$face_width,
            ];
            //设置颜色
            $green = imagecolorallocate($dst_im, 0, 255, 0);
            //画三角形
            $is = imagefilledpolygon($dst_im,$points2,3,$green);
            //设置透明色
            imagecolortransparent($dst_im,65280);

            return $dst_im;
    }
    //生成下图
    function bottom_img($path,$get_path,$img_name,$img_width,$img_height,$face_width,$box_width,$box_height,$small_face,$c_width,$face_widths)
    {
        //目标图像
        $dst_im = imagecreatetruecolor($box_width,$face_width);
        //图片路径
        $url = $get_path.$img_name;
        //打开图片
        $root_img = imagecreatefromjpeg($url);
        //逆时针旋转90度生成下边图
        $root_img = imagerotate($root_img,90,0);
        //copy所需画框区域(拉伸)
        imagecopyresampled($dst_im,$root_img,0,0,0,0,$box_width,$face_width,3600,$face_widths);
//        //设置三角形顶点位置
        $points = [
            0,0,
            $face_width-1,0,
            0,$face_width-1
        ];
        //设置颜色
        $blue = imagecolorallocate($dst_im, 0, 255, 0);
        //画三角形
        $root_img = imagefilledpolygon($dst_im,$points,3,$blue);
        //设置三角形顶点位置
        $points2 = [
            $box_width,0,
            $box_width+1,$face_width+1,
            $box_width-$face_width+1,-1000,
        ];
        //设置颜色
        $green = imagecolorallocate($dst_im, 0, 255, 0);
        //画三角形
        $is = imagefilledpolygon($dst_im,$points2,3,$green);
        //设置透明色
        imagecolortransparent($dst_im,65280);
        return $dst_im;
    }
    //装裱单立体
    public function actionSinglestereo()
    {
        $filename1 = Yii::getAlias('@backend').'\web\test\top1.jpg';
        $filename2 = Yii::getAlias('@backend').'\web\test\left1.jpg';
        $filename3 = Yii::getAlias('@backend').'\web\test\bottom1.jpg';
        $filename4 = Yii::getAlias('@backend').'\web\test\right1.jpg';
        if(file_exists($filename1)){
            unlink($filename1);
        }
        if(file_exists($filename2)){
            unlink($filename2);
        }
        if(file_exists($filename3)){
            unlink($filename3);
        }
        if(file_exists($filename4)){
            unlink($filename4);
        }
        $box_img_width1 = $_GET['box_img_width1'];
        $box_img_height1 = $_GET['box_img_height1'];
        $img_width1 = $_GET['img_width1'];
        $img_height1 = $_GET['img_height1'];
        $face_width1 = $_GET['face_width'];
        //$img_name = $_GET['img_name'];
//        $face_width1 = 15;
        //源图片名
        $img_name = '20181030160655(2).jpg';
        //目标图片名
//        $toimg_name = '149561562350036926.jpg';
        //路径
        $path = Yii::getAlias('@backend').'\web\test\\';
        //这里用的GD库
        //设置头部
        header("Content-type:image/jpeg;charset=UTF-8");
        //装框图像路径
//            $decora_img = Yii::getAlias('@backend').'\web\goods\\'.$toimg_name;
        //获取装框图像大小
//            $image_size = getimagesize($decora_img);
        $bili = $img_width1/$img_height1;
        //图片宽(画芯宽)
        $img_width = $img_width1;
        //图片高(画芯高)
        $img_height = $img_height1;
        //面宽(前端给)
        $face_width = $face_width1;
        //框宽(外框宽)
        $box_width = $box_img_width1;
        //框高(外框高)
        $box_height = $box_img_height1;
        //生成左图
        $this->left_img1($path,$img_name,$img_width,$img_height,$face_width,$box_width,$box_height);
//            imagepng($left_img);
        //生成下图
        $this->bottom_img1($path,$img_name,$img_width,$img_height,$face_width,$box_width,$box_height);
//            imagepng($bottom_img);
        //打开左图水平对称生成右图
        $img_left = new \Imagick($path.'left1.jpg');
        $img_left->flopImage();
        //保存图片
        file_put_contents($path.'right1.jpg',$img_left);
        //打开下图垂直对称生成上图
        $img_bottom = new \Imagick($path.'bottom1.jpg');
        $img_bottom->flipImage();
        //保存图片
        file_put_contents($path.'top1.jpg',$img_bottom);
        //生成一个画板
        $new_img = imagecreatetruecolor($box_width,$box_height);
        //设置背景颜色
        $color = imagecolorallocate($new_img, 0, 255, 255);

        //填充颜色
        imagefill ($new_img,0,0,$color);

        //保存图片
        imagecolortransparent($new_img,65535);
        imagepng($new_img,$path.'new_img1.png');
        //打开图片
        $top_imgs = imagecreatefrompng($path.'top1.jpg');
        imagecolortransparent($top_imgs,65280);
        imagepng($top_imgs,$path.'top1.jpg');
        $right_imgs = imagecreatefrompng($path.'right1.jpg');
        imagecolortransparent($right_imgs,65280);
        imagepng($right_imgs,$path.'right1.jpg');

        $top_img = imagecreatefrompng($path.'top1.jpg');
        $right_img = imagecreatefrompng($path.'right1.jpg');
        $bottom_img = imagecreatefrompng($path.'bottom1.jpg');
        $left_img = imagecreatefrompng($path.'left1.jpg');

        //打开画布
        $newimg = imagecreatefrompng($path.'new_img1.png');

        //拷贝图片
//            $decora_img = imagecreatefromjpeg(Yii::getAlias('@backend').'\web\goods\\'.$toimg_name);

//            imagecopymerge($newimg,$decora_img,$face_width,$face_width,0,0,$box_width,$box_height,100);
//            imagejpeg($newimg,$path.'new_img2.jpg');

        //复制上框
        imagecopymerge($newimg,$top_img,0,0,0,0,$box_width,$face_width,100);
        //复制左框
        imagecopymerge($newimg,$left_img,0,0,0,0,$face_width,$box_height,100);
        //复制下框
        imagecopymerge($newimg,$bottom_img,0,$box_height-$face_width,0,0,$box_width,$face_width,100);
        //复制右框
        imagecopymerge($newimg,$right_img,$box_width-$face_width,0,0,0,$face_width,$box_height,100);
        //保存图片
        imagepng($newimg,$path.'singlestereo.jpg');
    }
    //生成左图
    function left_img1($path,$img_name,$img_width,$img_height,$face_width,$box_width,$box_height)
    {
        //目标图像
        $dst_im = imagecreatetruecolor($face_width,$box_height);
        $root_img = imagecreatefromjpeg($path.$img_name);
        //copy所需画框区域(不拉伸)
        //imagecopy( $dst_im, $root_img, 0, 0, 0, 0, $face_width, $box_height);
        //copy所需画框区域(拉伸)
        imagecopyresampled($dst_im,$root_img,0,0,0,0,$face_width,$box_height,$face_width,3600);
        //设置三角形顶点位置
        $points = [
            2,0,
            $face_width,0,
            $face_width,$face_width-2
        ];
        //设置颜色
        $blue = imagecolorallocate($dst_im, 0, 255, 0);
        //画三角形
        $root_img = imagefilledpolygon($dst_im,$points,3,$blue);
        //设置三角形顶点位置
        $points2 = [
            1,$box_height,
            $face_width,$box_height,
            $face_width,$box_height-$face_width+1,
        ];
        //设置颜色
        $green = imagecolorallocate($dst_im, 0, 255, 0);
        //画三角形
        $is = imagefilledpolygon($dst_im,$points2,3,$green);
        //设置透明色
        imagecolortransparent($dst_im,65280);
        //保存图片(左边)
        imagepng($dst_im,$path.'left1.jpg');
//            return $dst_im;
    }
    //生成下图
    function bottom_img1($path,$img_name,$img_width,$img_height,$face_width,$box_width,$box_height)
    {
        //目标图像
        $dst_im = imagecreatetruecolor($box_width,$face_width);
        //旋转图像
        $img_root = new \Imagick($path.$img_name);
        //逆时针旋转90度生成下边图
        $img_root->rotateimage('rgb(0,255,0)',-90);
        //保存图片
        file_put_contents($path.'bottom1.jpg',$img_root);
        //打开下图
        $root_img = imagecreatefromjpeg($path.'bottom1.jpg');
        //拷贝所需区域(不拉伸)
        //imagecopy( $dst_im, $root_img, 0, 0, 0, 0,$box_width,$face_width);
        //copy所需画框区域(拉伸)
        imagecopyresampled($dst_im,$root_img,0,0,0,0,$box_width,$face_width,3600,$face_width);
        //保存截好的下图
        imagepng($dst_im,$path.'bottom1.jpg');
        //设置三角形顶点位置
        $points = [
            0,0,
            $face_width,0,
            0,$face_width
        ];
        //设置颜色
        $blue = imagecolorallocate($dst_im, 0, 255, 0);
        //画三角形
        $root_img = imagefilledpolygon($dst_im,$points,3,$blue);
        //设置三角形顶点位置
        $points2 = [
            $box_width,0,
            $box_width,$face_width,
            $box_width-$face_width,0,
        ];
        //设置颜色
        $green = imagecolorallocate($dst_im, 0, 255, 0);
        //画三角形
        $is = imagefilledpolygon($dst_im,$points2,3,$green);
        //设置透明色
        imagecolortransparent($dst_im,65280);
        imagepng($dst_im,$path.'bottom1.jpg');
//        return $dst_im;
    }
    //可能要找的图片(10张)
    public function actionFindmayimg()
    {
        $labels = Goods::find()->select('label')->where(['id'=>$_GET['id']])->one();
        $label = array_filter(array_values(array_unique(explode(",",$labels['label']))));
        sort($label);
        $label_str = '';
        for($i = 0; $i < count($label); $i++){
            $label_str .= 'and (`label` LIKE "%,'.$label[$i].',%") ';
        }
        $may_img_arr = [];
        if(count($may_img_arr)<10){
            $this->cycle($label,$label,$may_img_arr);
            $result = array_slice($this->img_arr,0,10);
            return $result;
        }else{
            $res = array_slice($may_img_arr,0,10);
            return $res;
        }
    }
    function cycle($labels,$label,$may_img_arr)
    {
        if(count($label) != 0){
            $label2 = $label;
            $label_str = '';
            for($i = 0; $i < count($label2); $i++){
                $label_str .= 'and (`label` LIKE "%,'.$label2[$i].',%") ';
            }
            $may_img_arr1 = Yii::$app->db->createCommand("select `id`,`image` from `tsy_goods` where `id` !=".$_GET['id']." ".$label_str."")->queryAll();
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
            if(count($res)<10){
                unset($label2[count($label2)-1]);
                $label2 = array_values($label2);
                $this->cycle($labels,$label2,$res);
            }else{
                $this->img_arr = $res;
            }
        }else{
            if(count($may_img_arr)<10){
                $this->cycle2($labels,$label,$may_img_arr);
            }
        }
    }
    function cycle2($labels,$label,$may_img_arr)
    {
        if(count($labels)!=0){
            $label2 = $labels;
            $label_str2 = '';
            for($q = 0; $q < count($label2); $q++){
                $label_str2 .= 'and (`label` LIKE "%,'.$label2[$q].',%") ';
            }
            $may_img_arr2 = Yii::$app->db->createCommand("select `id`,`image` from `tsy_goods` where `id` !=".$_GET['id']." ".$label_str2."")->queryAll();
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
            if(count($res)<10){
                unset($label2[0]);
                $label2 = array_values($label2);
                $this->cycle2($label2,$label2,$res);
            }else{
                $this->img_arr = $res;
            }
        }else{
            $this->img_arr = $may_img_arr;
        }
    }
    //可能要找的图片(所有)
    public function actionFindmayimgall()
    {
        $pageSize = $_GET['pageSize'];
        $currentPage = $_GET['currentPage'];
        $contrast = '';
        switch($_GET['contrast']){
            case -1:$contrast = '<';break;
            case 0:$contrast = '=';break;
            case 1:$contrast = '>';break;
            case 2:$contrast = 'or';break;
        }
        $label_name_str = '';
        $label_name = [];
        $start = ($currentPage-1) * $pageSize;
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
        $this->cycles($label,$label,$may_img_arr,$contrast);
        $res = $this->img_arr;
        if($res){
            for($i = 0;$i<count($res);$i++){
                $res1[] = [
                    'id' => $res[$i]['id'],
                    'image' => $res[$i]['image'],
//                    'name' => $res[$i]['name'],
                    'label_name' => trim($label_name_str,','),
                    'count' => count($res)
                ];
            }
            $res1 = array_slice($res1,$start,$pageSize,false);
            return $res1;
        }else{
            return false;
        }

        return $res;
    }
    function cycles($labels,$label,$may_img_arr,$contrast)
    {
        if(count($label)!=0){
            $label2 = $label;
            $label_str = '';
            for($i = 0; $i < count($label2); $i++){
                $label_str .= 'and (`label` LIKE "%,'.$label2[$i].',%") ';
            }
            $may_img_arr1 = Yii::$app->db->createCommand("select `id`,`image` from `tsy_goods` where `id` !=".$_GET['id']." ".$label_str." AND `max_length` ".$contrast." `max_width`")->queryAll();
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
            $this->cycles($labels,$label2,$res,$contrast);
        }else{
            $this->cycles2($labels,$label,$may_img_arr,$contrast);
        }
    }
    function cycles2($labels,$label,$may_img_arr,$contrast)
    {
        if(count($labels)!=0){
            $label2 = $labels;
            $label_str2 = '';
            for($q = 0; $q < count($label2); $q++){
                $label_str2 .= 'and (`label` LIKE "%,'.$label2[$q].',%") ';
            }
            $may_img_arr2 = Yii::$app->db->createCommand("select `id`,`image` from `tsy_goods` where `id` !=".$_GET['id']." ".$label_str2." AND `max_length` ".$contrast." `max_width`")->queryAll();
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
            $this->cycles2($label2,$label2,$res,$contrast);
        }else{
            $this->img_arr = $may_img_arr;
        }
    }
//    //标签排序
    public function actionUp_label()
    {
        $label = Goods::find()->select('id,label')->all();
        for($i=0;$i<count($label);$i++){
            if(count(array_filter(explode(',',trim($label[$i]['label'],', ')))) == 0){
                Goods::updateAll(['label' => null], ['id'=>$label[$i]['id']]);
            }else{
                $new_label = array_filter(array_unique(explode(',',trim($label[$i]['label'],', '))));
                sort($new_label);
                $new_labels = ','.implode(',',$new_label).',';
                Goods::updateAll(['label' => $new_labels], ['id'=>$label[$i]['id']]);
                var_dump($label[$i]['id']);
            }
        }
    }
    //添加到购物车
    public function actionTo_shopcar()
    {
        $img_width = $_POST['end_info']['img_width'];//图片宽
        $img_height = $_POST['end_info']['img_height'];//图片高
        $box_img_width = $_POST['end_info']['box_img_width'];//框宽
        $box_img_height = $_POST['end_info']['box_img_height'];//框高
        $decoration_status = $_POST['end_info']['decoration_status'];//装裱方式
        $material_name = $_POST['end_info']['material_name'];//画芯名
        $color_val = $_POST['end_info']['color_val'];//卡纸颜色
        $border_name = $_POST['end_info']['border_name'];//框名
        $preview_img = $_POST['end_info']['preview_img'];//预览图片路径
        $drawing_core_val = $_POST['end_info']['drawing_core_val']?$_POST['end_info']['drawing_core_val']:0;//画芯留边值
        $core_offset = $_POST['end_info']['core_offset']?$_POST['end_info']['drawing_core_val']:0;//画芯偏移值
        $core_offset_direction = $_POST['end_info']['core_offset_direction'];//画芯偏移方向
        $core_shift = $_POST['end_info']['core_shift']?$_POST['end_info']['core_shift']:0;///留边偏移值
        $core_shift_direction = $_POST['end_info']['core_shift_direction'];//留边偏移方向
        $core_price = $_POST['end_info']['core_price'];//画芯价格
        $decoration_price = $_POST['end_info']['decoration_price'];///装裱价格
        $total_price = $_POST['end_info']['total_price'];//总价
        $user_tel = $_POST['end_info']['user_tel'];//用户手机号
        $imgid = $_POST['end_info']['imgid'];//图片id
//        $base64_string = explode(',',$preview_img);
//        $data = base64_decode($base64_string[1]);
        $path = Yii::getAlias('@backend').'\web\preview_img';
        $img_name = date('YmdHis').rand(100,900);
        $res = $this->base64_image_content($preview_img,$path,$img_name);
        $uid = account::find()->select('id')->where(['phone'=>$user_tel])->one();//用户id
        if($res){
            if($img_width && $img_height && $box_img_width && $box_img_height && $decoration_status && $material_name && $color_val && $border_name && $preview_img && $core_offset_direction && $core_shift_direction && $core_price && $decoration_price && $total_price && $user_tel && $imgid){
                $res = Yii::$app->db->createCommand()
                    ->insert('tsy_shopcar',[
                        'goods_id' => $imgid,
                        'user_id' => $uid['id'],
                        'color' => $color_val,
                        'img_name'=> $res,
                        'box_name'=> $border_name,
                        'img_width' => $img_width,
                        'img_height' => $img_height,
                        'box_width' => $box_img_width,
                        'box_height'=>$box_img_height,
                        'decoration_status'=>$decoration_status,
                        'core_material'=>$material_name,
                        'drawing_core_val'=>$drawing_core_val,
                        'core_offset'=>$core_offset,
                        'core_offset_direction'=>$core_offset_direction,
                        'core_shift_val'=>$core_shift,
                        'core_shift_direction'=>$core_shift_direction,
                        'core_price'=>$core_price,
                        'decoration_price'=>$decoration_price,
                        'total_price'=>$total_price,
                        'status'=>0,
                    ])
                    ->execute();
                $result_id = Yii::$app->db->getLastInsertID();
                if($result_id){
                    return 1;//添加成功
                }else{
                    return 0;//添加失败
                }
            }else{
                return 99;//缺少参数
            }
        }else{
            return 100;//图片保存失败
        }
    }
    function base64_image_content($base64_image_content,$path,$img_name)
    {
        //匹配出图片的格式
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)) {
            $type = $result[2];
            $new_file = $path;
            if (!file_exists($new_file)) {
                //检查是否有该文件夹，如果没有就创建，并给予最高权限
                mkdir($new_file, 0700);
            }
            $new_file = $new_file .'\\'. $img_name . ".{$type}";
            if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_image_content)))) {
                return $img_name.'.'.$type;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    //获取登陆界面图片
    public function actionGetloginimg()
    {
        $res = Goods::find()->select('image')->where(['is_login'=>1])->all();
        $num = rand(0,count($res)-1);
        return $res[$num];
    }

}



























