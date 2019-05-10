<?php
namespace api\modules\v1\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\QueryParamAuth;
use yii\data\ActiveDataProvider;
use backend\models\category;
use backend\models\BorderMaterial;
use backend\models\DecorationPrice;
use backend\models\Met;
use backend\models\Boxseries;

class BorderController extends ActiveController
{
    public $modelClass = 'api\modules\v1\models\goods';

    public function actions()
    {
        $actions = parent::actions();

        // 注销系统自带的实现方法
        unset($actions['index'], $actions['update'], $actions['create'], $actions['delete'], $actions['view']);
        header('Access-Control-Allow-Origin:*');
        return $actions;
    }
    //获取画框素材
    public function actionGet_border()
    {
        $res = BorderMaterial::find()->select('id,img_name,sid,border_name,preview_img,price,face_width,Thickness')->all();
        for($i=0;$i<count($res);$i++){
            $info[] = [
                'id' =>$res[$i]['id'],
                'img_name' =>$res[$i]['img_name'],
                'border_name' =>$res[$i]['border_name'],
                'preview_img' =>$res[$i]['preview_img'],
                'price' =>$res[$i]['price'],
                'sid' => $res[$i]['sid'],
                'face_width' =>$res[$i]['face_width'],
                'Thickness' =>$res[$i]['Thickness'],
                'border_name2' =>substr($res[$i]['border_name'],0,strpos($res[$i]['border_name'], '_')),
            ];
        }
        return $info;
    }
    //获取画芯材质
    public function actionGet_material()
    {
        $res = Met::find()->select('id,name,price,margin_price')->all();
        return $res;
    }
    //获取装裱方式价格
    public function actionGet_decoration_price()
    {
        $decoration_status = $_GET['decoration_status'];
        $res = DecorationPrice::find()->select('id,price,float_scale')->where(['decoration_code'=>$decoration_status])->one();
        return $res;
    }
    //获取画框色系
    public function actionGet_series()
    {
        $res = Boxseries::find()->select('id,series_name')->all();
        return $res;
    }
}