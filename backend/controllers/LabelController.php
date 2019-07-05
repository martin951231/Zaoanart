<?php

namespace backend\controllers;

use Yii;
use backend\models\Label;
use backend\models\Goods;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * LabelController implements the CRUD actions for Label model.
 */
class LabelController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Label models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Label::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Label model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Label model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Label();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Label model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Label model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Label model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Label the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Label::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    //查询所有标签
    public function actionSelect_label(){
        $res = label::find()->select('id,label_name')->all();
        for($i=0;$i<count($res);$i++){
            $info[] = [
                'id' => $res[$i]['id'],
                'label_name' => $res[$i]['label_name'],
            ];
        }
        return json_encode($info);
    }
    //添加常用标签
    public function actionAdd_label(){
        $label = $_POST['label'];
        $labels = label::find()->select('id')->where(['label_name'=>$label])->one();
        if($labels['id']){
            return "该标签已经存在,请重新定义";
        }
        $res = Yii::$app->db->createCommand()
            ->insert('tsy_label',[
                'label_name' => $label,
            ])
            ->execute();
        $result_id = Yii::$app->db->getLastInsertID();
        if($res){
            $info[] = [
                'id' => $result_id,
                'label_name' => $label
            ];
            return json_encode($info);
        }else{
            return false;
        }
    }
    //删除常用标签
    public function actionDelete_labelist(){
        $label_id = $_POST['label_id'];
        if($label_id){
            $res = label::deleteAll(['id'=>$label_id]);
        }
        if($res){
            $info[] = [
                'del_labelid' => $label_id,
            ];
            return json_encode($info);
        }else{
            return "删除失败";
        }
    }
    //批量上传修改标签
    public function actionAdd_label_all()
    {
        $id = $_POST['id'];
        $label_id = $_POST['label_id'];
        for($i=0;$i<count($id);$i++){
            $info = goods::find()->select('label')->where(['id'=>$id[$i]])->one();
            $label = explode(",",trim($info['label'].','.$label_id,','));
            sort($label);
            $labels = ','.implode(",",array_unique($label)).',';
            $res = goods::updateAll(['label' => $labels],['id'=>$id[$i]]);
        }
        if($res){
            return 1;
        }
    }

    //查询指定id的所有标签
    public function actionSelect_label2(){
        $ids = $_POST['id'];
        $lable_str = '';
        for($i=0;$i<count($ids);$i++){
            $res = goods::find()->select('label')->where(['id'=>$ids[$i]])->one();
            $lable_str .= ','.trim($res['label'],',');

        }
        $label_arrs = array_filter(explode(',',$lable_str));
        $label_arr = array_unique($label_arrs);
        sort($label_arr);
        for($k=0;$k<count($label_arr);$k++){
            $res2[] = label::find()->select('id,label_name')->where(['id'=>$label_arr[$k]])->one();
        }
        for($q=0;$q<count($res2);$q++){
            $info[] = [
                'id' => $res2[$q]['id'],
                'label_name' => $res2[$q]['label_name'],
            ];
        }
        return json_encode($info);
    }

    //批量删除标签
    public function actionDelete_label_all()
    {
        $label_id = $_GET['label_id'];
        $ids = $_GET['id'];
        $res = $res2 = null;
        for($i=0;$i<count($ids);$i++){
            $res = goods::find()->select('label')->where(['id'=>$ids[$i]])->one();
            $label = array_filter(explode(",",$res['label']));
            sort($label);
            $is_exists = in_array($label_id,$label);
            if($is_exists){
                $key = array_search($label_id,$label);
                unset($label[$key]);
                sort($label);
                $labels = ','.implode(",",array_unique($label)).',';
                $res2 += goods::updateAll(['label' => $labels],['id'=>$ids[$i]]);
            }
        }
        if($res2>1){
            return 1;
        }else{
            return 2;
        }
    }
}
