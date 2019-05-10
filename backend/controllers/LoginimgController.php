<?php

namespace backend\controllers;

use Yii;
use backend\models\Loginimg;
use backend\models\search\LoginimgSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;

/**
 * LoginimgController implements the CRUD actions for Loginimg model.
 */
class LoginimgController extends Controller
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
     * Lists all Loginimg models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new LoginimgSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Loginimg model.
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
     * Creates a new Loginimg model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Loginimg();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Loginimg model.
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
     * Deletes an existing Loginimg model.
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
     * Finds the Loginimg model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Loginimg the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Loginimg::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    //上传七牛云token
    public function actionGettoken()
    {
        $ak = 'mSlTl2-S30-y-d6BVAVQWx0eh_GHGvmMutQkulCk';
        $sk = 'Yjq9vpfthYcVXeIeGRWKmhp0J4xuvxgp6SN5YVD5';
        $bucket = 'zaoanart';
        $time = time()+60*60;

        for($i=0; $i<count($_POST['img']); $i++){
            $new_name = 'login'.date('YmdHis'.rand(0,999999)).'.jpg';
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
    //添加图片
    public function actionAddimg()
    {
        $login_img = $_POST['login_img'];
        $img_name = $_POST['img_name'];
        if($login_img && $img_name){
            $res = Yii::$app->db->createCommand()
                ->insert('tsy_loginimg',[
                    'img_name' => $img_name,
                    'login_img' => $login_img
                ])
                ->execute();
            $result_id = Yii::$app->db->getLastInsertID();
        }
        if($res){
            $info = [
                'img_name' => $img_name,
                'login_img' => $login_img,
                'id' => $result_id
            ];
            return json_encode($info);
        }else{
            return 0;
        }
    }
}
