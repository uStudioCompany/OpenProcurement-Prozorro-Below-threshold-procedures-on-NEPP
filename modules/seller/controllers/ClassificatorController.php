<?php

namespace app\modules\seller\controllers;

use Yii;
use yii\web\Controller;
use app\models\Cpv;
use app\models\Dkpp;
use app\models\Dk015;
use app\models\Dk018;
use app\models\Dk003;
use app\models\Kekv;

class ClassificatorController extends Controller
{

    public function actionCpv()
    {
        $params = Yii::$app->request->queryParams;

        $model = new Cpv;

        return $this->renderAjax('Classificator', ['items' => $model->search($params)]);
    }

    public function actionDkpp()//ДК 016:2010
    {
        $params = Yii::$app->request->queryParams;

        $model = new Dkpp;

        return $this->renderAjax('Classificator', ['items' => $model->search($params)]);
    }

    public function actionDk015()//ДК 016:2010
    {
        $params = Yii::$app->request->queryParams;

        $model = new Dk015;

        return $this->renderAjax('Classificator', ['items' => $model->search($params)]);
    }

    public function actionDk018()//ДК 018:2000
    {
        $params = Yii::$app->request->queryParams;

        $model = new Dk018;

        return $this->renderAjax('Classificator', ['items' => $model->search($params)]);
    }

    public function actionDk003()//ДК 003
    {
        $params = Yii::$app->request->queryParams;

        $model = new Dk003;

        return $this->renderAjax('Classificator', ['items' => $model->search($params)]);
    }

    public function actionKekv()
    {
        $params = Yii::$app->request->queryParams;

        $model = new Kekv;

        return $this->renderAjax('Classificator', ['items' => $model->search($params)]);
    }

    public function actionMapping()
    {
        return true;
    }

}
