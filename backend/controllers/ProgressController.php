<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * MetController implements the CRUD actions for Met model.
 */
class ProgressController extends Controller
{
    public function actionIndex()
    {
        $session = Yii::$app->session;
        $session->open();
            var_dump($session['__flash']);die;
        $i = ini_get('session.upload_progress.name');
        $key = ini_get("session.upload_progress.prefix") . $_GET[$i];
        if (!empty($_SESSION[$key])) {
            $current = $_SESSION[$key]["bytes_processed"];
            $total = $_SESSION[$key]["content_length"];
            return $current < $total ? ceil($current / $total * 100) : 100;
        }else{
            return 100;
        }
    }
}
