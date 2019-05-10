<?php
namespace api\modules\v1\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\QueryParamAuth;
use yii\data\ActiveDataProvider;
use backend\models\category;

class CategoryController extends ActiveController
{
    public $modelClass = 'api\modules\v1\models\category';

    public function actions()
    {
        $actions = parent::actions();

        // 注销系统自带的实现方法
        unset($actions['index'], $actions['update'], $actions['create'], $actions['delete'], $actions['view']);
        header('Access-Control-Allow-Origin:*');
        return $actions;
    }

    public function actionFindcategory()
    {
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
        return $res2;
    }
    public function actionFindcategory1()
    {
        $res = category::find()->select('id,pid,category_name,face_img')->all();
        return $res;
    }

}