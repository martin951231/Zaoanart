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
    //记录网站访问量
    public function actionRecord_access()
    {
        date_default_timezone_set('PRC');
        $sql = 'select `id` from `tsy_access` where TO_DAYS(`created_at`) = TO_DAYS(NOW()) AND hour(`created_at`) = hour(NOW()) ';
        $id = Yii::$app->db->createCommand($sql)->queryAll();
        //记录访问首页
        $up_pv_count = 'UPDATE `tsy_pv` SET `count`=`count`+1 where `id`=1';
        Yii::$app->db->createCommand($up_pv_count)->execute();
        if($id){
            $up_access_sum = 'UPDATE `tsy_access` SET `access_sum`=`access_sum`+1 where `id`='.$id[0]['id'].' ';
            Yii::$app->db->createCommand($up_access_sum)->execute();
        }else{
            $res = Yii::$app->db->createCommand()
                ->insert('tsy_access',[
                    'access_sum' => 1,
                    'created_at' => date("Y-m-d H:i:s"),
                ])
                ->execute();
        }
    }

    //记录访问列表页
    public function actionUp_pv_count2()
    {
        $up_pv_count = 'UPDATE `tsy_pv` SET `count`=`count`+1 where `id`=2';
        Yii::$app->db->createCommand($up_pv_count)->execute();
    }
    //记录访问详情页
    public function actionUp_pv_count3()
    {
        $up_pv_count = 'UPDATE `tsy_pv` SET `count`=`count`+1 where `id`=3';
        Yii::$app->db->createCommand($up_pv_count)->execute();
    }
    //记录访问装裱页
    public function actionUp_pv_count4()
    {
        $up_pv_count = 'UPDATE `tsy_pv` SET `count`=`count`+1 where `id`=4';
        Yii::$app->db->createCommand($up_pv_count)->execute();
    }
    //记录访问收藏夹页
    public function actionUp_pv_count5()
    {
        $up_pv_count = 'UPDATE `tsy_pv` SET `count`=`count`+1 where `id`=5';
        Yii::$app->db->createCommand($up_pv_count)->execute();
    }
    //记录访问收藏夹详情页
    public function actionUp_pv_count6()
    {
        $up_pv_count = 'UPDATE `tsy_pv` SET `count`=`count`+1 where `id`=6';
        Yii::$app->db->createCommand($up_pv_count)->execute();
    }
    //记录访问兴趣图片页
    public function actionUp_pv_count7()
    {
        $up_pv_count = 'UPDATE `tsy_pv` SET `count`=`count`+1 where `id`=7';
        Yii::$app->db->createCommand($up_pv_count)->execute();
    }
    //记录访问相似类型页
    public function actionUp_pv_count8()
    {
        $up_pv_count = 'UPDATE `tsy_pv` SET `count`=`count`+1 where `id`=8';
        Yii::$app->db->createCommand($up_pv_count)->execute();
    }
    //记录访问相似主题页
    public function actionUp_pv_count9()
    {
        $up_pv_count = 'UPDATE `tsy_pv` SET `count`=`count`+1 where `id`=9';
        Yii::$app->db->createCommand($up_pv_count)->execute();
    }
    //记录访问历史足迹页
    public function actionUp_pv_count10()
    {
        $up_pv_count = 'UPDATE `tsy_pv` SET `count`=`count`+1 where `id`=10';
        Yii::$app->db->createCommand($up_pv_count)->execute();
    }
    //记录访问个人中心页
    public function actionUp_pv_count11()
    {
        $up_pv_count = 'UPDATE `tsy_pv` SET `count`=`count`+1 where `id`=11';
        Yii::$app->db->createCommand($up_pv_count)->execute();
    }
    //记录访问购物车页
    public function actionUp_pv_count12()
    {
        $up_pv_count = 'UPDATE `tsy_pv` SET `count`=`count`+1 where `id`=12';
        Yii::$app->db->createCommand($up_pv_count)->execute();
    }
}