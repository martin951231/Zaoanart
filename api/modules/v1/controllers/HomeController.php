<?php
namespace api\modules\v1\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\data\ActiveDataProvider;
use backend\models\random;
use backend\models\account;
use backend\models\goods;
use backend\models\keep;
use backend\models\ad;
use backend\models\keepimage;
use backend\models\Shopcar;
use backend\models\History;
use moonland\phpexcel\Excel;
use backend\models\attention;
use backend\models\attentionUser;
use backend\models\accountChannel;

class HomeController extends ActiveController
{
    public $modelClass = 'api\modules\v1\models\account';

    public function actions()
    {
        $actions = parent::actions();

        // 注销系统自带的实现方法
        unset($actions['index'], $actions['update'], $actions['create'], $actions['delete'], $actions['view']);
        header('Access-Control-Allow-Origin:*');
        return $actions;
    }
    public function actionHomeinfo()
    {
        if($_POST){
            $tel = $_POST['username'];
            $res = account::find()->where(['phone'=>$tel])->one();
            return $res;
        }else{
            return false;
        }
    }
    //修改用户名
    public function actionUp_username()
    {
        if($_POST){
            $tel = $_POST['tel'];
            $username = $_POST['username'];
        }else{
            return false;
        }
        $res = account::updateAll(['username'=>$username],['phone'=>$tel]);
        if($res){
            return true;
        }else{
            return false;
        }
    }
    //修改微信
    public function actionUp_wechat()
    {
        if($_POST){
            $tel = $_POST['tel'];
            $wechat = $_POST['wechat'];
        }else{
            return false;
        }
        $res = account::updateAll(['weixin'=>$wechat],['phone'=>$tel]);
        if($res){
            return true;
        }else{
            return false;
        }
    }
    //修改地址
    public function actionUp_address()
    {
        if($_POST){
            $tel = $_POST['tel'];
            $address = $_POST['address'];
        }else{
            return false;
        }
        $res = account::updateAll(['position'=>$address],['phone'=>$tel]);
        if($res){
            return true;
        }else{
            return false;
        }
    }
    //修改生日
    public function actionUp_birthday()
    {
        if($_POST){
            $tel = $_POST['tel'];
            $birthday = $_POST['birthday'];
        }else{
            return false;
        }
        $res = account::updateAll(['birthday'=>$birthday],['phone'=>$tel]);
        if($res){
            return true;
        }else{
            return false;
        }
    }
    //修改密码
    public function actionUp_newpwd()
    {
        if($_POST){
            $tel = $_POST['telephone'];
            $newpwd = $_POST['newpwd'];
        }else{
            return false;
        }
        $phone = account::find()->select('password')->where(['phone'=>$tel])->one();
        if($phone['password'] == md5($newpwd)){
            return 1;
        }
        $res = account::updateAll(['password'=>md5($newpwd)],['phone'=>$tel]);
        if($res){
            return 2;
        }else{
            return 0;
        }
    }
    //查询我的收藏夹
    public function actionFindkeep()
    {
        if($_GET){
            $tel = $_GET['tel'];
        }else{
            return false;
        }
        $user = account::find()->select('id')->where(['phone'=>$tel])->one();
        $uid = $user['id'];
        $keep = keep::find()->select('id,keep_name,uid,heat,img_ratio')->where(['uid'=>$uid])->all();
        if($keep){
            for($i = 0; $i<count($keep);$i++){
                $attention_num = attention::find()->select('id')->where(['kid'=>$keep[$i]['id']])->count();
                $res2[$i] = keepimage::find()->select('id,imgid')->where(['kid'=>$keep[$i]['id']])->limit(4)->orderBy('created_at')->all();
                for ($k = 0; $k<count($res2[$i]);$k++){
                    $res3[$i][] = goods::find()->select('id,name,image,')->where(['id'=>$res2[$i][$k]['imgid']])->one();
                    $res[$i][] = [
                        'id' => $keep[$i]['id'],
                        'keep_name' => $keep[$i]['keep_name'],
                        'heat' => $keep[$i]['heat'],
                        'attention_num' => $attention_num,
                            'img_ratio'=> $keep[$i]['img_ratio'],
//                        'kid' => $res2[$i][$k]['id'],
                        'imgid' => $res2[$i][$k]['imgid'],
                        'imgname' => $res3[$i][$k]['name'],
                        'image' => 'http://qiniu.zaoanart.com/'.$res3[$i][$k]['image']
                    ];
                }
            }
            for($v = 0; $v<count($keep); $v++){
                if(empty($res2[$v])){
                    $res[$v][] = [
                        'id' => $keep[$v]['id'],
                        'keep_name' => $keep[$v]['keep_name'],
                    ];
                }
            }
            return $res;
        }
    }
    //查询收藏夹图片
    public function actionFindkeepimg()
    {
        if($_GET){
            $kid = $_GET['kid'];
        }else{
            return false;
        }
        $res = keepimage::find()->select('id,imgid')->where(['kid'=>$kid])->all();
        $res2 = keep::find()->select('keep_name')->where(['id'=>$kid])->one();
        if($res){
            for($i=0; $i<count($res); $i++){
                $img_info[] = goods::find()->select('id,name,image')->where(['id'=>$res[$i]['imgid']])->one();
            }
            $arr['info'] = array_values(array_filter($img_info));
            $arr['keep_name'] = $res2['keep_name'];
            return $arr;
        }else{
            return false;
        }
    }
    //添加收藏夹
    public function actionAddkeep()
    {
        if($_GET){
            $tel = $_GET['tel'];
            $keep_name = $_GET['keep_name'];
        }else{
            return false;
        }
        $uid = account::find()->select('id')->where(['phone'=>$tel])->one();
        $keep_info = Keep::find()->select('id')->where(['keep_name'=>$keep_name,'uid' => $uid['id']])->all();
        if($keep_info){
            return 1;//该收藏夹名已经存在
        }
        $res = Yii::$app->db->createCommand()
                            ->insert('tsy_keep',[
                                'uid' => $uid['id'],
                                'keep_name' => $keep_name
                            ])
                            ->execute();
        $kid = Yii::$app->db->getLastInsertId();
        $arr = [
            'uid' => $uid['id'],
            'kid' => $kid,
            'keep_name' => $keep_name
        ];
        return $arr;
    }
    //修改收藏夹名字
    public function actionUpkeepname()
    {
        if($_GET){
            $kid = $_GET['id'];
            $keep_name = $_GET['keep_name'];
            $tel = $_GET['tel'];
        }else{
            return false;
        }
        $uid = account::find()->select('id')->where(['phone'=>$tel])->one();
        $res = keep::updateAll(['keep_name'=>$keep_name],['uid'=>$uid['id'],'id'=>$kid]);
        if($res){
            return true;
        }else{
            return false;
        }
    }
    //删除收藏夹
    public function actionDelete_keep()
    {
        if($_GET){
            $kid = $_GET['kid'];
            $tel = $_GET['tel'];
        }else{
            return false;
        }
        $uid = account::find()->select('id')->where(['phone'=>$tel])->one();
        $res = keepimage::deleteAll(['kid'=>$kid]);
        $res2 = keep::deleteAll(['id'=>$kid,'uid'=>$uid['id']]);
        if($res || $res2){
            return true;
        }else{
            return false;
        }
    }
    //查询收藏夹(二级页)
    public function actionSelect_keep()
    {
        if($_GET){
            $tel = $_GET['tel'];
        }else{
            return false;
        }
        $uid = account::find()->select('id')->where(['phone'=>$tel])->one();
        $res = keep::find()->select('id,keep_name')->where(['uid'=>$uid])->orderBy(['updated_at'=>SORT_DESC])->all();
        if($res){
            return $res;
        }
    }
    //添加到收藏夹
    public function actionAddto_keep()
    {
        if($_GET){
            $tel = $_GET['tel'];
            $keep_name = $_GET['keep_name'];
            $img_id = $_GET['img_id'];
        }else{
            return false;
        }
        $up_goods_keep_sum = 'UPDATE `tsy_goods` SET `keep_sum`=`keep_sum`+1 where `id`='.$img_id.' ';
        Yii::$app->db->createCommand($up_goods_keep_sum)->execute();
        $uid = account::find()->select('id')->where(['phone'=>$tel])->one();
        $kid = keep::find()->select('id')->where(['uid'=>$uid,'keep_name'=>$keep_name])->one();
        if(!$kid && !$uid){
            return 2;
        }
        $res = keep::updateAll(['updated_at'=>date("Y-m-d H:i:s")],['uid'=>$uid['id'],'keep_name'=>$keep_name]);
        $img = keepimage::find()->where(['kid'=>$kid['id'],'imgid' => $img_id,])->all();
        if($img){
            return 1;
        }
        $res2 = Yii::$app->db->createCommand()
                            ->insert('tsy_keep_image',[
                                'imgid' => $img_id,
                                'created_at'=>date("Y-m-d H:i:s"),
                                'kid'=>$kid['id']
                            ])
                            ->execute();
        if($res2){
            return 0;
        }
    }
    //添加历史纪录
    public function actionSet_history()
    {
        if($_GET){
            $tel = $_GET['tel'];
            $img_id = $_GET['img_id'];
        }else{
            return false;
        }
        $uid = account::find()->select('id')->where(['phone'=>$tel])->one();
        $exists = history::find()->where(['uid'=>$uid,'creates_at'=>date("Y-m-d"),'imgid'=>$img_id])->all();
        if(!$exists){
            $res = Yii::$app->db->createCommand()
                                ->insert('tsy_history',[
                                    'imgid' => $img_id,
                                    'created_at'=>date("Y-m-d H:i:s"),
                                    'creates_at'=>date("Y-m-d"),
                                    'uid'=>$uid['id']
                                ])
                                ->execute();
        }
    }
    //获取历史纪录
    public function actionGet_history()
    {
        if($_GET){
            $tel = $_GET['tel'];
        }else{
            return false;
        }
        $uid = account::find()->select('id')->where(['phone'=>$tel])->one();
        $res = history::find()->select('creates_at')
                                ->where(['uid'=>$uid['id']])
                                ->addGroupBy('creates_at')
                                ->orderBy(['creates_at'=>SORT_DESC])
                                ->limit(7)
                                ->all();
        for($i = 0; $i < count($res); $i++){
            $img[] = history::find()->select('imgid')->where(['uid'=>$uid['id'],'creates_at'=>$res[$i]['creates_at']])->all();
            for($k = 0; $k< count($img[$i]); $k++){
                $img_info[$res[$i]['creates_at']][$img[$i][$k]['imgid']] = 'http://qiniu.zaoanart.com/'.
                                                                            goods::find()->select('image')
                                                                                        ->where(['id'=>$img[$i][$k]['imgid']])
                                                                                        ->one()['image'].'?imageView2/1/w/250/h/250';
            }
        }
        return $img_info;
    }
    //移动收藏夹图片
    public function actionMove_img()
    {
        if($_GET){
            $tel = $_GET['tel'];
            $img_id = $_GET['img_id'];
            $new_keep = $_GET['new_keep'];
            $old_keep = $_GET['old_keep'];
        }else{
            return false;
        }
        $uid = account::find()->select('id')->where(['phone'=>$tel])->one();
        $new_keep_id = keep::find()->select('id')->where(['keep_name'=>$new_keep,'uid'=>$uid['id']])->one();
        $old_keep_id = keep::find()->select('id')->where(['keep_name'=>$old_keep,'uid'=>$uid['id']])->one();
//        var_dump($img_id,$new_keep_id['id'],$old_keep_id['id']);die;
        $res = keepimage::updateAll(['updated_at'=>date("Y-m-d H:i:s"),'kid'=>$new_keep_id['id']],['kid'=>$old_keep_id['id'],'imgid'=>$img_id]);
        if($res){
            return true;
        }
    }
    //复制收藏夹图片
    public function actionCopy_img()
    {
        if($_GET){
            $tel = $_GET['tel'];
            $img_id = $_GET['img_id'];
            $new_keep = $_GET['new_keep'];
            $old_keep = $_GET['old_keep'];
        }else{
            return false;
        }
        $uid = account::find()->select('id')->where(['phone'=>$tel])->one();
        $new_keep_id = keep::find()->select('id')->where(['keep_name'=>$new_keep,'uid'=>$uid['id']])->one();
        $old_keep_id = keep::find()->select('id')->where(['keep_name'=>$old_keep,'uid'=>$uid['id']])->one();
        $res = keepimage::find()->where(['imgid'=>$img_id,'kid'=>$new_keep_id['id']])->all();
        if($res){
            return 1;//已存在
        }
        $res2 = Yii::$app->db->createCommand()
                            ->insert('tsy_keep_image',[
                                'imgid' => $img_id,
                                'created_at'=>date("Y-m-d H:i:s"),
                                'kid'=>$new_keep_id['id']
                            ])
                            ->execute();

        if($res2){
            return 0;//复制成功
        }else{
            return 2;//复制失败
        }
    }
    //删除收藏夹图片
    public function actionDelete_img()
    {
        if($_GET){
            $tel = $_GET['tel'];
            $img_id = $_GET['img_id'];
            $keep = $_GET['keep'];
        }else{
            return false;
        }
        $uid = account::find()->select('id')->where(['phone'=>$tel])->one();
        $keep_id = keep::find()->select('id')->where(['keep_name'=>$keep,'uid'=>$uid['id']])->one();
        $res = keepimage::deleteAll(['imgid'=>$img_id,'kid'=>$keep_id['id']]);
        if($res){
            return true;
        }else{
            return false;
        }
    }
    //批量移动
    public function actionMove_img_all()
    {
        if($_GET){
            $tel = $_GET['tel'];
            $img_id = $_GET['img_id'];
            $new_keep = $_GET['new_keep'];
            $old_keep = $_GET['old_keep'];
        }else{
            return false;
        }
        $res1 = $res2 = false;
        $uid = account::find()->select('id')->where(['phone'=>$tel])->one();
        $new_keep_id = keep::find()->select('id')->where(['keep_name'=>$new_keep,'uid'=>$uid['id']])->one();
        $old_keep_id = keep::find()->select('id')->where(['keep_name'=>$old_keep,'uid'=>$uid['id']])->one();
        for($i=0;$i<count($img_id);$i++){
            $res = keepimage::find()->where(['imgid'=>$img_id[$i],'kid'=>$new_keep_id])->one();
            if($res){
                $res1 = keepimage::deleteAll(['imgid'=>$img_id[$i],'kid'=>$old_keep_id['id']]);
                continue;
            }else{
                $res2 = keepimage::updateAll(['updated_at'=>date("Y-m-d H:i:s"),'kid'=>$new_keep_id['id']],['kid'=>$old_keep_id['id'],'imgid'=>$img_id[$i]]);
            }
        }
        if($res1 || $res2){
            return true;
        }else{
            return false;
        }
    }
    //批量复制
    public function actionCopy_img_all()
    {
        if($_GET){
            $tel = $_GET['tel'];
            $img_id = $_GET['img_id'];
            $new_keep = $_GET['new_keep'];
            $old_keep = $_GET['old_keep'];
        }else{
            return false;
        }
        $res = $res2 = false;
        $uid = account::find()->select('id')->where(['phone'=>$tel])->one();
        $new_keep_id = keep::find()->select('id')->where(['keep_name'=>$new_keep,'uid'=>$uid['id']])->one();
        $old_keep_id = keep::find()->select('id')->where(['keep_name'=>$old_keep,'uid'=>$uid['id']])->one();
        for($i=0;$i<count($img_id);$i++){
            $res = keepimage::find()->where(['imgid'=>$img_id[$i],'kid'=>$new_keep_id['id']])->all();
            if($res){
                continue;
            }else{
                $res2 = Yii::$app->db->createCommand()
                                    ->insert('tsy_keep_image',[
                                        'imgid' => $img_id[$i],
                                        'created_at'=>date("Y-m-d H:i:s"),
                                        'kid'=>$new_keep_id['id']
                                    ])
                                    ->execute();
            }
        }
        if($res || $res2){
            return true;
        }else{
            return false;
        }
    }
    //批量删除
    public function actionDelete_img_all()
    {
        if($_GET){
            $tel = $_GET['tel'];
            $img_id = $_GET['img_id'];
            $keep = $_GET['keep'];
        }else{
            return false;
        }
        $uid = account::find()->select('id')->where(['phone'=>$tel])->one();
        $keep_id = keep::find()->select('id')->where(['keep_name'=>$keep,'uid'=>$uid['id']])->one();
        for($i=0;$i<count($img_id);$i++){
            $res = keepimage::deleteAll(['imgid'=>$img_id[$i],'kid'=>$keep_id['id']]);
        }
        if($res){
           return true;
        }else{
            return false;
        }
    }
    //查询推荐收藏夹
    public function actionFindreckeep()
    {
        $keep = keep::find()->select('id,keep_name')->where(['status'=>1])->all();
        $keep = array_slice($keep,0,5);
        if($keep){
            for($i = 0; $i<count($keep);$i++){
                $res2[$i] = keepimage::find()->select('id,imgid')->where(['kid'=>$keep[$i]])->all();
                for ($k = 0; $k<count($res2[$i]);$k++){
                    $res3[$i][] = goods::find()->select('id,name,image,')->where(['id'=>$res2[$i][$k]['imgid']])->one();
                    $res[$i][] = [
                        'id' => $keep[$i]['id'],
                        'keep_name' => $keep[$i]['keep_name'],
//                        'kid' => $res2[$i][$k]['id'],
                        'imgid' => $res2[$i][$k]['imgid'],
                        'imgname' => $res3[$i][$k]['name'],
                        'image' => 'http://qiniu.zaoanart.com/'.$res3[$i][$k]['image'].'?imageView2/2/h/400'
                    ];
                }
            }
            for($v = 0; $v<count($keep); $v++){
                if(empty($res2[$v])){
                    $res[$v][] = [
                        'id' => $keep[$v]['id'],
                        'keep_name' => $keep[$v]['keep_name'],
                    ];
                }
            }
            return $res;
        }
    }
    //查询所有收藏夹
    public function actionFindkeepall()
    {
        $result = keep::find()->select('id,keep_name')->where(['topping'=>1])->limit(6)->all();
        for($i=0;$i<count($result);$i++){
            $top_keep[] = [
                'id' => $result[$i]['id'],
                'keep_name' => $result[$i]['keep_name']
            ];
        }
        $result2 = keep::find()->select('id,keep_name')->where(['<>','topping',1])->orderBy(['heat' => SORT_ASC,])->all();
        for($k=0;$k<count($result2);$k++){
            $top_keep2[] = [
                'id' => $result2[$k]['id'],
                'keep_name' => $result2[$k]['keep_name']
            ];
        }
        $keep = array_values(array_merge($top_keep,$top_keep2));
        if($keep){
            for($i = 0; $i<count($keep);$i++){
                $res2[$i] = keepimage::find()->select('id,imgid')->where(['kid'=>$keep[$i]])->all();
                for ($k = 0; $k<count($res2[$i]);$k++){
                    $res3[$i][] = goods::find()->select('id,name,image')->where(['id'=>$res2[$i][$k]['imgid']])->one();
                    $res[$i][] = [
                        'id' => $keep[$i]['id'],
                        'keep_name' => $keep[$i]['keep_name'],
//                        'kid' => $res2[$i][$k]['id'],
                        'imgid' => $res2[$i][$k]['imgid'],
                        'imgname' => $res3[$i][$k]['name'],
                        'image' => 'http://qiniu.zaoanart.com/'.$res3[$i][$k]['image'].'?imageView2/2/h/400'
                    ];
                }
            }
            for($v = 0; $v<count($keep); $v++){
                if(empty($res2[$v])){
                    $res[$v][] = [
                        'id' => $keep[$v]['id'],
                        'keep_name' => $keep[$v]['keep_name'],
                    ];
                }
            }
            return $res;
        }
    }
    //查询收藏夹
    public function actionFindkeepname()
    {
        if($_GET){
            $tel = $_GET['tel'];
        }else{
            return false;
        }
        $uid = account::find()->select('id')->where(['phone'=>$tel])->one();
        $res = keep::find()->select('id,keep_name')->where(['uid'=>$uid['id']])->orderBy(['heat' => SORT_DESC])->limit(5)->all();
        for($k=0;$k<count($res);$k++){
            $keep[] = [
                'id' => $res[$k]['id'],
                'keep_name' => $res[$k]['keep_name']
            ];
        }
        return $keep;
    }
    //查询购物车数据
    public function actionFind_car()
    {
        $tel = $_GET['tel'];
        $uid = account::find()->select('id')->where(['phone'=>$tel])->one();
        $res = Shopcar::find()->where(['user_id'=>$uid['id']])->all();
        $path = Yii::getAlias('@backend').'\web\preview_img\\';
        for($i=0;$i<count($res);$i++){
            $img_info = filesize($path.$res[$i]['img_name']);
            $fp = fopen($path.$res[$i]['img_name'], "r");
            $content = fread($fp,$img_info);
            $img_str = chunk_split(base64_encode($content));
            $img_base64 = 'data:image/png;base64,'.$img_str;
            $info[] = [
                'id' => $res[$i]['id'],
                'goods_id' => $res[$i]['goods_id'],
                'user_id' => $res[$i]['user_id'],
                'box_name' => $res[$i]['box_name'],
                'excel_name' => $res[$i]['excel_name'],
                'color' => $res[$i]['color'],
                'img_name' => $res[$i]['img_name'],
                'img_width' => $res[$i]['img_width'],
                'img_height' => $res[$i]['img_height'],
                'box_width' => $res[$i]['box_width'],
                'box_height' => $res[$i]['box_height'],
                'decoration_status' => $res[$i]['decoration_status'],
                'core_material' => $res[$i]['core_material'],
                'drawing_core_val' => $res[$i]['drawing_core_val'],
                'core_offset' => $res[$i]['core_offset'],
                'core_offset_direction' => $res[$i]['core_offset_direction'],
                'core_shift_val' => $res[$i]['core_shift_val'],
                'core_shift_direction' => $res[$i]['core_shift_direction'],
                'core_price' => $res[$i]['core_price'],
                'decoration_price' => $res[$i]['decoration_price'],
                'total_price' => $res[$i]['total_price'],
                'status' => $res[$i]['status'],
                'img_url'=> $img_base64
            ];
        }
        return $info;
    }
    //生成表格
    public function actionTo_excel()
    {
        $excel_name = Shopcar::find()
            ->select('excel_name')
            ->where(['user_id'=>$_POST['data']['user_id']])
            ->andwhere(['id'=>$_POST['data']['id']])
            ->andwhere(['goods_id'=>$_POST['data']['goods_id']])
            ->one();
//        var_dump($excel_name);die;
        if($excel_name['excel_name']){
            return 1;//订单已经生成
        }else{
            require_once dirname(dirname(__FILE__)).'/phpexcel/Classes/PHPExcel.php';
            require_once dirname(dirname(__FILE__)).'/phpexcel/Classes/PHPExcel/IOFactory.php';
            require_once dirname(dirname(__FILE__)).'/phpexcel/Classes/PHPExcel/Reader/Excel5.php';
            require_once dirname(dirname(__FILE__)).'/phpexcel/Classes/PHPExcel/Reader/Excel2007.php';
            $path = Yii::getAlias('@backend').'\web\preview_img\\';
            $path2 = Yii::getAlias('@backend').'\web\excel\\';
//            $path2 = 'http://www.zaoanart.com:8000/excel/';
            $objPHPExcel = new \PHPExcel();
            //设置宽度
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(7);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getRowDimension('3')->setRowHeight(250);
            //字体
            $styleArray = array(
                'font' => array(
//                'bold' => false,
                    'size'=>12,
//                'color'=>array(
//                    'argb' => '00000000',
//                ),
                ),
                'alignment' => array(
                    'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
            );
            //设置字体
            $objPHPExcel->getActiveSheet()->getStyle('A3')->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('D3')->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('E3')->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('F3')->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle("2")->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('2')->getAlignment()->sethorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            //自动换行
            $objPHPExcel->getActiveSheet()->getStyle("3")->getAlignment()->setWrapText(TRUE);
            //设置表头
            $objPHPExcel->setActiveSheetIndex()->setCellValue('A2', 'ID');
            $objPHPExcel->setActiveSheetIndex()->setCellValue('B2', '图片');
            $objPHPExcel->setActiveSheetIndex()->setCellValue('C2', '商品信息');
            $objPHPExcel->setActiveSheetIndex()->setCellValue('D2', '单价');
            $objPHPExcel->setActiveSheetIndex()->setCellValue('E2', '数量');
            $objPHPExcel->setActiveSheetIndex()->setCellValue('F2', '总价');
            $objPHPExcel->setActiveSheetIndex()->setCellValue('G2', '备注');
            $data = $_POST['data'];
            $a = "边框:".$data['box_name'];
            $b = "\n卡纸颜色:".$data['color'];
            $c = "\n装裱方式:".$data['decoration_status'];
            $d = "\n画芯材质:".$data['core_material'];
            $e = "\n画芯尺寸:".$data['img_width'].'*'.$data['img_height'].'cm';
            $f = "\n画框尺寸:".$data['box_width'].'*'.$data['box_height'].'cm';
            $data['drawing_core_val'] == 0? $g = '':$g = "\n画芯留边值:".$data['drawing_core_val'].'cm';
            $i = "\n画芯偏移方向:".$data['core_offset_direction'];
            $k = "\n留边偏移方向:".$data['core_shift_direction'];
            $data['core_offset'] == 0? $h = '':$h = "\n画芯偏移值:".$data['core_offset']."cm".$i;
            $data['core_shift_val'] == 0? $j = '': $j = "\n留边偏移值:".$data['core_shift_val'].$k;
            $l = "\n画芯价格:".$data['core_price'].'元';
            $m = "\n装裱价格:".$data['decoration_price'].'元';
            $str = $a.$b.$c.$d.$e.$f.$g.$h.$j.$l.$m;
            //数量
            $num = 1;
            //设置数据
            $objPHPExcel->getActiveSheet()->setCellValue('A3' ,$data['id']);
            $objPHPExcel->getActiveSheet()->setCellValue('C3' ,$str);
            $objPHPExcel->getActiveSheet()->setCellValue('D3' ,$data['total_price']);
            $objPHPExcel->getActiveSheet()->setCellValue('E3' ,$num);
            $objPHPExcel->getActiveSheet()->setCellValue('F3' ,$data['total_price']*$num);
            $objPHPExcel->getActiveSheet()->getStyle('C3')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
            //添加边框
            $styleThinBlackBorderOutline = array(
                'borders' => array(
                    'allborders' => array( //设置全部边框
                        'style' => \PHPExcel_Style_Border::BORDER_THIN //粗的是thick
                    ),
                ),
            );
            $objPHPExcel->getActiveSheet()->getStyle( 'A2:G3')->applyFromArray($styleThinBlackBorderOutline);
            $objPHPExcel->getActiveSheet()->mergeCells('B4:E4');
            $objPHPExcel->getActiveSheet()->mergeCells('B5:E5');
            $objPHPExcel->getActiveSheet()->mergeCells('B6:E6');
            $objPHPExcel->getActiveSheet()->mergeCells('B7:E7');
            $objPHPExcel->getActiveSheet()->mergeCells('B8:E8');
            $objPHPExcel->getActiveSheet()->mergeCells('B9:E9');
            $objPHPExcel->getActiveSheet()->mergeCells('B10:E10');
            $objPHPExcel->getActiveSheet()->mergeCells('A12:G12');
            $objPHPExcel->getActiveSheet()->mergeCells('A1:G1');
            $objPHPExcel->getActiveSheet()->setCellValue('A1' ,'ZaoAn Art--订单');
            $objPHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setSize(18);
            $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->sethorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->setCellValue('B4' ,'ZaoAn Art 早安艺术');
            $objPHPExcel->getActiveSheet()->getStyle("B4")->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle("B4")->getFont()->setSize(16);
            $objPHPExcel->getActiveSheet()->setCellValue('B5' ,'公司名称 : 上海早艾电子商务有限公司');
            $objPHPExcel->getActiveSheet()->setCellValue('B6' ,'开户行 : 招商银行长乐路支行');
            $objPHPExcel->getActiveSheet()->setCellValue('B7' ,'银行账号 : 121923721210801');
            $objPHPExcel->getActiveSheet()->setCellValue('B8' ,'信用代码 : 91310120MA1HLJC584');
            $objPHPExcel->getActiveSheet()->setCellValue('B9' ,'公司地址 : 上海市静安区巨鹿路861号2楼');
            $objPHPExcel->getActiveSheet()->setCellValue('B10' ,'公司电话 : 021-64032911  客服QQ : 2945296672');
            $objPHPExcel->getActiveSheet()->setCellValue('A12' ,'报价说明: 以上报价为含税价(3%增值税专用发票)');
            $objPHPExcel->getActiveSheet()->getStyle("A12")->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle("A12")->getFont()->setSize(14);
            $objPHPExcel->getActiveSheet()->getStyle('A12')->getAlignment()->sethorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            //图片
            $objDrawing = new \PHPExcel_Worksheet_Drawing();
            $objDrawing->setName('Logo');
            $objDrawing->setDescription('Logo');
            //图片名
            $objDrawing->setPath($path.$data['img_name']);
            $objDrawing->setWidth(80);
            $objDrawing->setHeight(80);
            //图片所在位置
            $objDrawing->setCoordinates ( 'B3' );
            $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
            $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
            ob_end_clean();
            ob_start();
            $time = date('YmdHis');
            $filename = iconv('UTF-8','GB2312','订单').$time.'.xlsx';
            $objWriter= \PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
            $objWriter->save($path2.$filename);
            Shopcar::updateAll(['excel_name'=>iconv('GB2312','UTF-8',$filename)],['id'=>$_POST['data']['id']]);
            return iconv('GB2312','UTF-8',$filename);
        }
    }
    //记录网站访问量
    public function actionRecord_access()
    {
        var_dump(123);die;
        $sql = 'select `id` from `tsy_access`where TO_DAYS(`created_at`) = TO_DAYS(NOW())';
        $up_goods_search_sum = 'UPDATE `tsy_access` SET `access_sum`=`access_sum`+1';
        Yii::$app->db->createCommand($up_goods_search_sum)->execute();
    }
    //查询轮播图
    public function actionFindlunbotu()
    {
        $res = Ad::find()->select('img_name')->where(['is_appear'=>1])->all();
        for($i=0;$i<count($res);$i++){
            $info[$i] = $res[$i]['img_name'];
        }
        return $info;
    }
    //获取用户名头像,关注收藏夹,关注的人
    public function actionGetusername()
    {
        $tel = $_GET['tel'];
        $username = account::find()->select('id,username,icon')->where(['phone'=>$tel])->one();
        $my_attention = attention::find()->select('id,kid,uid')->where(['uid'=>$username['id']])->count();
        $attention_user = attentionUser::find()->select('id')->where(['uid'=>$username['id']])->count();
        $info = [
            'username'=>$username['username'],
//            'icon'=>'http://qiniu.zaoanart.com/'.$username['icon'],
            'icon'=>'http://118.178.89.229/resource/userIcon/'.$username['icon'],
            'my_attention'=>$my_attention,
            'attention_user_num'=>$attention_user,
            'uid'=>$username['id']
        ];
        return $info;
    }
    //获取用户名头像,关注收藏夹,关注的人
    public function actionGetusername1()
    {
        $tel = $_GET['tel'];
        $uid = $_GET['uid'];
        $user = account::find()->select('id')->where(['phone'=>$tel])->one();
        $user_id = $user['id'];
        $username = account::find()->select('id,username,icon')->where(['id'=>$uid])->one();
        $my_attention = attention::find()->select('id,kid,uid')->where(['uid'=>$username['id']])->count();
        $attention_user = attentionUser::find()->select('id')->where(['uid'=>$username['id']])->count();
        $is_attention = attentionUser::find()->select('id')->where(['attention_uid'=>$username['id'],'uid'=>$user_id])->all();
        if($is_attention){
            $is_attention_user = 1;//已经关注
        }else{
            $is_attention_user = 2;//未关注
        }
        $info = [
            'username'=>$username['username'],
//            'icon'=>'http://qiniu.zaoanart.com/'.$username['icon'],
            'icon'=>'http://118.178.89.229/resource/userIcon/'.$username['icon'],
            'my_attention'=>$my_attention,
            'attention_user_num'=>$attention_user,
            'uid'=>$username['id'],
            'is_attention_user'=>$is_attention_user
        ];
        return $info;
    }
    //获取我关注的收藏夹
    public function actionFindkeep2()
    {
        if(!$_GET){
            return false;
        }
        $user = account::find()->select('id')->where(['phone'=>$_GET['tel']])->one();
        $uid = $user['id'];
//        $uid = intval($_GET['uid']);
        $attention_keep = attention::find()->select('id,kid,uid')->where(['uid'=>$uid])->all();
        $attention_num = count($attention_keep);
        if($attention_keep) {
            for ($i = 0; $i < count($attention_keep); $i++) {
                $res2 = keepimage::find()->select('id,imgid')->where(['kid' => $attention_keep[$i]['kid']])->limit(4)->all();
                $keep = keep::find()->select('id,keep_name,uid,img_ratio,heat')->where(['id' => $attention_keep[$i]['kid']])->one();
                $attention_count = attention::find()->select('id')->where(['kid' => $keep['id']])->count();
                if (count($res2) <= 0) {
                    $res[$i][] = [
                        'keep_id' => $attention_keep[$i]['kid'],
                        'keep_name' => $keep['keep_name'],
                        'uid' => $keep['uid'],
                        'img_ratio' => '1',
                        'attention_num' => $attention_num,
                        'attention_count' => $attention_count,
                        'heat' => $keep['heat'],
                        'imgid' => '',
                        'is_attention' => 2,
                        'image' => ''
                    ];
                } else {
                    for ($k = 0; $k < count($res2); $k++) {
                        $res3[$i][] = goods::find()->select('id,name,image')->where(['id' => $res2[$k]['imgid']])->one();
                        if ($res2[$k]['imgid']) {
                            $res[$i][] = [
                                'keep_id' => $attention_keep[$i]['kid'],
                                'keep_name' => $keep['keep_name'],
                                'img_ratio' => $keep['img_ratio'],
                                'uid' => $keep['uid'],
                                'attention_num' => $attention_num,
                                'attention_count' => $attention_count,
                                'heat' => $keep['heat'],
                                'imgid' => $res2[$k]['imgid'],
                                'is_attention' => 2,
                                'image' => 'http://qiniu.zaoanart.com/' . $res3[$i][$k]['image']
                            ];
                        }
                    }
                }
            }
            sort($res);
            return $res;
        }
    }

    //获取我关注的人
    public function actionFindattentionuser()
    {
        $tel = $_GET['tel'];
        $user = account::find()->select('id')->where(['phone'=>$tel])->one();
        $uid = $user['id'];
        $res = attentionUser::find()->select('id,attention_uid')->where(['uid'=>$uid])->all();
        $user_info = [];
        for($i=0;$i<count($res);$i++){
            $res2 = account::find()->select('id,username,icon')->where(['id'=>$res[$i]['attention_uid']])->one();
            $attention_keep = attention::find()->select('id')->where(['uid'=>$res2['id']])->count();
            $attention_user = attentionUser::find()->select('id')->where(['uid'=>$res2['id']])->count();
            $user_info[] = [
                'uid'=>$res2['id'],
                'username'=>$res2['username'],
                'attention_keep'=>$attention_keep,
                'attention_user'=>$attention_user,
//                'image'=>'http://qiniu.zaoanart.com/'.$res2['icon'],
                'image'=>'http://118.178.89.229/resource/userIcon/'.$res2['icon'],
            ];
        }
        return $user_info;
    }

    //查询用户收藏夹
    public function actionFinduserkeep()
    {
        if($_GET){
            $tel = $_GET['tel'];
            $uid = $_GET['uid'];
        }else{
            return false;
        }
        $current_uid = $_GET['uid'];
        $attention_keep = [];
        $username = account::find()->select('id,username')->where(['id'=>$current_uid])->one();
        $keep = keep::find()->select('id,keep_name,uid,img_ratio')->where(['uid'=>$current_uid])->all();
        $user = account::find()->select('id')->where(['phone'=>$tel])->one();
        $attention_keep = attention::find()->select('id,kid')->where(['uid'=>$user['id']])->all();
        if($keep){
            for($i = 0; $i<count($keep);$i++){
                $attention_num = attention::find()->select('id')->where(['kid'=>$keep[$i]['id']])->count();
                $res2[$i] = keepimage::find()->select('id,imgid')->where(['kid'=>$keep[$i]['id']])->limit(4)->orderBy('created_at')->all();
                for ($k = 0; $k<count($res2[$i]);$k++){
                    $res3[$i][] = goods::find()->select('id,name,image')->where(['id'=>$res2[$i][$k]['imgid']])->one();
                    if($res2[$i][$k]['imgid']){
                        $res[$i][] = [
                            'keep_id' => $keep[$i]['id'],
                            'uid' => $keep[$i]['uid'],
                            'username' => $username['username'],
                            'keep_name' => $keep[$i]['keep_name'],
                            'img_ratio' => $keep[$i]['img_ratio'],
                            'imgid' => $res2[$i][$k]['imgid'],
                            'attention_num' => $attention_num,
                            'is_attention' => 1,//没有关注
                            'image' => 'http://qiniu.zaoanart.com/'.$res3[$i][$k]['image']
                        ];
                    }
                }
            }
            if($attention_keep){
                for($z=0;$z<count($attention_keep);$z++){
                    for($x=0;$x<count($res);$x++){
                        for($c=0;$c<count($res[$x]);$c++){
                            if($attention_keep[$z]['kid'] == (int)$res[$x][$c]['keep_id']){
                                $res[$x][$c]['is_attention'] = 2;
                            }
                        }
                    }
                }
            }
            sort($res);
            return $res;
        }
    }

    //获取用户关注的收藏夹
    public function actionUserattenkeep()
    {
        if($_GET){
            $tel = $_GET['tel'];
            $uid = $_GET['uid'];
        }else{
            return false;
        }
        $user = account::find()->select('id')->where(['phone'=>$_GET['tel']])->one();
        $user_id = $user['id'];
        $current_uid = $_GET['uid'];
        $attention_keep = [];
        $username = account::find()->select('username')->where(['id'=>$current_uid])->one();
        $attention_keep = attention::find()->select('id,kid,uid')->where(['uid'=>$uid])->all();
        for($v=0;$v<count($attention_keep);$v++){
            $keep[] = keep::find()->select('id,keep_name,uid,img_ratio')->where(['id'=>$attention_keep[$v]['kid']])->one();
        }
        if($keep){
            for($i = 0; $i<count($keep);$i++){
                $attention_num = attention::find()->select('id')->where(['kid'=>$keep[$i]['id']])->count();
                $res2[$i] = keepimage::find()->select('id,imgid')->where(['kid'=>$keep[$i]['id']])->limit(4)->orderBy('created_at')->all();
                for ($k = 0; $k<count($res2[$i]);$k++){
                    $res3[$i][] = goods::find()->select('id,name,image')->where(['id'=>$res2[$i][$k]['imgid']])->one();
                    if($res2[$i][$k]['imgid']){
                        $res[$i][] = [
                            'keep_id' => $keep[$i]['id'],
                            'uid' => $keep[$i]['uid'],
                            'username' => $username['username'],
                            'keep_name' => $keep[$i]['keep_name'],
                            'img_ratio' => $keep[$i]['img_ratio'],
                            'imgid' => $res2[$i][$k]['imgid'],
                            'attention_num' => $attention_num,
                            'is_attention' => 1,//没有关注
                            'image' => 'http://qiniu.zaoanart.com/'.$res3[$i][$k]['image']
                        ];
                    }
                }
            }
            sort($res);
            $attention_keep = attention::find()->select('id,kid,uid')->where(['uid'=>$user_id])->all();
            if($attention_keep){
                for($z=0;$z<count($attention_keep);$z++){
                    for($x=0;$x<count($res);$x++){
                        for($c=0;$c<count($res[$x]);$c++){
                            if($attention_keep[$z]['kid'] == (int)$res[$x][$c]['keep_id']){
                                $res[$x][$c]['is_attention'] = 2;
                            }
                        }
                    }
                }
            }
            sort($res);
            return $res;
        }
    }

    //获取用户关注的人
    public function actionUserattentionuser()
    {
        $uid = (int)$_GET['uid'];
        $tel = $_GET['tel'];
        $user = account::find()->select('id')->where(['phone'=>$tel])->one();
        $user_id = $user['id'];
        $res = attentionUser::find()->select('id,attention_uid')->where(['uid'=>$uid])->all();
        $user_info = [];
        for($i=0;$i<count($res);$i++){
            $res2 = account::find()->select('id,username,icon')->where(['id'=>$res[$i]['attention_uid']])->one();
            $attention_keep = attention::find()->select('id')->where(['uid'=>$res2['id']])->count();
            $attention_user = attentionUser::find()->select('id')->where(['uid'=>$res2['id']])->count();
            $is_attention = attentionUser::find()->select('id')->where(['uid'=>$user_id,'attention_uid'=>$res2['id']])->all();
            if($is_attention){
                $is_attentions = true;
            }else{
                $is_attentions = false;
            }
            $user_info[] = [
                'user_id'=>$uid,
                'uid'=>$res2['id'],
                'username'=>$res2['username'],
                'attention_keep'=>$attention_keep,
                'attention_user'=>$attention_user,
                'is_attention'=>$is_attentions,
//                'image'=>'http://qiniu.zaoanart.com/'.$res2['icon'],
                'image'=>'http://www.zaoanart.com:8000/userIcon/'.$res2['icon'],
            ];
        }
        return $user_info;
    }

    //取消关注收藏夹
    public function actionDel_attention_keep()
    {
        $keep_id = $_GET['keep_id'];
        $tel = $_GET['tel'];
        $user = account::find()->select('id')->where(['phone'=>$tel])->one();
        $uid = $user['id'];
        if($uid && $keep_id){
            $res = attention::find()->select('id')->where(['uid'=>$uid,'kid'=>$keep_id])->all();
            if(!$res){
                return 3;//该收藏夹没有关注
            }else{
                $res2 = attention::deleteAll(['uid'=>$uid,'kid'=>$keep_id]);
                if($res2){
                    return 1;//取消关注成功
                }else{
                    return 2;//取消关注失败
                }
            }
        }
    }

    //取消关注的人
    public function actionDel_attention_user()
    {
        $tel = $_GET['tel'];
        $user = account::find()->select('id')->where(['phone'=>$tel])->one();
        $uid = $user['id'];
        $attention_uid = $_GET['attention_uid'];
        if($uid && $attention_uid){
            $res = attentionUser::find()->select('id')->where(['uid'=>$uid,'attention_uid'=>$attention_uid])->all();
            if(!$res){
                return 3;//该收藏夹没有关注
            }else{
                $res2 = attentionUser::deleteAll(['uid'=>$uid,'attention_uid'=>$attention_uid]);
                if($res2){
                    return 1;//取消关注成功
                }else{
                    return 2;//取消关注失败
                }
            }
        }
    }

    //关注收藏夹
    public function actionAdd_attention_keep()
    {
        $tel = $_GET['tel'];
        $user = account::find()->select('id')->where(['phone'=>$tel])->one();
        $uid = $user['id'];
        $keep_id = intval($_GET['keep_id']);
        if($uid && $keep_id){
            $res = attention::find()->select('id')->where(['uid'=>$uid,'kid'=>$keep_id])->all();
            if($res){
                return 3;//该收藏夹已经添加
            }else{
                $res2 = Yii::$app->db->createCommand()
                    ->insert('tsy_attention',[
                        'uid' => $uid,
                        'kid' => $keep_id,
                        'created_at' => date("Y-m-d H:i:s"),
                    ])
                    ->execute();
                if($res2){
                    return 1;//添加关注成功
                }else{
                    return 2;//添加关注失败
                }
            }
        }
    }

    //关注用户
    public function actionAdd_attention_user()
    {
        $tel = $_GET['tel'];
        $user = account::find()->select('id')->where(['phone'=>$tel])->one();
        $uid = $user['id'];
        $attention_uid = $_GET['attention_uid'];
        if($uid && $attention_uid){
            $res = attentionUser::find()->select('id')->where(['uid'=>$uid,'attention_uid'=>$attention_uid])->all();
            if($res){
                return 3;//该用户已经关注
            }else{
                $res2 = Yii::$app->db->createCommand()
                    ->insert('tsy_attention_user',[
                        'uid' => $uid,
                        'attention_uid' => $attention_uid,
                        'created_at' => date("Y-m-d H:i:s"),
                    ])
                    ->execute();
                if($res2){
                    return 1;//添加关注成功
                }else{
                    return 2;//添加关注失败
                }
            }
        }
    }
}
?>