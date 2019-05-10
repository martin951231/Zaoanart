<?php

namespace backend\controllers;

use Yii;
use backend\models\Ad;
use backend\models\search\Ad as AdSearch;
use yii\web\Controller;
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
        $res1=$res2=$res3=0;
        if($info_title){$res1 = Ad::updateAll([ 'title' => $info_title],['id'=>$_POST['id']]);}
        if($info_num){$res2 = Ad::updateAll([ 'goods_id' => $info_num],['id'=>$_POST['id']]);}
        if($info_list){$res3 = Ad::updateAll([ 'is_appear' => $info_list],['id'=>$_POST['id']]);}
        if($res1 || $res2 || $res3){
            return "添加成功";
        }
    }
}






















