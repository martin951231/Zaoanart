<?php
namespace backend\controllers;

use Yii;
use backend\models\Goods;
use backend\models\Theme;
use backend\models\Category;
use backend\models\Label;
use backend\models\search\GoodsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Transaction;
use yii\web\UploadedFile;
use yii\imagine\Image;

/**
 * GoodsController implements the CRUD actions for Goods model.
 */
class GoodsController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Goods models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new GoodsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Goods model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Goods model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Goods();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Goods model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Goods model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Goods model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Goods the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Goods::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    //修改状态
    public function actionStatus()
    {
        $id = $_POST['id'];
        $is_appear = $_POST['is_appear'];
        $res = $is_appear == 0 ? Goods::updateAll(['is_appear' => 1], ['id'=>$id]):Goods::updateAll(['is_appear' => 0],['id'=>$id]);
        if($res){
            return $id;
        }else{
            return false;
        }
    }
    //是否推荐
    public function actionRecommend()
    {
        $id = $_POST['id'];
        $is_recommend = $_POST['is_recommend'];
        $res = $is_recommend == 0 ? Goods::updateAll(['is_recommend' => 1], ['id'=>$id]):Goods::updateAll(['is_recommend' => 0],['id'=>$id]);
        if($res){
            return $id;
        }else{
            return false;
        }
    }
    //修改名称
    public function actionUpdatename()
    {
        $id = $_POST['id'];
        $name = $_POST['name'];
        if(!$name){
            return false;
        }
        $res = Goods::updateAll(['name' => $name], ['id'=>$id]);
        $result = [
            'id' => $id,
            'name' => $name
        ];
        return json_encode($result);
    }
    //修改作者
    public function actionUpdateauthor()
    {
        $id = $_POST['id'];
        $author = $_POST['author'];
        if($author == "待添加" || !$author){
            $author = null;
        }
        $res = Goods::updateAll(['author' => $author], ['id'=>$id]);
        $result = [
            'id' => $id,
            'author' => $author
        ];
        return json_encode($result);
    }
    //修改标签
    public function actionUpdatelabel()
    {
        $id = $_POST['id'];
        $label = $_POST['label'];
        if($label == "待添加" || !$label){
            $label = null;
        }
        $res = Goods::updateAll(['label' => $label], ['id'=>$id]);
        $result = [
            'id' => $id,
            'label' => $label
        ];
        return json_encode($result);
    }
    //修改分类
    public function actionUpdatecategory()
    {
        $id = $_POST['id'];
        $pid = $_POST['pid'];
        $res = Goods::updateAll(['category' => $pid], ['id'=>$id]);
        $info = Category::find()->select('id,category_name')->where(['id'=>$pid])->all();
        $info1 = [
            'id' => $id,
            'pid' => $pid,
            'category_name' => $info['0']['category_name']
        ];
        return json_encode($info1);
    }
    //修改主题
    public function actionUpdatetheme()
    {
        $id = $_POST['id'];
        $pid = $_POST['pid'];
        $res = Goods::updateAll(['theme' => $pid], ['id'=>$id]);
        $info = Theme::find()->select('id,theme_name')->where(['id'=>$pid])->all();
        $info1 = [
            'id' => $id,
            'pid' => $pid,
            'theme_name' => $info['0']['theme_name']
        ];
        return json_encode($info1);
    }
    //修改创作时间
    public function actionUpdatetime()
    {
        $id = $_POST['id'];
        $time = $_POST['time'];
        if($time == "未设置" || !$time){
            $time = null;
        }
        $res = Goods::updateAll(['time' => $time], ['id'=>$id]);
        $result = [
            'id' => $id,
            'time' => $time
        ];
        return json_encode($result);
    }
    //修改描述
    public function actionUpdatecontent()
    {
        $id = $_POST['id'];
        $content = $_POST['content'];
        if($content == "暂无描述" || !$content){
            $content = null;
        }
        $res = Goods::updateAll(['content' => $content], ['id'=>$id]);
        $result = [
            'id' => $id,
            'content' => $content
        ];
        return json_encode($result);
    }
    //修改最大高度
    public function actionUpdatemax_length()
    {
        $id = $_POST['id'];
        $max_length = $_POST['max_length'];
        $max_width = $_POST['max_width'];
        if($max_length == "未设置" || !$max_length){
            $max_length = null;
        }
        $res = Goods::updateAll(['max_length' => $max_length,'max_width'=>$max_width], ['id'=>$id]);
        $result = [
            'id' => $id,
            'max_length' => $max_length,
            'max_width' => $max_width
        ];
        return json_encode($result);
    }
    //修改最大宽度
    public function actionUpdatemax_width()
    {
        $id = $_POST['id'];
        $max_width = $_POST['max_width'];
        $max_height = $_POST['max_height'];
        if($max_width == "未设置" || !$max_width){
            $max_width = null;
        }
        $res = Goods::updateAll(['max_width' => $max_width,'max_length' => $max_height], ['id'=>$id]);
        $result = [
            'id' => $id,
            'max_width' => $max_width,
            'max_height' => $max_height
        ];
        return json_encode($result);
    }
    //批量修改
    public function actionUpdate_all()
    {
        if(!$_POST['id']){
            return "暂时没有要修改的了";
        }
        $id = $_POST['id'];
        $status_edit_code = $_POST['status_edit_code'];
        $status_category_code = $_POST['status_category_code'];
        $status_theme_code = $_POST['status_theme_code'];
        $edit_info = $_POST['edit_info'];
        $edit_label = $_POST['edit_label'];
        $res1 = 0;$res2 = 0;$res3 = 0;$res4 = 0;$res5 = 0;$res6 = 0;$res7 = 0;$res8 = 0;$res9 = 0;
        function delete_all($id)
        {
            $res = 0;
            $length = count($id);
            if($length < 1){
                $res = false;
                return $res;
            }else{
                $res = Goods::deleteAll(['id'=>$id]);
                return $res;
            }
        }
        if($status_edit_code){
            switch($status_edit_code){
                case $status_edit_code == 1: $res1 = delete_all($id);break;
                case $status_edit_code == 2: $res2 = Goods::updateAll(['is_appear' => 1], ['id'=>$id]);break;
                case $status_edit_code == 3: $res3 = Goods::updateAll(['is_appear' => 0], ['id'=>$id]);break;
                case $status_edit_code == 4: $res4 = Goods::updateAll(['is_recommend' => 1], ['id'=>$id]);break;
                case $status_edit_code == 5: $res5 = Goods::updateAll(['is_recommend' => 0], ['id'=>$id]);break;
            }
        }
        if($status_category_code){
            $res6 = Goods::updateAll(['category' => $status_category_code], ['id'=>$id]);
        }
        if($status_theme_code){
            $res7 = Goods::updateAll(['theme' => $status_theme_code], ['id'=>$id]);
        }
        if($edit_info){
            $res8 = Goods::updateAll(['premium' => $edit_info], ['id'=>$id]);
        }
        if($edit_label){
            $old_label = Goods::find()->select('label')->where(['id'=>$id])->one();
            $new_label = $old_label['label'].','.$edit_label;
            $res9 = Goods::updateAll(['label' => $new_label], ['id'=>$id]);
        }
        if($res1){
            return "删除成功";
        }
        if(!$res2 && !$res3 && !$res4 && !$res5 && !$res6 && !$res7 && !$res8 && !$res9){
            return "修改失败";
        }else{
            return "修改成功";
        }
    }
    //修改溢价指数
    public function actionUpdatepremium()
    {
        $id = $_POST['id'];
        $premium = $_POST['premium'];
        if(!$premium){
            $premium = 1;
        }
        $res = Goods::updateAll(['premium' => $premium], ['id'=>$id]);
        $result = [
            'id' => $id,
            'premium' => $premium
        ];
        return json_encode($result);
    }
    //多文件上传
    public function actionUploadsimage()
    {
        $up_info = $_FILES['Goods'];
        $up_name = $_FILES['Goods']['name']['image'];
        $up_type = $_FILES['Goods']['type']['image'];
        $up_tmp_name = $_FILES['Goods']['tmp_name']['image'];
        $up_error = $_FILES['Goods']['error']['image'];
        $up_size = $_FILES['Goods']['size']['image'];
        $ob_path = Yii::getAlias('@backend').'\web\Goods';
        $to_path = Yii::getAlias('@backend').'\web\Goods';
        for($i=0;$i<count($up_name);$i++){
            //2.判断文件是否上传错误
            if($up_error[$i]>0){
                switch($up_info['error'][$i]){
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
            //3.上传文件的大小过滤
//            if($up_size[$i]>100000000000){
//                return '文件大小超过1000000';
//            }
            //5.上传文件名处理
            $exten_name=pathinfo($up_name[$i],PATHINFO_EXTENSION);
            $file_name=pathinfo($up_name[$i],PATHINFO_BASENAME);
            $file1_name = basename($file_name,".".$exten_name);
            do{
                $main_name=date('YmdHis'.rand(100,999));
                $new_name=$main_name.'.'.$exten_name;
            }while(file_exists($to_path.'/'.$new_name));

            //6.判断是否是上传的文件，并执行上传

            if(is_uploaded_file($up_tmp_name[$i])){
                if(move_uploaded_file($up_tmp_name[$i],$ob_path.'/'.$new_name)){
                    Image::thumbnail(Yii::getAlias('@backend').'\web\goods'.'\\'.$new_name, 88 , 88,\Imagine\Image\ManipulatorInterface::THUMBNAIL_INSET)->save(Yii::getAlias('@backend').'\web\goods\\goods'.$new_name,['quality' => 200]);
                    $color = $this->getcolor($new_name);
                    $image_size = getimagesize($ob_path.'/'.$new_name);
                    $image_width = $image_size['0'];
                    $image_height = $image_size['1'];
                    $res = Yii::$app->db->createCommand()
                                 ->insert('tsy_goods',[
                                     'name' => $file1_name,
                                     'image' => $new_name,
                                     'is_appear' => 1,
                                     'is_recommend' => 1,
                                     'color'=>$color
                                 ])
                                 ->execute();
                    $result_id = Yii::$app->db->getLastInsertID();
                    if($res){
                        $result_info[$i] = [
                            'id' => $result_id,
                            'name' => $file1_name,
                            'image' => $new_name,
                            'path' => $ob_path,
                            'tmp_name' => $up_tmp_name,
                            'image_length' => $image_height,
                            'image_width' => $image_width
                        ];
                    }
                }else{
                    echo '上传文件移动失败!222';
                }
            }else{
                echo '文件不是上传的文件333';
            }
        }   //for循环的括号
        return json_encode($result_info);
    }
    //批量添加
    public function actionCreate_all()
    {
        $res2=$res3=$res4=$res5=$res6=$res7=$res8=$res9=$res10=$res11=$res12=$res13 = 0;
        for($i=0;$i<count($_POST);$i++){
            if($_POST[$i]['author']){
                $res2 = Goods::updateAll([ 'author' => $_POST[$i]['author']],['id'=>$_POST[$i]['id']]);
            }
            if($_POST[$i]['year']){
                $res3 = Goods::updateAll([ 'time' => $_POST[$i]['year']],['id'=>$_POST[$i]['id']]);
            }
            if($_POST[$i]['more']){
                $res4 = Goods::updateAll([ 'premium' => $_POST[$i]['more']],['id'=>$_POST[$i]['id']]);
            }
            if($_POST[$i]['min_width']){
                $res7 = Goods::updateAll([ 'min_width' => $_POST[$i]['min_width']],['id'=>$_POST[$i]['id']]);
            }
            if($_POST[$i]['min_height']){
                $res8 = Goods::updateAll([ 'min_length' => $_POST[$i]['min_height']],['id'=>$_POST[$i]['id']]);
            }
            if($_POST[$i]['category2']){
                $res9 = Goods::updateAll([ 'category' => $_POST[$i]['category2']],['id'=>$_POST[$i]['id']]);
            }
            if($_POST[$i]['theme2']){
                $res10 = Goods::updateAll([ 'theme' => $_POST[$i]['theme2']],['id'=>$_POST[$i]['id']]);
            }
            if($_POST[$i]['rule_len']){
                $res11 = Goods::updateAll([ 'length' => $_POST[$i]['rule_len']],['id'=>$_POST[$i]['id']]);
            }
            if($_POST[$i]['rule_wid']){
                $res12 = Goods::updateAll([ 'width' => $_POST[$i]['rule_wid']],['id'=>$_POST[$i]['id']]);
            }
            if($_POST[$i]['content']){
                $res13 = Goods::updateAll([ 'content' => $_POST[$i]['content']],['id'=>$_POST[$i]['id']]);
            }
            $image_height = $_POST[$i]['image_height'];
            $image_width = $_POST[$i]['image_width'];
            if(!$_POST[$i]['max_width'] && !$_POST[$i]['max_height']){
                $res5 = Goods::updateAll([ 'max_width' => 88,'max_length'=>88],['id'=>$_POST[$i]['id']]);
            }else if($_POST[$i]['max_width'] && $_POST[$i]['max_height']){
                $res5 = Goods::updateAll([ 'max_width' =>number_format($_POST[$i]['max_width'],2) ,'max_length'=>number_format($_POST[$i]['max_height'],2)],['id'=>$_POST[$i]['id']]);
            }else if($_POST[$i]['max_width'] || $_POST[$i]['max_height']){
                if($_POST[$i]['max_width']){
                    $max_width = $_POST[$i]['max_width'];
                    $max_height = number_format(($max_width*$image_height)/$image_width,2);
                    $res5 = Goods::updateAll([ 'max_width' =>$max_width ,'max_length'=>$max_height],['id'=>$_POST[$i]['id']]);
                }else if($_POST[$i]['max_height']){
                    $max_height = $_POST[$i]['max_height'];
                    $max_width = number_format(($max_height*$image_width)/$image_height,2);
                    $res5 = Goods::updateAll([ 'max_width' =>$max_width ,'max_length'=>$max_height],['id'=>$_POST[$i]['id']]);
                }
            }
        }
        if($res2 || $res3 || $res4 || $res5 || $res7 || $res8 || $res9 || $res10 || $res11 || $res12 || $res13){
            return "添加成功";
        }else{
            return "添加失败";
        }
    }
    //获取图片信息
    public function actionGet_image_info()
    {
        $res = Goods::find()->select('image')->where(['id'=>$_POST['id']])->one();
        $to_path = Yii::getAlias('@backend').'\web\Goods';
        $info = getimagesize($to_path.'\\'.$res['image']);
        $width = $info['0'];
        $height = $info['1'];
        $arr[] = [
            'width' => $width,
            'height' => $height,
        ];
        return json_encode($arr);
    }
    //获取主色调
    public function getcolor($param)
    {
        $r = '';
        $g = '';
        $b = '';
        $img = $param;
        $path = Yii::getAlias('@backend').'\web\goods';
        $new_path = Yii::getAlias('@backend').'\web\goods\\goods';
        if(file_exists($path."\\".$img)){
            $img_info = getimagesize($path."\\".$img);
        }else{
            return '找不到此图片';
        }
        if($img_info['0']/$img_info['1'] >= 2 || $img_info['1']/$img_info['0']>=2){
            if($img_info['0']/$img_info['1'] >= 2){
                Image::thumbnail($path."\\".$img, 5 , 1,\Imagine\Image\ManipulatorInterface::THUMBNAIL_INSET)->save($new_path.'\\k'.$img,['quality' => 200]);
                $average = new \Imagick($new_path."\\k".$img);
            }else{
                Image::thumbnail($path."\\".$img, 1 , 5,\Imagine\Image\ManipulatorInterface::THUMBNAIL_INSET)->save($new_path.'\\c'.$img,['quality' => 200]);
                $average = new \Imagick($new_path."\\c".$img);
            }
        }else{
            Image::thumbnail($path."\\".$img, 1 , 1,\Imagine\Image\ManipulatorInterface::THUMBNAIL_INSET)->save($new_path.'\\px'.$img,['quality' => 200]);
            $average = new \Imagick($new_path."\\px".$img);
        }
        if(file_exists($new_path."\\k".$img)){
            $i = imagecreatefromjpeg($new_path."\\k".$img);
        }else if(file_exists($new_path."\\c".$img)){
            $i = imagecreatefromjpeg($new_path."\\c".$img);
        }else if(file_exists($new_path."\\px".$img)){
            $i = imagecreatefromjpeg($new_path."\\px".$img);
        }else{
            return "找不到此图片";
        }
        $rTotal = 0;
        $gTotal = 0;
        $bTotal = 0;
        $total = 0;
        for ($x=0;$x<imagesx($i);$x++) {
            for ($y=0;$y<imagesy($i);$y++) {
                $rgb = imagecolorat($i,$x,$y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;
                $rTotal += $r;
                $gTotal += $g;
                $bTotal += $b;
                $total++;
            }
        }
        $r = round($rTotal/$total);
        $g = round($gTotal/$total);
        $b = round($bTotal/$total);
        if(file_exists($new_path."\\k".$img)){
            unlink($new_path."\\k".$img);
        }else if(file_exists($new_path."\\c".$img)){
            unlink($new_path."\\c".$img);
        }else if(file_exists($new_path."\\px".$img)){
            unlink($new_path."\\px".$img);
        }
        //白色
        if($r >=110 && $g>=110 && $b >=110 && abs($r-$g) <=15 && abs($g-$b) <=15 && abs($r-$b)<=15){
            return 9;
        }
        //黑色
        if($r <=85 && $g<=85 && $b <=85 && abs($r-$g) <=15 && abs($g-$b) <=15 && abs($r-$b)<=15){
            return 10;
        }
        //红色
        if($r-$g>=50 && $r-$b>=30 && abs($g-$b)<=35 && $r>40 && $b<=80){
            if($r>=100){
                $rgb = [$r,$g,$b];
                switch($rgb){
                    case $rgb['0']>=100 && $rgb['0']<150 : $rgb['0']-$rgb['1']>=40? $num =  1 :  $num =  2;
                    case $rgb['0']>=150 : $rgb['0']-$rgb['1']>=75?  $num =  1 :  $num =  2;
                    case $rgb['0']>=200 : $rgb['0']-$rgb['1']>=100?  $num =  1 :  $num =  2;
                }
                return $num;
            }
            return 1;
        }
        //橙色
        if($g>=70 && $r-$g>=50 && $r-$g<=150 && $b<=$g){
                if($r>=100){
                    $rgb = [$r,$g,$b];
                    switch($rgb){
                        case $rgb['0']>=100 && $rgb['0']<150 : $rgb['0']-$rgb['1']>=40? $num =  1 :  $num =  2;
                        case $rgb['0']>=150 : $rgb['0']-$rgb['1']>=75?  $num =  1 :  $num =  2;
                        case $rgb['0']>=200 : $rgb['0']-$rgb['1']>=100?  $num =  1 :  $num =  2;
                    }
                    return $num;
                }
            return 2;
        }
        //黄色
        if($r>=$g && abs($r-$g)<=60 && $b<$r && $b<$g){
            return 3;
        }
        //绿色
        if((abs($r-$b)<=70 || abs($b-$r)<=35) && $g-$r>=2 && $g-$b>=2){
            return 4;
        }
        //青色
        if($b-$r>=30 && $r<=170 && $g>$r && $b>$r && $g>50 && $b>50 && $g-$r>=70){
            return 5;
        }
        //蓝色
        if($r<150 && abs($b-$r)>=65 && $b>50 && $b>$r && $b>$g){
            return 6;
        }
        //紫色
        if($g<=150 && $b-$r<=200 && $b>=$r && $b-$g>=40){
            return 7;
        }
        //粉色
        if($r-$b<=150 && $g<=180 && $r>=$b && $r-$g>=30){
            return 8;
        }
        //白色
        if($r >=130 && $g>=130 && $b >=130 && $r-$g <=30 && $g-$b <=30 && $r-$b<=30){
            return 9;
        }
        //黑色
        if($r <=85 && $g<=85 && $b <=85 && $r-$g <=20 && $g-$b <=20 && $r-$b<=20){
            return 10;
        }
            return 11;
    }
    //修改颜色(修改数据库数据)
    public function actionUpdatecolor()
    {
        $res = Goods::find()->select('id')->where(['>', 'id', 200])->all();
        for($i = 0;$i<count($res);$i++){
            $id = $res[$i]['id'];
            $res1 = Goods::find()->select('image')->where(['id'=>$id])->one();
            $img = $res1['image'];
            $color = $this->getcolor($img);
            $res2 = Goods::updateAll(['color' => $color,'updated_at'=>date("Y-m-d H:i:s")], ['id'=>$id]);
        }
    }
    //修改颜色(点击修改Ajax请求)
    public function actionUpdate_color()
    {
        $id = $_POST['id'];
        $color = $_POST['color'];
        $res = Goods::updateAll(['color' => $color], ['id'=>$id]);
        $info = [
            'id' => $id,
            'color' => $color,
        ];
        return json_encode($info);
    }
    //查询我的标签
    public function actionSelect_label()
    {
        $id = $_POST['id'];
        $res = goods::find()->select('label,id')->where(['id'=>$id])->one();
        $label = explode(',',$res['label']);
        $id = $res['id'];
        for($i = 0;$i<count($label);$i++){
            $data = label::find()->select('label_name,id')->where(['id'=>$label[$i]])->one();
            $label_name = $data['label_name'];
            $label_id = $data['id'];
            $info[] = [
                'id'=>$label[$i],
                'label_name'=> $label_name
            ];
        }
        for($num = 0;$num<count($info);$num++){
            if($info[$num]['label_name']){
                $name[] = $info[$num]['id'];
            }
        }
        $labels = implode(',',$name);
        goods::updateAll(['label' => $labels], ['id'=>$id]);
        return json_encode($info);
    }
    //添加我的标签
    public function actionAdd_mylabel()
    {
        $label_id = $_POST['label_id'];
        $id = $_POST['id'];
        $info = goods::find()->select('label')->where(['id'=>$id])->one();
        $label = trim($info['label'].','.$label_id,',');
        $res = goods::updateAll(['label' => $label], ['id'=>$id]);
            $result[] = [
                'id'=>$label_id,
                'label_name'=>label::find()->select('label_name')->where(['id'=>$label_id])->one()->label_name
            ];
        return json_encode($result);
    }
    //删除我的标签
    public function actionDelete_mylabel()
    {
        $id = $_POST['id'];
        $label_id = $_POST['label_id'];
        $info = goods::find()->select('label')->where(['id'=>$id])->one();
        $label = explode(',',$info['label']);
        foreach($label as $k=>$v){
            if($v == $label_id){
                unset($label[$k]);
            }
        }
        $new_label = implode(',',$label);
        $res = goods::updateAll(['label' => $new_label], ['id'=>$id]);
        if($res){
            $result[]=[
                'id'=>$id,
                'label'=>$label,
                'del_labelid'=>$label_id,
                'del_labelname'=>label::find()->select('label_name')->where(['id'=>$label_id])->one()->label_name
            ];
            return json_encode($result);
        }else{
            return false;
        }
    }
}






























