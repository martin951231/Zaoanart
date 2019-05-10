<?php

namespace backend\controllers;

use Yii;
use backend\models\DecorationPrice;
use backend\models\search\DecorationPriceSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * DecorationPriceController implements the CRUD actions for DecorationPrice model.
 */
class DecorationPriceController extends Controller
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
     * Lists all DecorationPrice models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DecorationPriceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single DecorationPrice model.
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
     * Creates a new DecorationPrice model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new DecorationPrice();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing DecorationPrice model.
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
     * Deletes an existing DecorationPrice model.
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
     * Finds the DecorationPrice model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DecorationPrice the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DecorationPrice::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    //修改价格
    public function actionUpprice()
    {
        if($_POST){
            $id = $_POST['id'];
            $price = $_POST['price'];
            $res = DecorationPrice::updateAll(['price'=>$price],['id'=>$id]);
            if($res){
                $info = [
                    'id' => $id,
                    'price' => $price
                ];
                return json_encode($info);
            }
        }
    }

    //修改浮动比例
    public function actionUpfloat_scale()
    {
        if($_POST){
            $id = $_POST['id'];
            $float_scale = $_POST['float_scale'];
            $res = DecorationPrice::updateAll(['float_scale'=>$float_scale],['id'=>$id]);
            if($res){
                $info = [
                    'id' => $id,
                    'price' => $float_scale
                ];
                return json_encode($info);
            }
        }
    }
}
