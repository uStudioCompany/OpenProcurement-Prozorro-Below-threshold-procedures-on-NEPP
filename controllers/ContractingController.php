<?php

namespace app\controllers;

use Yii;
use app\models\ContractingSearch;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Contracting;

/**
 * ContractingController implements the CRUD actions for Contracting model.
 */
class ContractingController extends Controller
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
     * Lists all Contracting models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ContractingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Contracting model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $contracts = Contracting::getModel($id);
        $data['Contract'] = Json::decode($contracts->response)['data'];

        return $this->render('view', [
            'contract' => \app\components\HContract::update($data),
            'contracts' => $contracts
        ]);
    }


    /**
     * Finds the Contracting model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Contracting the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Contracting::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
