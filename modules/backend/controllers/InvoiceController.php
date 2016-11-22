<?php

namespace app\modules\backend\controllers;

use Yii;
use app\models\Invoice;
use app\models\InvoiceSearch;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

define( 'STATUS_NOT_VERIFIED', 'rejected' );
define( 'STATUS_VERIFIED', 'accepted' );
define( 'STATUS_DOUBLE', 'double' );

/**
 * PaymentController implements the CRUD actions for Payment model.
 */
class InvoiceController extends BackendController
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
        $searchModel = new InvoiceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionPayInvoice()
    {
        if (Yii::$app->request->method == 'POST'){
            $post = Yii::$app->request->post();
            $invoice = Invoice::find()->where(['id' => $post['invoiceId']])->with('companies')->one();
            $dataJson = json_decode(file_get_contents(__DIR__ . '/../views/invoice/_paymentData.json'));
            $dataJson->payer->EDRPOU = $invoice['companies']['identifier'];
            $dataJson->destination = $invoice['destination'];
            $dataJson->amount = $invoice['amount'];
            $dataJson->technicalData->Fact->info->{'@postdate'} = date('c');
            $dataJson = json_encode($dataJson);
            $ch = curl_init(Url::base(true). '/payment/callback');
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataJson);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($dataJson))
            );

            $data = curl_exec($ch);
//            if(curl_error($ch))
//            {
//                echo 'error:' . curl_error($ch);die;
//            }
//            print_r($data);die;
            if ($data == 'OK'){
                $invoice->payer = Yii::$app->user->identity->id;
                $invoice->save(false);
            }
            curl_close($ch);
        }
        return isset(Yii::$app->request->post()['url']) ? $this->redirect(Yii::$app->request->post()['url']) : $this->redirect(['index']);
    }

    /**
     * Finds the Payment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Invoice the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Invoice::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}