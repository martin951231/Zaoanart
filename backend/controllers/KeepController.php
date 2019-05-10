<?php

namespace backend\controllers;

use Yii;
use backend\models\Keep;
use backend\models\Goods;
use backend\models\Keepimage;
use backend\models\search\KeepSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * KeepController implements the CRUD actions for Keep model.
 */
class KeepController extends Controller
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
     * Lists all Keep models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new KeepSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Keep model.
     * @param string $id
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
     * Creates a new Keep model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Keep();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Keep model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
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
     * Deletes an existing Keep model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Keep model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Keep the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Keep::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    //修改状态
    public function actionStatus()
    {
        $id = $_POST['id'];
        $status = $_POST['is_status'];
        $res = $status == 0 ? Keep::updateAll(['status' => 1], ['id'=>$id]):Keep::updateAll(['status' => 0],['id'=>$id]);
        if($res){
            return $id;
        }else{
            return false;
        }
    }

    //修改置顶
    public function actionTopping()
    {
        $id = $_POST['id'];
        $status = $_POST['is_topping'];
        $res = $status == 0 ? Keep::updateAll(['topping' => 1], ['id'=>$id]):Keep::updateAll(['topping' => 0],['id'=>$id]);
        if($res){
            return $id;
        }else{
            return false;
        }
    }

    //查看收藏夹图片
    public function actionSelect_keep()
    {
        if($_POST){
            $res = Keepimage::find()->select('imgid')->where(['kid'=>$_POST['id']])->all();
            if($res){
                for($i=0;$i<count($res);$i++){
                    $imgid = $res[$i]['imgid'];
                    $img = Goods::find()->select('id,name,image')->where(['id'=>$imgid])->one();
                    $img_info[] = [
                        'id' => $img['id'],
                        'name' => $img['name'],
                        'image' => $img['image'],
                    ];
                }
                return json_encode($img_info);
            }else{
                //收藏夹没有图片
                return 1;
            }
        }
    }
}
