<?php

namespace app\modules\backend\controllers;

use yii\web\Controller;

class BackendController extends Controller
{
    public $modelName = null;

    public $layout = '@app/modules/backend/views/layouts/main';

    public function actionIndex()
    {
        return $this->render('index');
    }
}
