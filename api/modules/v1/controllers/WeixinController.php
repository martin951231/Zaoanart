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
use backend\models\label;
use backend\models\keep;
use backend\models\keepimage;

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
        $res = category::find()->select('id,category_name,face_img')->where(['pid'=>0])->andWhere(['<>','id',999])->all();
        for($i=0;$i<count($res);$i++){
            $info[] = [
                'id'=> $res[$i]['id'],
                'category_name'=> $res[$i]['category_name'],
                'face_img'=> 'http://qiniu.zaoanart.com/'.$res[$i]['face_img'],
            ];
        }
        return $info;
    }
    //获取分类2
    public function actionGetcate2()
    {
        $id = $_GET['id'];
        $res = category::find()->select('id,category_name,face_img')->where(['pid'=>$id])->andWhere(['<>','id',999])->all();
        if($res){
            for($i=0;$i<count($res);$i++){
                $info[] = [
                    'id'=> $res[$i]['id'],
                    'category_name'=> $res[$i]['category_name'],
                    'face_img'=> 'http://qiniu.zaoanart.com/'.$res[$i]['face_img'],
                ];
            }
            return $info;
        }else{
            return false;
        }
    }
    //获取主题
    public function actionGettheme()
    {
        $res = theme::find()->select('id,theme_name,theme_img')->where(['pid'=>0])->andWhere(['<>','id',999])->all();
        for($i=0;$i<count($res);$i++){
            $info[] = [
                'id'=> $res[$i]['id'],
                'theme_name'=> $res[$i]['theme_name'],
                'face_img'=> 'http://qiniu.zaoanart.com/'.$res[$i]['theme_img'],
            ];
        }
        return $info;
    }
    //获取分类图片
    public function actionGetcateimg()
    {
        $cate_id = $_GET['id'];
        $pageSize = 20;
        $start = $_GET['start'];
        $id_arr = category::find()->select('id')->where(['pid'=>$cate_id])->all();
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
        $height = 0;
        for($i=0;$i<count($res);$i++){
            $imgsize = getimagesize('http://qiniu.zaoanart.com/'.$res[$i]['image']);
            $info['image'][] = [
                'id'=> $res[$i]['id'],
                'cate_id'=>$cate_id,
                'name'=> $res[$i]['name'],
                'image'=> 'http://qiniu.zaoanart.com/'.$res[$i]['image'],
//                'imgheight'=> $res[$i]['img_height'],
                'img_height'=> $imgsize[1],
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
        $res = Goods::find()
            ->select('id,image,name,img_height')
            ->where(['theme'=>$theme_id])
            ->andWhere(['or',['<>','category',999],['<>','theme',999]])
            ->andWhere(['is_appear'=>1])
//            ->orderBy(['id'=>SORT_DESC])
            ->limit($pageSize)
            ->offset($start)
            ->all();
        for($i=0;$i<count($res);$i++){
//            $imgsize = getimagesize('http://qiniu.zaoanart.com/'.$res[$i]['image']);
            $info['image'][] = [
                'id'=> $res[$i]['id'],
                'theme_id'=>$theme_id,
                'name'=> $res[$i]['name'],
                'image'=> 'http://qiniu.zaoanart.com/'.$res[$i]['image'],
                'img_height'=> $res[$i]['img_height'],
//                'img_height'=> $imgsize[1],
            ];
        }
        $info['start'] = $start+$pageSize;
        return $info;
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
                $imgsize = getimagesize('http://qiniu.zaoanart.com/'.$info[$i]['image']);
                $res4['image'][] = [
                    'id' => $info[$i]['id'],
                    'name' => $info[$i]['name'],
                    'image' => 'http://qiniu.zaoanart.com/'.$info[$i]['image'],
                    'img_height'=> $imgsize[1],
                ];
            }
            $res4['start'] = $start+$pageSize;
            return $res4;
        }
    }
    //获取图片详情
    public function actionGetimg()
    {
        $img_id = $_GET['id'];
        $res = Goods::find()
            ->select('id,name,category,theme,image')
            ->where(['id'=>$img_id])
            ->andWhere(['is_appear'=>1])
            ->one();
        if($res){
            $info = [
                'id'=>$res['id'],
                'name'=>$res['name'],
                'category'=>$res['category'],
                'theme'=>$res['theme'],
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
                    'image' => 'http://qiniu.zaoanart.com/'.$res[$i]['image'],
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
                $res2[$i] = keepimage::find()->select('id,imgid')->where(['kid'=>$keep[$i]])->limit(3)->all();
                for ($k = 0; $k<count($res2[$i]);$k++){
                    $res3[$i][] = goods::find()->select('id,name,image')->where(['id'=>$res2[$i][$k]['imgid']])->one();
                    $res[$i][] = [
                        'keep_id' => $keep[$i]['id'],
                        'keep_name' => $keep[$i]['keep_name'],
                        'imgid' => $res2[$i][$k]['imgid'],
                        'image' => 'http://qiniu.zaoanart.com/'.$res3[$i][$k]['image'].'?imageView2/1/w/200/h/200'
                    ];
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
                ->andWhere(['or',['<>','category',999],['<>','theme',999]])
                ->andWhere(['is_appear'=>1])
                ->one();
            if($res){
                $info['image'][] = [
                    'id'=> $res['id'],
                    'image'=> 'http://qiniu.zaoanart.com/'.$res['image'],
                    'name'=> $res['name'],
                    'img_height'=> $res['img_height'],
                ];
            }
        }
        $info['start'] = $start+$pageSize;
        return $info;
    }

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