<?php
namespace api\modules\v1\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\data\ActiveDataProvider;
use backend\models\random;
use backend\models\account;
use backend\models\loginimg;

class RegisterController extends ActiveController
{
    public $modelClass = 'api\modules\v1\models\Register';

    public function actions()
    {
        $actions = parent::actions();

        // 注销系统自带的实现方法
        unset($actions['index'], $actions['update'], $actions['create'], $actions['delete'], $actions['view']);
        header('Access-Control-Allow-Origin:*');
        return $actions;
    }
    //注册
    public function actionRegister()
    {
        $tel = $_GET['username'];
        $pwd = $_GET['password'];
        $code = $_GET['code'];
        $checked = $_GET['checked'];
        $res1 = Account::find()->where(['phone'=>$tel])->all();
        if($res1){
            return 1;//手机号码已经存在
        }
        $res = Random::find()->where(['tel'=>$tel])->orderBy(['created_at'=>SORT_DESC])->one();
        if($res){
            if($res['random'] == $code){
                $result = Yii::$app->db->createCommand()
                    ->insert('tsy_account',[
                        'phone'=>$tel,
                        'password'=>md5($pwd)
                    ])
                    ->execute();
                if($result){
                    return 0;//用户创建成功
                }
            }else{
                return 2;//验证码输入有误
            }
        }
    }
    //验证码登录
    public function actionLogin()
    {
        $tel = $_GET['username'];
        $code = $_GET['code'];
        $res = Account::find()->where(['phone'=>$tel])->all();
        if(!$res){
            return 1;//手机号码不存在
        }
        $res1 = Random::find()->where(['tel'=>$tel])->orderBy(['created_at'=>SORT_DESC])->one();
        if($res1){
            if($res1['random'] == $code){
                account::updateAll(['last_login_time'=>date("Y-m-d H:i:s")],['phone'=>$tel]);
                return 0;//登录成功
            }else{
                return 2;//验证码输入有误
            }
        }
    }
    //账号密码登录
    public function actionLoginuser()
    {
        $tel = $_POST['username'];
        $password = $_POST['password'];
        $checked = $_POST['checked'];
        $res = Account::find()->where(['phone'=>$tel])->one();
        if($_COOKIE){
            if($tel == $_COOKIE['tel']){
                return 3;//重复登录
            }
        }
        if(!$res){
            return 1;//手机号码不存在
        }
        if(md5($password) !== $res['password']){
            return 2;//密码错误
        }
        if($checked == 'true'){
            return 10;//记住密码登录成功
        }else{
            return 11;//不记住密码登录成功
        }
    }
    //获取注册页图片
    public function actionGetloginimg()
    {
        $res = Loginimg::find()->select('id,login_img')->all();
        for($i=0;$i<count($res);$i++){
            $info[] = [
                'id' => $res[$i]['id'],
                'login_img' => $res[$i]['login_img'],
            ];
        }
        return $info;
    }
}
?>