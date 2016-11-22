<?php

namespace app\controllers;

use Yii;
use app\models\viewerModels\Tenders;
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

    /**
     * Lists archive(completed, canceled, unsuccessful) Tenders models.
     * @return mixed
     */
    public function actionArchive()
    {
        $searchModel = new TendersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists actual(active.tendering, active.enquiries) Tenders models.
     * @return mixed
     */
    public function actionActual()
    {
        $searchModel = new TendersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists current(active.pre-qualification, active.auction, active.qualification, active.awarded, active.pre-qualification.stand-still) Tenders models.
     * @return mixed
     */
    public function actionCurrent()
    {
        $searchModel = new TendersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Finds the Tenders model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Tenders the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Tenders::findOne(['id'=>$id])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
