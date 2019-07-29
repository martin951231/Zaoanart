<?php
namespace api\modules\v1\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\data\ActiveDataProvider;
use backend\models\random;
use backend\models\account;
use backend\models\login;
use backend\models\accountChannel;
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
        if (!empty($_SERVER['HTTP_CLIENT_IP'])){//check ip from share internet
            $ip=$_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){//to check ip is pass from proxy
            $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
        }else{
            $ip=$_SERVER['REMOTE_ADDR'];
        }
        $url = "http://ip.taobao.com/service/getIpInfo.php?ip={$ip}";
        $ch = curl_init ();
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = json_decode(curl_exec($ch));
        curl_close($ch);
        if($output){
            $city = $output->data->city;
        }else{
            $city = null;
        }
        if($res1){
            return 1;//手机号码已经存在
        }
        $res = Random::find()->where(['tel'=>$tel])->orderBy(['created_at'=>SORT_DESC])->one();
        if($res){
            if($res['random'] == $code){
                $result = Yii::$app->db->createCommand()
                    ->insert('tsy_account',[
                        'phone'=>$tel,
                        'username'=>$tel,
                        'password'=>md5($pwd),
                        'ip_address'=>$ip,
                        'position'=>$city,
                    ])
                    ->execute();
                $up_channel = 'UPDATE `tsy_account_channel` SET `count`=`count`+1 where `id`=1 ';
                Yii::$app->db->createCommand($up_channel)->execute();
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
        date_default_timezone_set('PRC');
        $tel = $_GET['username'];
        $code = $_GET['code'];
        $res = Account::find()->where(['phone'=>$tel])->all();
        if(!$res){
            return 1;//手机号码不存在
        }
        $res1 = Random::find()->where(['tel'=>$tel])->orderBy(['created_at'=>SORT_DESC])->one();
        if($res1){
            if($res1['random'] == $code){

                //获取登录ip及地址
                if (!empty($_SERVER['HTTP_CLIENT_IP'])){//check ip from share internet
                    $ip=$_SERVER['HTTP_CLIENT_IP'];
                } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){//to check ip is pass from proxy
                    $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
                }else{
                    $ip=$_SERVER['REMOTE_ADDR'];
                }
                $url = "http://ip.taobao.com/service/getIpInfo.php?ip={$ip}";
                $ch = curl_init ();
                curl_setopt($ch, CURLOPT_URL, $url );
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $output = json_decode(curl_exec($ch));
                curl_close($ch);
                if($output){
                    $city = $output->data->city;
                }else{
                    $city = null;
                }
                $sql = 'select `id` from `tsy_login` where TO_DAYS(`created_at`) = TO_DAYS(NOW()) AND hour(`created_at`) = hour(NOW()) AND `login_city`= "'.$city.'" ';
                $id = Yii::$app->db->createCommand($sql)->queryAll();
                if($id){
                    $up_login_sum = 'UPDATE `tsy_login` SET `login_sum`=`login_sum`+1 where `id`='.$id[0]['id'].' ';
                    Yii::$app->db->createCommand($up_login_sum)->execute();
                }else{
                    $res = Yii::$app->db->createCommand()
                        ->insert('tsy_login',[
                            'login_sum' => 1,
                            'created_at' => date("Y-m-d H:i:s"),
                            'login_ip' => $ip,
                            'login_city' => $city,
                        ])
                        ->execute();
                }
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
        date_default_timezone_set('PRC');
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
        //获取登录ip及地址
        if (!empty($_SERVER['HTTP_CLIENT_IP'])){//check ip from share internet
            $ip=$_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){//to check ip is pass from proxy
            $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
        }else{
            $ip=$_SERVER['REMOTE_ADDR'];
        }
        $url = "http://ip.taobao.com/service/getIpInfo.php?ip={$ip}";
        $ch = curl_init ();
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = json_decode(curl_exec($ch));
        curl_close($ch);
        if($output){
            $city = $output->data->city;
        }else{
            $city = null;
        }
        $sql = 'select `id` from `tsy_login` where TO_DAYS(`created_at`) = TO_DAYS(NOW()) AND hour(`created_at`) = hour(NOW()) AND `login_city`= "'.$city.'" ';
        $id = Yii::$app->db->createCommand($sql)->queryAll();
        if($id){
            $up_login_sum = 'UPDATE `tsy_login` SET `login_sum`=`login_sum`+1 where `id`='.$id[0]['id'].' ';
            Yii::$app->db->createCommand($up_login_sum)->execute();
        }else{
            $res = Yii::$app->db->createCommand()
                ->insert('tsy_login',[
                    'login_sum' => 1,
                    'created_at' => date("Y-m-d H:i:s"),
                    'login_ip' => $ip,
                    'login_city' => $city,
                ])
                ->execute();
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