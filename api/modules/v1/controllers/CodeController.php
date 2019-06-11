<?php
namespace api\modules\v1\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\data\ActiveDataProvider;
use backend\models\random;
use backend\models\account;

class CodeController extends ActiveController
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
    //发送验证码
    public function actionSendcode()
    {
        header("Content-type:application/json;charset=utf-8");
        $tel = $_GET['username'];
        $url = 'http://smssh1.253.com/msg/send/json';
        $num = $this->rand_number(0,999999);
        $msg = "【早安艺术】您好，您的验证码是$num";
        $param=array(
            "account"=>"N4193013",
            "password"=>"sZ4Beq5Ybwf841",
            'msg'=>$msg,
            'phone'=>$tel,
            'report'=>true
        );
        $data = json_encode($param);
        $result = $this->curlPost($url, $data);
        $arr = json_decode($result,true);
        if($arr['code']==0){
            $res = Yii::$app->db->createCommand()
                ->insert('tsy_random_tel',[
                    'tel' => $tel,
                    'random' => $num,
                    'errorcode'=>$arr['code'],
                    'errormsg'=>$arr['errorMsg']
                ])
                ->execute();
            if($res){
                $res2 = [
                    'tel' => $tel,
                    'random' => $num,
                ];
                return $res2;
            }else{
                return '操作失败';
            }
        }else{
            return '验证码发送失败';
        }
    }
    //请求发送短信接口
    private function curlPost($url,$postFields){
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_POST, 1 );
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type:application/json;charset=utf-8',
                'Content-Length: '.strlen($postFields)
            )
        );
        $result = curl_exec ( $ch );
        curl_close ( $ch );
        return $result;
    }
    function rand_number ($min, $max) {
        return sprintf("%0".strlen($max)."d", mt_rand($min,$max));
    }
    //查看电话号码是否存在
    public function actionCodetel()
    {
        $tel = $_GET['telephone'];
        $res = Account::find()->where(['phone'=>$tel])->andWhere(['is_deleted'=>0])->one();
        if($res){
            return 1;//用户存在
        }else{
            return 2;//用户不存在
        }
    }
    //验证验证码
    public function actionVercode()
    {
        if($_GET){
            $code = $_GET['code'];
            $tel = $_GET['telephone'];
        }
        $res = Random::find()->where(['tel'=>$tel])->orderBy(['created_at'=>SORT_DESC])->one();
        if($res['random'] == $code){
            return true;//验证码输入正确
        }else{
            return false;//验证码输入有误
        }
    }
}
?>