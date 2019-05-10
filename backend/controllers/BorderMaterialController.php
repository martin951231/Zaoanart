<?php

namespace backend\controllers;

use Yii;
use backend\models\BorderMaterial;
use backend\models\search\BorderMaterialSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\imagine\Image;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use backend\models\Boxseries;
/**
 * BorderMaterialController implements the CRUD actions for BorderMaterial model.
 */
class BorderMaterialController extends Controller
{
    /**
     * {@inheritdoc}
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
     * Lists all BorderMaterial models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BorderMaterialSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single BorderMaterial model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new BorderMaterial model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new BorderMaterial();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing BorderMaterial model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing BorderMaterial model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the BorderMaterial model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return BorderMaterial the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BorderMaterial::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    //上传七牛云token(A类)
    public function actionGettoken()
    {
        $ak = 'mSlTl2-S30-y-d6BVAVQWx0eh_GHGvmMutQkulCk';
        $sk = 'Yjq9vpfthYcVXeIeGRWKmhp0J4xuvxgp6SN5YVD5';
        $bucket = 'zaoanart';
        $time = time()+60*60;

        for($i=0; $i<count($_POST['img']); $i++){
            $new_name = 'border'.date('YmdHis'.rand(0,999999)).'.jpg';
            $body = [
                'img_name' => $_POST['img'][$i],
                'new_name' => $new_name,
                'img_width' => '$(imageInfo.width)',
                'img_height' => '$(imageInfo.height)'
            ];
            $putPolicy = [
                'scope ' => $_POST['img'][$i],
                'deadline' => $time,
                'returnBody' => json_encode($body)
            ];

            $auth = new auth($ak,$sk);
            $token = $auth->uploadToken($bucket,null,3600,$putPolicy);

            $uploadToken_json[$i] = [
                'uptoken' => $token,
                'img_name' => $_POST['img'][$i],
                'new_name' => $new_name,
                'data_id' => array_flip($_POST['img'])[$_POST['img'][$i]]
            ];
        }
        return json_encode($uploadToken_json);
    }
    //上传七牛云token(B类)
    public function actionGettoken2()
    {
        $ak = 'mSlTl2-S30-y-d6BVAVQWx0eh_GHGvmMutQkulCk';
        $sk = 'Yjq9vpfthYcVXeIeGRWKmhp0J4xuvxgp6SN5YVD5';
        $bucket = 'zaoanart';
        $time = time()+60*60;
        $auth = new auth($ak,$sk);
        $num = date('YmdHis'.rand(0,999999));
        $new_nameA = 'border_A'.$num.'.jpg';
        $new_nameB = 'border_B'.$num.'.jpg';
        $bodyA = [
            'img_name' => $_POST['img'][0],
            'new_name' => $new_nameA,
            'img_width' => '$(imageInfo.width)',
            'img_height' => '$(imageInfo.height)'
        ];
        $putPolicyA = [
            'scope ' => $_POST['img'][0],
            'deadline' => $time,
            'returnBody' => json_encode($bodyA)
        ];
        $tokenA = $auth->uploadToken($bucket,null,3600,$putPolicyA);
        $uploadToken_json[0] = [
            'uptoken' => $tokenA,
            'img_name' => $_POST['img'][0],
            'new_name' => $new_nameA,
            'data_id' => array_flip($_POST['img'])[$_POST['img'][0]]
        ];
        $bodyB = [
            'img_name' => $_POST['img'][1],
            'new_name' => $new_nameB,
            'img_width' => '$(imageInfo.width)',
            'img_height' => '$(imageInfo.height)'
        ];
        $putPolicyB = [
            'scope ' => $_POST['img'][1],
            'deadline' => $time,
            'returnBody' => json_encode($bodyB)
        ];
        $tokenB = $auth->uploadToken($bucket,null,3600,$putPolicyB);
        $uploadToken_json[1] = [
            'uptoken' => $tokenB,
            'img_name' => $_POST['img'][1],
            'new_name' => $new_nameB,
            'data_id' => array_flip($_POST['img'])[$_POST['img'][1]]
        ];
//        var_dump($uploadToken_json);die;
//
//        for($i=0; $i<count($_POST['img']); $i++){
//            $new_name = 'border'.date('YmdHis'.rand(0,999999)).'.jpg';
//            $body = [
//                'img_name' => $_POST['img'][$i],
//                'new_name' => $new_name,
//                'img_width' => '$(imageInfo.width)',
//                'img_height' => '$(imageInfo.height)'
//            ];
//            $putPolicy = [
//                'scope ' => $_POST['img'][$i],
//                'deadline' => $time,
//                'returnBody' => json_encode($body)
//            ];
//
//            $auth = new auth($ak,$sk);
//            $token = $auth->uploadToken($bucket,null,3600,$putPolicy);
//
//            $uploadToken_json[$i] = [
//                'uptoken' => $token,
//                'img_name' => $_POST['img'][$i],
//                'new_name' => $new_name,
//                'data_id' => array_flip($_POST['img'])[$_POST['img'][$i]]
//            ];
//        }
        return json_encode($uploadToken_json);
    }
    //添加边框素材图
    public function actionAddimg()
    {
        $border_name = $_POST['border_name'];
        $img_name = $_POST['img_name'];
        if($border_name && $img_name){
            $res = Yii::$app->db->createCommand()
                ->insert('tsy_border_material',[
                    'img_name' => $img_name,
                    'border_name' => $border_name
                ])
                ->execute();
            $result_id = Yii::$app->db->getLastInsertID();
        }
        $result = BorderMaterial::find()->select('face_width')->where(['id'=>$result_id])->one();
        $preview_names =  $this->Decoration($img_name,$result['face_width']);
        BorderMaterial::updateAll(['preview_img'=>$preview_names],['id'=>$result_id]);
        if($res){
            $info = [
                'img_name' => $img_name,
                'border_name' => $border_name,
                'id' => $result_id
            ];
            return json_encode($info);
        }else{
            return 0;
        }
    }
    //添加边框素材图
    public function actionAddimg2()
    {
        for($i=0;$i<count($_POST['img_name']);$i++){
            if(strstr($_POST['img_name'][$i],'border_A')){
                $imgnameArr[0] = $_POST['img_name'][$i];
            }else{
                $imgnameArr[1] = $_POST['img_name'][$i];
            }
        }
        for($i=0;$i<count($_POST['border_name']);$i++){
            if(strstr($_POST['border_name'][$i],'_A.jpg')){
                $bordernameArr[0] = $_POST['border_name'][$i];
            }else{
                $bordernameArr[1] = $_POST['border_name'][$i];
            }
        }
        sort($imgnameArr);
        sort($bordernameArr);
        $img_nameA = $imgnameArr[0];
        $img_nameB = $imgnameArr[1];
        $border_nameA = $bordernameArr[0];
        $border_nameB = $bordernameArr[1];
        if($border_nameA && $border_nameB && $img_nameA && $img_nameB){
            $res = Yii::$app->db->createCommand()
                ->insert('tsy_border_material',[
                    'img_name' => $img_nameA.'/'.$img_nameB,
                    'border_name' => $border_nameA.'/'.$border_nameB
                ])
                ->execute();
            $result_id = Yii::$app->db->getLastInsertID();
        }
        $result = BorderMaterial::find()->select('face_width')->where(['id'=>$result_id])->one();
        $preview_names =  $this->Decoration2($img_nameA,$img_nameB,$result['face_width']);
        BorderMaterial::updateAll(['preview_img'=>$preview_names],['id'=>$result_id]);
        if($res){
            return 1;
        }else{
            return 0;
        }
    }

    //装裱画框
    function Decoration2($img_nameA,$img_nameB,$face_width)
    {
        //图片保存路径
        $path = Yii::getAlias('@backend').'\web\test\preview\\';
        //图片获取路径
        $get_path = 'http://qiniu.zaoanart.com/';
        //这里用的GD库
        //设置头部
        header("Content-type:image/jpeg;charset=UTF-8");
        //装框图像路径
        //$decora_img = Yii::getAlias('@backend').'\web\goods\\'.$toimg_name;
        //获取装框图像大小(A)
        $image_sizeA = getimagesize($get_path.$img_nameA);
        //获取装框图像大小(B)
        $image_sizeB = getimagesize($get_path.$img_nameB);
        //图片本来的面宽
        $face_width = $image_sizeA[0];
        //框宽(外框宽)
        $box_width = 1250;
        //框高(外框高)
        $box_height = 1800;
        //生成左图
        $left_img = $this->left_img2($path,$get_path,$img_nameA,$face_width,$box_width,$box_height);
        //生成右图
        $right_img = $this->right_img2($path,$get_path,$img_nameB,$face_width,$box_width,$box_height);
        //垂直对称生成右边图
        //imageflip($right_img,IMG_FLIP_VERTICAL);
        //生成上图
        $top_img = $this->top_img2($path,$get_path,$img_nameA,$face_width,$box_width,$box_height);
        //水平对称生成上边图
        imageflip($top_img,IMG_FLIP_HORIZONTAL);
        //生成下图
        $bottom_img = $this->bottom_img2($path,$get_path,$img_nameB,$face_width,$box_width,$box_height);
        //水平对称生成下边图
        imageflip($bottom_img,IMG_FLIP_HORIZONTAL);
        $c_width = 70;
        $c_height = 100;
        //缩小比例
        $small_bili = intval($box_width/$c_width);
        $small_face = intval($face_width/$small_bili);
        //生成一个画板
//        $new_img = imagecreatetruecolor($box_width,$box_height);
        $new_img = imagecreatetruecolor($c_width,$c_height);
        //设置背景颜色(0.255.255, 65535)
        $color = imagecolorallocate($new_img, 0, 255, 0);
        //填充颜色
        imagefill($new_img,0,0,$color);
        imagecolortransparent($new_img,65280);
        //复制下框
        imagecopyresized($new_img,$bottom_img,0,$c_height-$small_face,0,0,$c_width,$small_face,$box_width,$face_width);
        //复制上框
        imagecopyresized($new_img,$top_img,0,0,0,0,$c_width,$small_face,$box_width,$face_width);
        //复制左框
        imagecopyresized($new_img,$left_img,0,0,0,0,$small_face,$c_height,$face_width,$box_height);
        //复制右框
        imagecopyresized($new_img,$right_img,$c_width-$small_face,0,0,0,$small_face,$c_height,$face_width,$box_height);
        imagecolortransparent($new_img,65280);
//        //设置背景颜色
//        $color = imagecolorallocate($new_img, 0, 255, 255);
//        //填充颜色
//        imagefill ($new_img,0,0,$color);
//        //复制上框
//        imagecopymerge($new_img,$top_img,0,0,0,0,$box_width,$face_width,100);
//        //复制下框
//        imagecopymerge($new_img,$bottom_img,0,$box_height-$face_width,0,0,$box_width,$face_width,100);
//        //复制左框
//        imagecopymerge($new_img,$left_img,0,0,0,0,$face_width,$box_height,100);
//        //复制右框
//        imagecopymerge($new_img,$right_img,$box_width-$face_width,0,0,0,$face_width,$box_height,100);
//        imagecolortransparent($new_img,65535);
//        //缩放图片
//        $newimg = imagescale($new_img,70,100,IMG_BICUBIC_FIXED);
//        imagepng($new_img);die;
//        //填充透明色
//        imagecolortransparent($newimg,65535);
//        imagepng($new_img);die;
        //保存图片
        $preview_name = date('YmdHis').rand(1000,9999).'.png';
        imagepng($new_img,$path.$preview_name);
        return $preview_name;
    }
    //生成左图
    function left_img2($path,$get_path,$img_nameA,$face_width,$box_width,$box_height)
    {
        //目标图像
        $dst_im = imagecreatetruecolor($face_width,$box_height);
        //echo $dst_im;
        $root_img = imagecreatefromjpeg($get_path.$img_nameA);
        //copy所需画框区域
        imagecopyresized( $dst_im, $root_img, 0, 0, 0, 0, $face_width, $box_height,$face_width,3600);
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
        return $dst_im;
    }
    //生成右图
    function right_img2($path,$get_path,$img_nameB,$face_width,$box_width,$box_height)
    {
        //目标图像
        $dst_im = imagecreatetruecolor($face_width,$box_height);
        //图片路径
        $url = $get_path.$img_nameB;
        //打开图片
        $root_img = imagecreatefromjpeg($url);
        //拷贝所需区域
        imagecopyresized( $dst_im, $root_img, 0, 0, 0, 0,$face_width, $box_height,$face_width,3600);
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
            0,$box_height-$face_width,
            0,$box_height,
            $face_width,$box_height,
        ];
        //设置颜色
        $green = imagecolorallocate($dst_im, 0, 255, 0);
        //画三角形
        $is = imagefilledpolygon($dst_im,$points2,3,$green);
        //设置透明色
        imagecolortransparent($dst_im,65280);
        return $dst_im;
    }
    //生成上图
    function top_img2($path,$get_path,$img_nameA,$face_width,$box_width,$box_height)
    {
        //目标图像
        $dst_im = imagecreatetruecolor($face_width,$box_height);
        //echo $dst_im;
        $root_img = imagecreatefromjpeg($get_path.$img_nameA);
        //copy所需画框区域
        imagecopy( $dst_im, $root_img, 0, 0, 0, 0, $face_width, $box_height);
        //顺时针旋转90度生成上边图
        $root_img = imagerotate($root_img,-90,0);
        return $root_img;
    }
    //生成下图
    function bottom_img2($path,$get_path,$img_nameB,$face_width,$box_width,$box_height)
    {
        //目标图像
        $dst_im = imagecreatetruecolor($face_width,$box_height);
        //echo $dst_im;
        $root_img = imagecreatefromjpeg($get_path.$img_nameB);
        //copy所需画框区域
        imagecopy( $dst_im, $root_img, 0, 0, 0, 0, $face_width, $box_height);
        //顺时针旋转90度生成下边图
        $root_img = imagerotate($root_img,-90,0);
        return $root_img;
    }


    //装裱画框
    function Decoration($img_name,$face_width)
    {
        //图片保存路径
        $path = Yii::getAlias('@backend').'\web\test\preview\\';
        //图片获取路径
        $get_path = 'http://qiniu.zaoanart.com/';
        //这里用的GD库
        //设置头部
//        header("Content-type:image/jpeg;charset=UTF-8");
        //装框图像路径
        //$decora_img = Yii::getAlias('@backend').'\web\goods\\'.$toimg_name;
        //获取装框图像大小
        $image_size = getimagesize($get_path.$img_name);
        //图片本来的面宽
        $face_width = $image_size[0];
        //框宽(外框宽)
        $box_width = 1250;
        //框高(外框高)
        $box_height = 1800;
        //生成左图
        $left_img = $this->left_img($path,$get_path,$img_name,$face_width,$box_width,$box_height);
        //生成下图
        $bottom_img = $this->bottom_img($path,$get_path,$img_name,$face_width,$box_width,$box_height);
        //生成一个画板
        $new_img = imagecreatetruecolor($box_width,$box_height);
        //设置背景颜色
        $color = imagecolorallocate($new_img, 0, 255, 255);
        //填充颜色
        imagefill ($new_img,0,0,$color);
        //复制下框
        imagecopymerge($new_img,$bottom_img,0,$box_height-$face_width,0,0,$box_width,$face_width,100);
        //复制左框
        imagecopymerge($new_img,$left_img,0,0,0,0,$face_width,$box_height,100);
        //垂直对称下图生成上图
        imageflip($bottom_img,IMG_FLIP_VERTICAL);
        //复制上框
        imagecopymerge($new_img,$bottom_img,0,0,0,0,$box_width,$face_width,100);
        //垂直对称左图生成右图
        imageflip($left_img,IMG_FLIP_HORIZONTAL);
        //复制右框
        imagecopymerge($new_img,$left_img,$box_width-$face_width,0,0,0,$face_width,$box_height,100);
        //缩放图片
        $newimg = imagescale($new_img,70,100,IMG_NEAREST_NEIGHBOUR);
        //填充透明色
        imagecolortransparent($newimg,65535);
        //保存图片
        $preview_name = date('YmdHis').rand(1000,9999).'.png';
        imagepng($newimg,$path.$preview_name);
        return $preview_name;
    }
    //生成左图
    function left_img($path,$get_path,$img_name,$face_width,$box_width,$box_height)
    {
        //目标图像
        $dst_im = imagecreatetruecolor($face_width,$box_height);
        //echo $dst_im;
        $root_img = imagecreatefromjpeg($get_path.$img_name);
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
        return $dst_im;
    }
    //生成下图
    function bottom_img($path,$get_path,$img_name,$face_width,$box_width,$box_height)
    {
        //目标图像
        $dst_im = imagecreatetruecolor($box_width,$face_width);
        //图片路径
        $url = $get_path.$img_name;
        //打开图片
        $root_img = imagecreatefromjpeg($url);
        //逆时针旋转90度生成下边图
        $root_img = imagerotate($root_img,90,0);
        //拷贝所需区域
        imagecopy( $dst_im, $root_img, 0, 0, 0, 0,$box_width,$face_width);
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
        return $dst_im;
    }
    //修改边框名
    public function actionUpborder_name()
    {
        if($_POST){
            $id = $_POST['id'];
            $name = $_POST['name'];
            $res = BorderMaterial::updateAll(['border_name'=>$name],['id'=>$id]);
            if($res){
                $info = [
                    'id' => $id,
                    'border_name' => $name
                ];
                return json_encode($info);
            }
        }
    }
    //修改面宽
    public function actionUpface_width()
    {
        if($_POST){
            $id = $_POST['id'];
            $face_width = $_POST['face_width'];
            $res = BorderMaterial::updateAll(['face_width'=>$face_width],['id'=>$id]);
            if($res){
                $info = [
                    'id' => $id,
                    'face_width' => $face_width
                ];
                return json_encode($info);
            }
        }
    }
    //修改侧厚
    public function actionUpthickness()
    {
        if($_POST){
            $id = $_POST['id'];
            $Thickness = $_POST['Thickness'];
            $res = BorderMaterial::updateAll(['Thickness'=>$Thickness],['id'=>$id]);
            if($res){
                $info = [
                    'id' => $id,
                    'Thickness' => $Thickness
                ];
                return json_encode($info);
            }
        }
    }
    //修改价格
    public function actionUpprice()
    {
        if($_POST){
            $id = $_POST['id'];
            $price = $_POST['price'];
            $res = BorderMaterial::updateAll(['price'=>$price],['id'=>$id]);
            if($res){
                $info = [
                    'id' => $id,
                    'price' => $price
                ];
                return json_encode($info);
            }
        }
    }
    //修改色系
    public function actionUpseries()
    {
        if($_POST){
            $id = $_POST['id'];
            $sid = $_POST['sid'];
            $res = BorderMaterial::updateAll(['sid'=>$sid],['id'=>$id]);
            $sname = Boxseries::find()->select('series_name')->where(['id'=>$sid])->one();
            if($res){
                $info = [
                    'id' => $id,
                    'sid' => $sid,
                    'series_name' => $sname['series_name']
                ];
                return json_encode($info);
            }
        }
    }
}
