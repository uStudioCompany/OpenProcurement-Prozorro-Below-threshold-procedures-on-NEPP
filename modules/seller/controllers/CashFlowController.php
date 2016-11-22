<?php

namespace app\modules\seller\controllers;

use app\components\SetFlashGoBack;
use app\models\Companies;
use app\models\Contracts;
use app\models\ContractsTemplates;
use app\models\Invoice;
use app\models\InvoiceSearch;
use app\models\User;
use Yii;
use app\models\CashFlow;
use app\models\CashFlowSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Dompdf\Dompdf;
use Dompdf\Options;
use yii\widgets\ActiveForm;

/**
 * CashFlowController implements the CRUD actions for CashFlow model.
 */
class CashFlowController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'denyCallback' => function ($rule, $action) {
                    Yii::$app->session->setFlash('Forbidden', Yii::t('app', "For Access to pages, Need a Authorization"));
                    $this->goBack();
                },
                //'only' => ['index', 'view-invoices'],
                'rules' => [
                    // deny all POST requests
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        //'actions' => ['index, view-invoices'],
                        'allow' => false,
                        'verbs' => ['POST, GET'],
                        'roles' => ['*'],
                    ],
                ],
            ],
            SetFlashGoBack::className(),
            'verbs' => [
                'class' => VerbFilter::className(),
            ],
        ];
    }

    public function beforeAction($action)
    {
        $get = Yii::$app->request->get();
        if (!$get['isBackground']) {
            if (Companies::getStatus(\Yii::$app->user->identity->company_id) != Companies::STATUS_ACCEPTED) {
                return $this->redirect(['/seller/cabinet']);
            }
        }
        return parent::beforeAction($action);
    }

    public function afterAction($action, $result)
    {
        if (Yii::$app->getUser()->getReturnUrl() !== Yii::$app->request->url) {
            Yii::$app->getUser()->setReturnUrl(Yii::$app->request->url);
        }
        return parent::afterAction($action, $result);
    }

    /**
     * Lists all CashFlow models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CashFlowSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all Invoice models.
     * @return mixed
     */
    public function actionViewInvoices()
    {
        $searchModel = new InvoiceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('@app/views/invoice/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Invoice model.
     * @param integer $id
     * @return mixed

    public function actionViewInvoice($id)
    {
        return $this->render('@app/views/invoice/view', [
            'model' => $this->findInvoiceModel($id),
        ]);
    }
     * */

    /**
     * Creates a new Invoice model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreateInvoice($isBackground = false, $createFinAuthInvoice = false)
    {
        $companyModel = $this->findCompanyModel(Yii::$app->user->identity->company_id);

        $companyModel->scenario = 'changeBankAccount';
        $contract = Contracts::findOne(['company_id' => $companyModel->id]);
        $post = Yii::$app->request->post();

        // если нет контракта, то создаем его и дату создания
        if (!$contract) {
            $template = ContractsTemplates::find()->orderBy('create_at DESC')->one();
            $contract = new Contracts();
            $contract->contract_num = $companyModel->identifier . date('dmYHis');
            $contract->template_id = $template->id;
            $contract->save(false);
        }
        if (isset($post['Companies'])) {
            if ($companyModel->load($post['Companies'])) {
                $companyModel->payer_pdv = $companyModel->isNeedCheckPayerVAT() ? $post['Companies']['payer_pdv'] : null;
                if (!$companyModel->save()) {
                    $model = new Invoice();
                    return $this->render('@app/views/invoice/create', [
                        'model' => $model,
                        'companyModel' => $companyModel
                    ]);
                }
            }
        }
        $model = NULL;
        if ($isBackground) {
            $model = Invoice::find()->where(['amount' => Invoice::FIN_AUTH_DEFAULT_AMOUNT])->andWhere(['balance_id' => $companyModel->id])->one();
        }
        if (!$model){
            $model = new Invoice();
        }
        $arrModels = [$model];
        if (!$companyModel->mfo || !$companyModel->bank_account || !$companyModel->bank_branch || ($companyModel->isNeedCheckPayerVAT() ? !isset($companyModel->payer_pdv) : false)) {
            $arrModels = [$model, $companyModel];
        } else {
            $companyModel = NULL;
        }
        $isload = true;
        if ($isBackground && !$companyModel) {
            $model->amount = $model::FIN_AUTH_DEFAULT_AMOUNT;
        }
        else {
            foreach ($arrModels as &$item) {
                $isload = (!$item->load($post)) ? false : true && $isload;
            }
        }

        if (Yii::$app->request->isAjax && $isload) {
            $resValidArr = [];
            foreach ($arrModels as &$item){
                $resValidArr = array_merge($resValidArr, ActiveForm::validate($item));
            }
            return json_encode($resValidArr);
        }
        if ($isload) {
            $isValid = true;
            if (!$isBackground || $companyModel) {
                foreach ($arrModels as $key => &$item) {
                    $isValid = (!$item->validate()) ? false : true && $isValid;
                }
            }
            if ($isValid || $isBackground) {
                foreach ($arrModels as &$item) {
                    $item->save(false);
                }
                if ($isBackground && !$companyModel){
                    return $this->redirect('/seller/cash-flow/shet-factura-pdf?id='.$model->id);
                }
                elseif(!$isBackground && $companyModel) {
                    return $this->redirect('/seller/cash-flow/shet-factura-pdf?id='.$model->id);
                }
                elseif(!$isBackground && !$companyModel) {
                    return $this->setFlashGoBack('Invoice created for viewing press button "Invoice"', 'success', 'view-invoices');
                }
                if ($isBackground && $companyModel->status == 0) {
                    return $this->redirect('/seller/cash-flow/shet-factura-pdf?id='.$model->id);
                }
            }
        }

        return $this->render('@app/views/invoice/create', [
            'model' => $model,
            'companyModel' => $companyModel
        ]);
    }

    public function actionShetFacturaPdf($id)
    {
        if (User::checkAdmin()){
            $invoiceData = Invoice::find()->where(['id' => $id])->with(['companies'])->asArray()->one();
        }
        else{
            $invoiceData = Invoice::find()->where(['id' => $id, 'balance_id' => Yii::$app->user->identity->company_id])->with(['companies'])->asArray()->one();
        }

//        // если пользователь еще не заполнил банковские реквизиты, создаем флеш сообщение и редиректим назад
//        echo Yii::$app->user->identity->company_id ."<br>";
//        echo $invoiceData['companies']['mfo'] ."<br>";
//        echo $invoiceData['companies']['bank_account']; exit;
        if (!$invoiceData['companies']['mfo'] || !$invoiceData['companies']['bank_account']) {
            return $this->setFlashGoBack('First you have to fill in the bank details of the company!', 'error');
        }
        // если нет такого инвойса, создаем флеш сообщение и редиректим назад
        if (!count($invoiceData)) {
            return $this->setFlashGoBack('This invoice does not exist!', 'error');
        }


        ob_end_clean();
        $payer_pdv = ($invoiceData['companies']['payer_pdv']) ? Yii::t('app', 'Payer of VAT on general grounds'): '';
        $ipn_id = ($invoiceData['companies']['ipn_id']) ? Yii::t('app', 'INN:') . $invoiceData['companies']['ipn_id']: '';
//        $PDV = $invoiceData['amount']/6;
        $PDV = 0;
        $withoutPDV = $invoiceData['amount'] - $invoiceData['amount']/6;
        $html = sprintf(
            file_get_contents(Yii::getAlias('@app').'/views/cash-flow/invoiceTemplate.html'),
            $invoiceData['companies']['legalName'],
            $invoiceData['companies']['identifier'],
            $invoiceData['companies']['streetAddress'],
            $invoiceData['companies']['bank_account'],
            $invoiceData['companies']['mfo'],
            $invoiceData['companies']['bank_branch'],
            $payer_pdv,
            $ipn_id,
            $invoiceData['code'],
            Yii::$app->formatter->asDateTime($invoiceData['created_at'], 'php:d'),
            Yii::$app->formatter->asDateTime($invoiceData['created_at'],  'php:F'),
            Yii::$app->formatter->asDateTime($invoiceData['created_at'], 'php:Y'),

            $invoiceData['destination'],
            $invoiceData['amount'],
            $invoiceData['amount'],
            $invoiceData['amount'],
//            round($withoutPDV, 2),
//            round($withoutPDV, 2),
//            round($withoutPDV, 2),
            round($PDV, 2),
            $invoiceData['amount'],
            $invoiceData['code'],
            $invoiceData['companies']['legalName']
        );

        $this->layout = 'contract';
        $html = $this->renderContent($html);

        $options = new Options();
        $options->setFontDir('../fonts');
        $dompdf = new DOMPDF($options);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->loadHtml($html);
        $dompdf->render();
        $dompdf->stream("shet-factura", ["Attachment" => 0]);
    }

    /**
     * Finds the Invoice model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Invoice the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findInvoiceModel($id)
    {
        if (($model = Invoice::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function findCompanyModel($id)
    {
        if (($model = Companies::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Finds the CashFlow model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CashFlow the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CashFlow::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
