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
    public function actionFindgoodsd(){
        $label = $_GET['label_id'];
        $id = $_GET['id'];
        $pageSize = $_GET['pageSize'];
        $currentPage = $_GET['currentPage'];
        $start = ($currentPage-1) * $pageSize;
        if($id){
            $info = Goods::find()->select('id,image,name')->where(['<>','id',$id])->andWhere(['like','label',$label])->andWhere(['is_appear'=>1])->orderBy(['id'=>SORT_DESC])->all();
            $res = array_slice($info,$start,$pageSize,false);
            $res2 = label::find()->select('label_name')->where(['id'=>$label])->one();
            if($res) {
                for ($i = 0; $i < count($res); $i++) {
                    $arr[] = [
                        'id' => $res[$i]['id'],
                        'image' => $res[$i]['image'],
                        'name' => $res[$i]['name'],
                        'label_name' => $res2['label_name'],
                        'count' => count($info)
                    ];
                }
                return $arr;
            }else{
                return false;
            }
        }else{
            $info = Goods::find()->select('id,image,name')->Where(['like','label',$label])->andWhere(['is_appear'=>1])->all();
            $res = array_slice($info,$start,$pageSize,false);
            $res2 = label::find()->select('label_name')->where(['id'=>$label])->one();
            if($res){
                for($i = 0;$i<count($res);$i++){
                    $arr[] = [
                        'id' => $res[$i]['id'],
                        'image' => $res[$i]['image'],
                        'name' => $res[$i]['name'],
                        'label_name' => $res2['label_name'],
                        'count' => count($info)
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
        $label = explode(',',$res['label']);

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
    public function actionFindgoodsown(){
        $param = urldecode($_GET['term']);
        $pageSize = $_GET['pageSize'];
        $currentPage = $_GET['currentPage'];
        $start = ($currentPage-1) * $pageSize;
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
        for($k=0;$k<count($res3);$k++){
            $label_res[$k] = Goods::find()
                ->select('id,image,name')
                ->where(['like','label',$res3[$k]['id']])
                ->andWhere(['is_appear'=>1])
                ->orderBy(['id'=>SORT_DESC])
                ->all();
        }
//        return $label_res;
        for($y=0;$y<count($res1);$y++){
            $cate_res[] = $res1[$y]['id'];
        }
        for($z=0;$z<count($res2);$z++){
            $theme_res[] = $res2[$z]['id'];
        }
        $res = Goods::find()
            ->select('id,image,name')
            ->where(['color' => $color])
            ->orFilterWhere(['like','id',$param])
            ->orFilterWhere(['like','name',$param])
            ->orFilterWhere(['in','category',$cate_res])
            ->orFilterWhere(['in','theme',$theme_res])
            ->orFilterWhere(['like','image',$param])
            ->orFilterWhere(['like','author',$param])
            ->orFilterWhere(['like','price',$param])
            ->orFilterWhere(['like','content',$param])
            ->andWhere(['or',['<>','category',999],['<>','theme',999]])
            ->andWhere(['is_appear'=>1])
            ->orderBy(['id'=>SORT_DESC])
            ->all();
        $end_res = [];
        if(count($label_res)>0){
            for($q=0;$q<count($label_res);$q++){
                $end_res = array_merge($res,$label_res[$q]);
            }
        }else{
            $end_res = $res;
        }
        array_multisort($end_res,SORT_DESC);
        $end_result = array_unique($end_res,SORT_REGULAR);
        $info = array_slice($end_result,$start,$pageSize,false);

        if($info){
            for($i = 0;$i<count($info);$i++){
                $res4[] = [
                    'id' => $info[$i]['id'],
                    'image' => $info[$i]['image'],
                    'name' => $info[$i]['name'],
                    'count' => count($end_result)
                ];
            }
            return $res4;
        }
    }
    public function actionCategory_find()
    {
        $cate_id = $_GET['cate_id'];
        $theme_id = $_GET['theme_id'];
        $color_id = $_GET['color_id'];
        $pageSize = $_GET['pageSize'];
        $currentPage = $_GET['currentPage'];
        $start = ($currentPage-1) * $pageSize;
        $res = [];
        if($cate_id == 0){
            if($theme_id == 0){
                if($color_id == 0){
                    //全空
                    $res = Goods::find()
                        ->select('id,image,name')
                        ->where(['or',['<>','category',999],['<>','theme',999]])
                        ->andWhere(['is_appear'=>1])
                        ->orderBy(['id'=>SORT_DESC])
                        ->all();
                }else{
                    //颜色不空,其他空
                    $res = Goods::find()
                        ->select('id,image,name')
                        ->where(['color'=>$color_id])
                        ->andWhere(['or',['<>','category',999],['<>','theme',999]])
                        ->andWhere(['is_appear'=>1])
                        ->orderBy(['id'=>SORT_DESC])
                        ->all();
                }
            }else{
                if($color_id == 0){
                    //颜色和cate空,theme不空
                    $res = Goods::find()
                        ->select('id,image,name')
                        ->where(['theme'=>$theme_id])
                        ->andWhere(['or',['<>','category',999],['<>','theme',999]])
                        ->andWhere(['is_appear'=>1])
                        ->orderBy(['id'=>SORT_DESC])
                        ->all();
                }else{
                    //颜色和theme不空,cate空
                    $res = Goods::find()
                        ->select('id,image,name')
                        ->where(['theme'=>$theme_id,'color'=>$color_id])
                        ->andWhere(['or',['<>','category',999],['<>','theme',999]])
                        ->andWhere(['is_appear'=>1])
                        ->orderBy(['id'=>SORT_DESC])
                        ->all();
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
                                ->andWhere(['is_appear'=>1])
                                ->orderBy(['id'=>SORT_DESC])
                                ->all();
                        }else{
                            $cate_id2[] = [
                                'id'=>(int)$cate_id
                            ];
                            $result = array_merge($cate_id2,$id_arr);
                            for($i = 0;$i<count($result);$i++){
                                $data[] = Goods::find()
                                    ->select('id,image,name')
                                    ->where(['category'=>$result[$i]['id']])
                                    ->andWhere(['or',['<>','category',999],['<>','theme',999]])
                                    ->andWhere(['is_appear'=>1])
                                    ->orderBy(['id'=>SORT_DESC])
                                    ->all();
                                for($k = 0; $k<count($data[$i]);$k++){
                                    $res[] = [
                                        'id' =>$data[$i][$k]['id'],
                                        'image' =>$data[$i][$k]['image'],
                                        'name' =>$data[$i][$k]['name'],
                                    ];
                                }
                            }
                        }
                    }else{
                        $res = Goods::find()
                            ->select('id,image,name')
                            ->where(['category'=>$cate_id])
                            ->andWhere(['or',['<>','category',999],['<>','theme',999]])
                            ->andWhere(['is_appear'=>1])
                            ->orderBy(['id'=>SORT_DESC])
                            ->all();
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
                                ->andWhere(['is_appear'=>1])
                                ->orderBy(['id'=>SORT_DESC])
                                ->all();
                        }else{
                            $cate_id2[] = [
                                'id'=>(int)$cate_id
                            ];
                            $result = array_merge($cate_id2,$id_arr);
                            for($i = 0;$i<count($result);$i++){
                                $data[] = Goods::find()
                                    ->select('id,image,name')
                                    ->where(['category'=>$result[$i]['id'],'color' => $color_id])
                                    ->andWhere(['or',['<>','category',999],['<>','theme',999]])
                                    ->andWhere(['is_appear'=>1])
                                    ->orderBy(['id'=>SORT_DESC])
                                    ->all();
                                for($k = 0; $k<count($data[$i]);$k++){
                                    $res[] = [
                                        'id' =>$data[$i][$k]['id'],
                                        'image' =>$data[$i][$k]['image'],
                                        'name' =>$data[$i][$k]['name'],
                                    ];
                                }
                            }
                        }
                    }else {
                        $res = Goods::find()
                            ->select('id,image,name')
                            ->where(['category' => $cate_id, 'color' => $color_id])
                            ->andWhere(['or',['<>','category',999],['<>','theme',999]])
                            ->andWhere(['is_appear'=>1])
                            ->orderBy(['id'=>SORT_DESC])
                            ->all();
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
                                ->andWhere(['is_appear'=>1])
                                ->orderBy(['id'=>SORT_DESC])
                                ->all();
                        }else{
                            $cate_id2[] = [
                                'id'=>(int)$cate_id
                            ];
                            $result = array_merge($cate_id2,$id_arr);
                            for($i = 0;$i<count($result);$i++){
                                $data[] = Goods::find()
                                    ->select('id,image,name')
                                    ->where(['category'=>$result[$i]['id'],'theme' => $theme_id])
                                    ->andWhere(['or',['<>','category',999],['<>','theme',999]])
                                    ->andWhere(['is_appear'=>1])
                                    ->orderBy(['id'=>SORT_DESC])
                                    ->all();
                                for($k = 0; $k<count($data[$i]);$k++){
                                    $res[] = [
                                        'id' =>$data[$i][$k]['id'],
                                        'image' =>$data[$i][$k]['image'],
                                        'name' =>$data[$i][$k]['name'],
                                    ];
                                }
                            }
                        }
                    }else {
                        $res = Goods::find()
                            ->select('id,image,name')
                            ->where(['category' => $cate_id, 'theme' => $theme_id])
                            ->andWhere(['or',['<>','category',999],['<>','theme',999]])
                            ->andWhere(['is_appear'=>1])
                            ->orderBy(['id'=>SORT_DESC])
                            ->all();
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
                                ->andWhere(['is_appear'=>1])
                                ->orderBy(['id'=>SORT_DESC])
                                ->all();
                        }else{
                            $cate_id2[] = [
                                'id'=>(int)$cate_id
                            ];
                            $result = array_merge($cate_id2,$id_arr);
                            for($i = 0;$i<count($result);$i++){
                                $data[] = Goods::find()
                                    ->select('id,image,name')
                                    ->where(['category'=>$result[$i]['id'],'theme' => $theme_id,'color' => $color_id])
                                    ->andWhere(['or',['<>','category',999],['<>','theme',999]])
                                    ->andWhere(['is_appear'=>1])
                                    ->orderBy(['id'=>SORT_DESC])
                                    ->all();
                                for($k = 0; $k<count($data[$i]);$k++){
                                    $res[] = [
                                        'id' =>$data[$i][$k]['id'],
                                        'image' =>$data[$i][$k]['image'],
                                        'name' =>$data[$i][$k]['name'],
                                    ];
                                }
                            }
                        }
                    }else {
                        $res = Goods::find()
                            ->select('id,image,name')
                            ->where(['category' => $cate_id, 'theme' => $theme_id, 'color' => $color_id])
                            ->andWhere(['or',['<>','category',999],['<>','theme',999]])
                            ->andWhere(['is_appear'=>1])
                            ->orderBy(['id'=>SORT_DESC])
                            ->all();
                    }
                }
            }
        }
        $info = array_slice($res,$start,$pageSize,false);
        if($info){
            for($i = 0;$i<count($info);$i++){
                $res1[] = [
                    'id' => $info[$i]['id'],
                    'image' => $info[$i]['image'],
                    'name' => $info[$i]['name'],
                    'count' => count($res)
                ];
            }
            return $res1;
        }else{
            return false;
        }

    }
    //装裱
    public function actionDecoration()
    {
        $filename1 = Yii::getAlias('@backend').'\web\test\top.jpg';
        $filename2 = Yii::getAlias('@backend').'\web\test\left.jpg';
        $filename3 = Yii::getAlias('@backend').'\web\test\bottom.jpg';
        $filename4 = Yii::getAlias('@backend').'\web\test\right.jpg';
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
        //源图片名
        $img_name = '20181030160655.jpg';
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
            $this->left_img($path,$img_name,$img_width,$img_height,$face_width,$box_width,$box_height);
//            imagepng($left_img);
            //生成下图
            $this->bottom_img($path,$img_name,$img_width,$img_height,$face_width,$box_width,$box_height);
//            imagepng($bottom_img);
            //打开左图水平对称生成右图
            $img_left = new \Imagick($path.'left.jpg');
            $img_left->flopImage();
            //保存图片
            file_put_contents($path.'right.jpg',$img_left);
            //打开下图垂直对称生成上图
            $img_bottom = new \Imagick($path.'bottom.jpg');
            $img_bottom->flipImage();
            //保存图片
            file_put_contents($path.'top.jpg',$img_bottom);
            //生成一个画板
            $new_img = imagecreatetruecolor($box_width,$box_height);
            //设置背景颜色
            $color = imagecolorallocate($new_img, 0, 255, 255);

        //填充颜色
            imagefill ($new_img,0,0,$color);

            //保存图片
            imagecolortransparent($new_img,65535);
            imagepng($new_img,$path.'new_img.png');
            //打开图片
            $top_imgs = imagecreatefrompng($path.'top.jpg');
            imagecolortransparent($top_imgs,65280);
            imagepng($top_imgs,$path.'top.jpg');
            $right_imgs = imagecreatefrompng($path.'right.jpg');
            imagecolortransparent($right_imgs,65280);
            imagepng($right_imgs,$path.'right.jpg');

            $top_img = imagecreatefrompng($path.'top.jpg');
            $right_img = imagecreatefrompng($path.'right.jpg');
            $bottom_img = imagecreatefrompng($path.'bottom.jpg');
            $left_img = imagecreatefrompng($path.'left.jpg');

        //打开画布
            $newimg = imagecreatefrompng($path.'new_img.png');

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
            imagepng($newimg,$path.'new_img2.jpg');
    }
    //生成左图
    function left_img($path,$img_name,$img_width,$img_height,$face_width,$box_width,$box_height)
    {
        //目标图像
        $dst_im = imagecreatetruecolor($face_width,$box_height);
        $root_img = imagecreatefromjpeg($path.$img_name);
        //copy所需画框区域
        imagecopy( $dst_im, $root_img, 0, 0, 0, 0, $face_width, $box_height);
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
            imagepng($dst_im,$path.'left.jpg');
//            return $dst_im;
    }
    //生成下图
    function bottom_img($path,$img_name,$img_width,$img_height,$face_width,$box_width,$box_height)
    {
        //目标图像
        $dst_im = imagecreatetruecolor($box_width,$face_width);
        //旋转图像
        $img_root = new \Imagick($path.$img_name);
        //逆时针旋转90度生成下边图
        $img_root->rotateimage('rgb(0,255,0)',-90);
        //保存图片
        file_put_contents($path.'bottom.jpg',$img_root);
        //打开下图
        $root_img = imagecreatefromjpeg($path.'bottom.jpg');
        //拷贝所需区域
        imagecopy( $dst_im, $root_img, 0, 0, 0, 0,$box_width,$face_width);
        //保存截好的下图
        imagepng($dst_im,$path.'bottom.jpg');
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
        imagepng($dst_im,$path.'bottom.jpg');
//        return $dst_im;
    }
    //可能要找的图片
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
        if(count($label) != 1){
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
        if(count($labels)!=1){
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
//    //标签排序
    public function actionUp_label()
    {
        $label = Goods::find()->select('id,label')->all();

        for($i=0;$i<count($label);$i++){
            if(strlen(trim($label[$i]['label'],',')) == 0){
                Goods::updateAll(['label' => null], ['id'=>$label[$i]['id']]);
            }else{
                $new_label = array_unique(explode(',',trim($label[$i]['label'],',')));
                sort($new_label);
                $new_labels = ','.implode(',',$new_label).',';
                Goods::updateAll(['label' => $new_labels], ['id'=>$label[$i]['id']]);
            }
        }
    }


}



























