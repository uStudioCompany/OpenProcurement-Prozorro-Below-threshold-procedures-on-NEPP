<?php

namespace app\modules\seller\controllers;

use yii\web\Controller;

class DefaultController extends Controller
{
    public function actionIndex()
    {
        //die('zzzzzzzzzz');
        return $this->render('index');
    }
}
