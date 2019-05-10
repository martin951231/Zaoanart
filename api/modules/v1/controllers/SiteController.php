<?php
namespace api\modules\v1\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\QueryParamAuth;
use yii\data\ActiveDataProvider;
use backend\models\goods;

class SiteController extends ActiveController
{

    public $modelClass = 'api\modules\v1\models\Label';

    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items'
    ];

    // public function behaviors()
    // {
    // $behaviors = parent::behaviors();
    // $behaviors['authenticator'] = [
    // 'class' => CompositeAuth::className(),
    // 'authMethods' => [
    // QueryParamAuth::className()
    // ]
    // ];
    // return $behaviors;
    // }
    public function actions()
    {
        $actions = parent::actions();
        // 注销系统自带的实现方法
        unset($actions['index'], $actions['update'], $actions['create'], $actions['delete'], $actions['view']);
        return $actions;
    }

    public function actionIndex()
    {
        $modelClass = $this->modelClass;
        $query = $modelClass::find();
        return new ActiveDataProvider([
            'query' => $query
        ]);
    }

    public function actionCreate()
    {
        $model = new $this->modelClass();
        // $model->load(Yii::$app->getRequest()
        // ->getBodyParams(), '');
        $model->attributes = Yii::$app->request->post();
        if (! $model->save()) {
            return array_values($model->getFirstErrors())[0];
        }
        return $model;
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->attributes = Yii::$app->request->post();
        if (! $model->save()) {
            return array_values($model->getFirstErrors())[0];
        }
        return $model;
    }

    public function actionDelete($id)
    {
        return $this->findModel($id)->delete();
    }

    public function actionView($id)
    {
        return $this->findModel($id);
    }

    protected function findModel($id)
    {
        $modelClass = $this->modelClass;
        if (($model = $modelClass::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function checkAccess($action, $model = null, $params = [])
    {
        // 检查用户能否访问 $action 和 $model
        // 访问被拒绝应抛出ForbiddenHttpException
        // var_dump($params);exit;
    }
    public function actionQwe(){
        $res = goods::find()->select('id')->where(['<', 'id', 200])->all();
        return $res;
    }
}