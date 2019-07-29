<?php
namespace backend\controllers;

use Yii;
use backend\models\Goods;
use backend\models\Goodscount;
use backend\models\Theme;
use backend\models\Category;
use backend\models\Label;
use backend\models\Page;
use backend\models\search\GoodsSearch;
use backend\models\search\GoodscountSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Transaction;
use yii\web\UploadedFile;
use yii\imagine\Image;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use yii\data\Pagination;

/**
 * GoodsController implements the CRUD actions for Goods model.
 */
class GoodscountController extends Controller
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
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Method:POST,GET');
        $searchModel = new GoodscountSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $count = Goods::find()->count();
        $uid = Yii::$app->user->identity->id;
        $num = Page::find()->select('pagesize')->where(['uid'=>$uid])->one();
        $page = new Pagination(['totalCount' => $count,'pageSize'=>$num['pagesize']]);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'page' => $page
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
    //查询图片所在页
    public function actionFind_img_page()
    {
        if($_POST){
            $id = $_POST['id'];
            $uid = Yii::$app->user->identity->id;
            $num = Page::find()->select('pagesize')->where(['uid'=>$uid])->one();
            $img_count = Goods::find()->where(['<=','id',$id])->count();
            if($img_count < 1){
                return 1;
            }
            $page = intval($img_count/$num['pagesize'])+1;
            $url = Yii::$app->urlManager->createAbsoluteUrl(['goodscount/index','page' => $page,'per-page'=> $num['pagesize']]);
            $data = [
                'url' => $url
            ];
            return json_encode($data);
        }
    }

}






























