<?php
namespace api\modules\v1\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\QueryParamAuth;
use yii\data\ActiveDataProvider;
use backend\models\label;

class LabelController extends ActiveController
{
    public $modelClass = 'api\modules\v1\models\label';

    public function actions()
    {
        $actions = parent::actions();

        // 注销系统自带的实现方法
        unset($actions['index'], $actions['update'], $actions['create'], $actions['delete'], $actions['view']);
        header('Access-Control-Allow-Origin:*');
        return $actions;
    }

    public function actionFindlabel()
    {
        $res = label::find()->select('id,label_name')->limit(14)->all();
        return $res;
    }
    public function actionFindlabel2()
    {
        $res = label::find()->select('id,label_name')->all();
        return $res;
    }
}