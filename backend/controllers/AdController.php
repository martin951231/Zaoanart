<?php

namespace backend\controllers;

use Yii;
use backend\models\Ad;
use backend\models\search\Ad as AdSearch;
use yii\web\Controller;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AdController implements the CRUD actions for Ad model.
 */
class AdController extends Controller
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
     * Lists all Ad models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AdSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Ad model.
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
     * Creates a new Ad model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Ad();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Ad model.
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
     * Deletes an existing Ad model.
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
     * Finds the Ad model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Ad the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Ad::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    public function actionStatus()
    {
        $id = $_POST['id'];
        $is_appear = $_POST['is_appear'];
        $res = $is_appear == 0 ? Ad::updateAll(['is_appear' => 1], ['id'=>$id]):Ad::updateAll(['is_appear' => 0],['id'=>$id]);
        if($res){
            return $id;
        }else{
            return false;
        }
    }
    public function actionUpload()
    {
        $model = new UploadForm();

        if (Yii::$app->request->isPost) {
            $model->image = UploadedFile::getInstances($model, 'image');

            if ($model->image && $model->validate()) {
                foreach ($model->image as $file) {
                    $file->saveAs('uploads/' . $file->baseName . '.' . $file->extension);
                }
            }
        }

        return $this->render('upload', ['model' => $model]);
    }
    //文件上传
    public function actionCreate_image()
    {
        $up_info = $_FILES;
        $up_name = $_FILES['Ad']['name']['image'];
        $up_type = $_FILES['Ad']['type']['image'];
        $up_tmp_name = $_FILES['Ad']['tmp_name']['image'];
        $up_error = $_FILES['Ad']['error']['image'];
        $up_size = $_FILES['Ad']['size']['image'];
        $ob_path = Yii::getAlias('@backend').'\web\Ad';
        $to_path = Yii::getAlias('@backend').'\web\Ad';
            if($up_error['0']>0){
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
            //3.上传文件的大小过滤
            if($up_size['0']>1000000){
                return '文件大小超过1000000';
            }
            //5.上传文件名处理
            $exten_name=pathinfo($up_name['0'],PATHINFO_EXTENSION);
            $file_name=pathinfo($up_name['0'],PATHINFO_BASENAME);
            $file1_name = basename($file_name,".".$exten_name);
            do{
                $main_name=date('YmdHis'.rand(100,999));
                $new_name=$main_name.'.'.$exten_name;
            }while(file_exists($to_path.'/'.$new_name));
            //6.判断目录是否存在,不存在则创建
            $path = $ob_path.'\\'.date("Y-m-d");
            if(!is_dir($path))
            {
                mkdir($path); //新建目录
            }
        //7.判断是否是上传的文件，并执行上传
            if(is_uploaded_file($up_tmp_name['0'])){
                if(move_uploaded_file($up_tmp_name['0'],$ob_path.'\\'.date("Y-m-d").'/'.$new_name)){
                    $res = Yii::$app->db->createCommand()
                        ->insert('tsy_ad',[
                            'image' => date("Y-m-d").'/'.$new_name,
                        ])
                        ->execute();
                    $result_id = Yii::$app->db->getLastInsertID();
                    if($res){
                        $result_info[] = [
                            'id' => $result_id,
                            'name' => $file1_name,
                            'image' => $new_name,
                            'path' => $ob_path,
                            'tmp_name' => $up_tmp_name
                        ];
                    }
                }else{
                    echo '上传文件移动失败!222';
                }
            }else{
                echo '文件不是上传的文件333';
            }
        return json_encode($result_info);
    }
    //信息上传
    public function actionCreate_info()
    {
        $info_title = $_POST['info_title'];
        $info_num = $_POST['info_num'];
        $info_list = $_POST['info_list'];
        $img_name = $_POST['img_name'];
        $new_name = $_POST['new_name'];
        $res = Yii::$app->db->createCommand()
            ->insert('tsy_ad',[
                'image' => $img_name,
                'img_name' => $new_name,
                'title' => $info_title,
                'goods_id' => $info_num,
                'is_appear' => $info_list,
            ])
            ->execute();
        $result_id = Yii::$app->db->getLastInsertID();
        if($result_id){
            return '添加成功';
        }else{
            return '添加失败';
        }
    }
    //上传七牛云获取token
    public function actionGettoken()
    {
        $ak = 'mSlTl2-S30-y-d6BVAVQWx0eh_GHGvmMutQkulCk';
        $sk = 'Yjq9vpfthYcVXeIeGRWKmhp0J4xuvxgp6SN5YVD5';
        $bucket = 'zaoanart';
        $time = time()+60*60;
        for($i=0; $i<count($_POST['img']); $i++){
            $new_name = 'ad'.date('YmdHis'.rand(0,999999)).'.jpg';
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
}






















