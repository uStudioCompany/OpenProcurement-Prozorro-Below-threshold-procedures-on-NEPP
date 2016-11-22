<?php

namespace app\modules\backend\controllers;

use yii\web\Controller;

class DefaultController extends BackendController
{
    public function actionIndex()
    {
        return $this->render('index');
    }
}
