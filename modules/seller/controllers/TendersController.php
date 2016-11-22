<?php

namespace app\modules\seller\controllers;

use Yii;
use app\modules\seller\models\Tenders;
use app\models\TendersSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
/**
 * TendersController implements the CRUD actions for Tenders model.
 */
class TendersController extends Controller
{


    /**
     * Lists all Tenders models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TendersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    protected function findModel($id)
    {
        if (($model = Tenders::findOne(['id'=>$id,'company_id'=>Yii::$app->user->identity->company_id])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
