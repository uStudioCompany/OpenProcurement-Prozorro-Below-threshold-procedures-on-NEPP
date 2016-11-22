<?php

namespace app\modules\backend\controllers;

use Yii;
use app\models\Payment;
use app\models\Invoice;
use app\models\PaymentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

define( 'STATUS_NOT_VERIFIED', 'rejected' );
define( 'STATUS_VERIFIED', 'accepted' );
define( 'STATUS_DOUBLE', 'double' );

/**
 * PaymentController implements the CRUD actions for Payment model.
 */
class PaymentController extends BackendController
{
    public function beforeAction($action)
    {     
        if ($action->id == 'callback') {
            $this->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }

    /**
     * Lists all Payment models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PaymentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Finds the Payment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Payment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Payment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
