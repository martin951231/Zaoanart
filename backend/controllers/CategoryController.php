<?php

namespace backend\controllers;

use Yii;
use backend\models\Category;
use backend\models\search\CategorySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CategoryController implements the CRUD actions for Category model.
 */
class CategoryController extends Controller
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
     * Lists all Category models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CategorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Category model.
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
     * Creates a new Category model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Category();
        $searchModel = new CategorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $res = category::find()->select('id,pid,category_name')->where(['pid'=>'0'])->andWhere(['<>','id',999])->all();
        for($i = 0; $i<count($res); $i++){
            $res1[$res[$i]['category_name']] = category::find()->select('id,category_name')->where(['pid'=>$res[$i]['id']])->all();
        }
        for($k = 0;$k<count($res1);$k++){
            $res2[$k] = [
                'id' => $res[$k]['id'],
                'name' => $res[$k]['category_name'],
                'info' => $res1[$res[$k]['category_name']]
            ];
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'model' => $model,
                'info'=>$res2
            ]);
        }
    }

    /**
     * Updates an existing Category model.
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
     * Deletes an existing Category model.
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
     * Finds the Category model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Category the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Category::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionSelectcategory()
    {
        $model = new Category;
        $res = $model->find()->select('id,pid,category_name')->all();
        foreach($res as $key => $value){
            $result[] = [
                'id' =>$res[$key]['id'],
                'pid' =>$res[$key]['pid'],
                'category_name' =>$res[$key]['category_name'],
            ];
        }
        return json_encode($result);
    }

    public function actionAddcate()
    {
        if(!$_GET['to_id'] && !$_GET['cate_content']){
            return false;
        }
        $to_id = $_GET['to_id'];
        $cate_content = $_GET['cate_content'];
        $res = Yii::$app->db->createCommand()
            ->insert('tsy_category',[
                'pid' => $to_id,
                'category_name'=>$cate_content,
                'order_id'=>rand(1,5),
                'created_at'=>date("Y-m-d H:i:s"),
            ])
            ->execute();
        if($res){
            return true;
        }
    }
}
