<?php

namespace app\controllers;

use Yii;
use app\models\Payment;
use yii\web\Controller;
use yii\filters\VerbFilter;


/**
 * PaymentController implements the CRUD actions for Payment model.
 */
class PaymentController extends Controller
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
                    'callback' => ['POST'],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        if ($action->id == 'callback') {
            $this->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }

    public function actionCallback(){
        $paymentModel = new Payment();
        $paymentModel->execute();
        if (is_object($paymentModel->invoiceModel)) {
            $paymentModel->createPaymentByInvoice();
        }
        else{
            $paymentModel->createPaymentWithoutInvoice();
        }
    }



    public function actionView($id)
    {
        $payment = new Payment();
        return $this->render('view', [
            'model' => $payment->find($id),
        ]);
    }



    public function find($id)
    {
        if (($model = $this::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
