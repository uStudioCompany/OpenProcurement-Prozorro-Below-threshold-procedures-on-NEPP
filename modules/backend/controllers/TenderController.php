<?php

namespace app\modules\backend\controllers;

use yii\web\Controller;

class TenderController extends BackendController
{
    public function actionIndex()
    {
        //die('zzzzzzzzzz');
        return $this->render('index');
    }
}