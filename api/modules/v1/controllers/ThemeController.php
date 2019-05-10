<?php
namespace api\modules\v1\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\QueryParamAuth;
use yii\data\ActiveDataProvider;
use backend\models\theme;

class ThemeController extends ActiveController
{
    public $modelClass = 'api\modules\v1\models\theme';

    public function actions()
    {
        $actions = parent::actions();

        // 注销系统自带的实现方法
        unset($actions['index'], $actions['update'], $actions['create'], $actions['delete'], $actions['view']);
        header('Access-Control-Allow-Origin:*');
        return $actions;
    }

    public function actionFindtheme()
    {
        $res = theme::find()->select('id,pid,theme_name,theme_img')->where(['<>','id',999])->all();
        return $res;
    }
}