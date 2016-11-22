<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "payment".
 *
 * @property integer $id
 * @property string $payment_id
 * @property string $status
 * @property integer $created_at
 * @property integer $invoice_id
 * @property string $destination
 * @property string $codes
 * @property double $amount
 * @property string $json
 * @property Invoice $invoice
 * @property Companies $companies
 */
class Payment extends ActiveRecord
{
    const STATUS_NOT_VERIFIED = 'rejected';
    const STATUS_VERIFIED = 'accepted';
    const STATUS_DOUBLE = 'double';
    const STATUS_UNKNOWN = 'unknown';

    public $invoiceModel;
    public $paymentData;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'payment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['payment_id', 'status', 'destination', 'codes', 'amount', 'json'], 'required'],
            [['status', 'json', 'codes'], 'string'],
            [['created_at', 'invoice_id'], 'integer'],
            [['amount'], 'number'],
            [['payment_id'], 'string', 'max' => 50],
            [['destination', 'codes'], 'string', 'max' => 255],
            [['invoice_id'], 'exist', 'skipOnError' => true, 'targetClass' => Invoice::className(), 'targetAttribute' => ['invoice_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'payment_id' => Yii::t('app', 'Payment ID'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'invoice_id' => Yii::t('app', 'Invoice ID'),
            'destination' => Yii::t('app', 'Destination'),
            'codes' => Yii::t('app', 'Payment ID'),
            'amount' => Yii::t('app', 'Amount'),
            'json' => Yii::t('app', 'Json'),
            'legalName' => Yii::t('app', 'Legal name'),
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoice()
    {
        return $this->hasOne(Invoice::className(), ['id' => 'invoice_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompanies()
    {
        return $this->hasOne(Companies::className(), ['id' => 'balance_id'])->via('invoice');
    }

//    public function beforeSave($insert)
//    {
//        if ($this->isNewRecord) {
//            $this->created_at = time();
//        }
//
//        return parent::beforeSave($insert);
//    }

    public function getCreatedAt() {
        return ($this->created_at) ? Yii::$app->formatter->asDateTime($this->created_at, 'php:d') . " " . Yii::$app->formatter->asDateTime($this->created_at,  'php:F') . " " . Yii::$app->formatter->asDateTime($this->created_at, 'php:Y') . " " . Yii::$app->formatter->asDateTime($this->created_at, 'php:h') . ":" . Yii::$app->formatter->asDateTime($this->created_at, 'php:i') : '-';
    }

    public function getPayedAt() {
        return ($this->payed_at) ? Yii::$app->formatter->asDateTime($this->payed_at, 'php:d') . " " . Yii::$app->formatter->asDateTime($this->payed_at,  'php:F') . " " . Yii::$app->formatter->asDateTime($this->payed_at, 'php:Y') . ":" . Yii::$app->formatter->asDateTime($this->payed_at, 'php:h') . " " . Yii::$app->formatter->asDateTime($this->payed_at, 'php:i') : '-';
    }

    public function getCashFlows()
    {
        return $this->hasMany(CashFlow::className(), ['payment_id' => 'id']);
    }

    public function execute(){
        $this->json = json_decode(file_get_contents('php://input'));
        $invoiceCodes = $this->findInvoiceCodes($this->json->destination);
        $this->invoiceModel = $this->getInvoiceForPayment($invoiceCodes);
        $this->paymentData = $this->setPaymentData($invoiceCodes);
        $this->status = $this->getStatus();
        return true;
    }

    private function getStatus(){
        // Проверить что они это они (x-sso-client-token)
        $ssoClientToken = Yii::$app->request->headers->get('x-sso-client-token');
        if (!$this->checkSsoClientToken($ssoClientToken)){
            return self::STATUS_NOT_VERIFIED;
        }
        elseif(!is_object($this->invoiceModel)){
            return self::STATUS_UNKNOWN;
        }
        elseif( $this::find()->where(['and', ['payment_id' => $this->json->id], ['or', ['status' => self::STATUS_VERIFIED], ['status' => self::STATUS_DOUBLE]]])->one() ) {
            return self::STATUS_DOUBLE;
        }
        else{
            return self::STATUS_VERIFIED;
        }
    }

    /**
     * Проверка что токен действителен в sso
     */
    private function checkSsoClientToken($ssoClientToken){
        return true;
    }

    private function getInvoiceForPayment($invoiceCodes){
        // find invoice
        $invoiceModel = Invoice::find()->where([ 'code' => $invoiceCodes ])->orderBy('code DESC')->one();
        $checkEDRPOU = Companies::find()->where(['id' => $invoiceModel->balance_id])->andWhere([ 'identifier' => $this->json->payer->EDRPOU])->asArray()->one();
        if (!count($checkEDRPOU)){
            return NULL;
        }

        return $invoiceModel;
    }


    public function createPaymentByInvoice(){
        $cashFlowModel = new CashFlow();
        if ($this->load( ['Payment'=>$this->paymentData] ) ) {

            // Обновляем статус и дату оплаты инвойса
            $this->invoiceModel->status = 'payed';
            $this->invoiceModel->payed_at =  $this->paymentData['payed_at'];

            // Заполняем табличку cash_flow
            $cashFlowModel->way = 'in';
            $cashFlowModel->amount = $this->amount;
            $cashFlowModel->balance_id = $this->invoiceModel->balance_id;
            $cashFlowModel->invoice_id = $this->invoiceModel->id;
            $cashFlowModel->cash_flow_reason_id = CashFlow::CASHFLOW_REASON_PAY;
            $cashFlowModel->created_at = $this->invoiceModel->created_at;
            $cashFlowModel->payed_at = date('U', $this->paymentData['payed_at']);


            $transaction = Yii::$app->db->beginTransaction();
            $this->json = json_encode($this->json);
            if( $this->save() && $this->invoiceModel->save()){

                $cashFlowModel->payment_id = $this->id;

                $company = Companies::findOne($this->invoiceModel->balance_id);
                $contract = Contracts::find()->where(['company_id' => $this->invoiceModel->balance_id])->one();
                $finance = new \app\components\finance();

                if($cashFlowModel->save()){
                    $curentBalance = $company->balance;
                    $balance = $finance->refreshBalance($company->id);
                    if ($curentBalance + $this->amount > $balance){
                        $cashFlowModelOut = new CashFlow();
                        $cashFlowModelOut->way = 'out';
                        $cashFlowModelOut->amount = 10.00;
                        $cashFlowModelOut->balance_id = $company->id;
                        $cashFlowModelOut->cash_flow_reason_id = CashFlow::CASHFLOW_REASON_FIN_AUTH_OFF;
                        $cashFlowModelOut->created_at = date('U');
                        $cashFlowModelOut->payed_at = date('U');
                        if ($cashFlowModelOut->save()){
                            $transaction->commit();
                        }
                        else{
                            $transaction->rollback();
                            print_r($this->getErrors());
                            echo "NO";
                            exit;
                        }
                    }
                    else{
                        $transaction->commit();
                    }
                    $user = User::find()->where(['company_id' => $company->id])->one();
                    if($balance < 10 && $company->status != Companies::STATUS_ACCEPTED){
                        Yii::$app->mailer->compose('email_fin_auth', [
                            'invoiceNum' => $this->invoiceModel->code,
                            'sum' => $this->paymentData['amount'],
                            'companyName' => $company->legalName,
                            'balance' => $company->balance,
                            'contractNum' => $contract->contract_num,
                            'fin_auth' => false,
                        ])
                            ->setFrom(Yii::$app->params['mail_sender_full'])
                            ->setTo([$user->username])
                            ->setSubject(Yii::t('app', 'Недостатньо коштів для фінансової авторізації.'))
                            ->send();
                    }
                    if($company->status == Companies::STATUS_ACCEPTED && $this->paymentData['amount'] >= $balance){
                        Yii::$app->mailer->compose('email_fin_auth', [
                            'invoiceNum' => $this->invoiceModel->code,
                            'sum' => $this->paymentData['amount'],
                            'companyName' => $company->legalName,
                            'balance' => $company->balance,
                            'contractNum' => $contract->contract_num,
                            'fin_auth' => true,
                        ])
                            ->setFrom(Yii::$app->params['mail_sender_full'])
                            ->setTo([$user->username])
                            ->setSubject(Yii::t('app', 'Списання коштів для фінансової авторізації.'))
                            ->send();
                    }
                    echo "OK";
                    exit;
                }
                else{
                    $transaction->rollback();
                    print_r($this->getErrors());
                    echo "NO";
                    exit;
                }
            }
            else{
                $transaction->rollback();
                print_r($this->getErrors());
                echo "NO";
                exit;
            }
        }
        print_r($this->getErrors());
        echo "NO";
        throw new \Exception("Error Processing Request", 1);
        exit;
    }

    public function createPaymentWithoutInvoice(){
        if ($this->load( ['Payment'=>$this->paymentData] ) ) {
            $transaction = Yii::$app->db->beginTransaction();
            $this->json = json_encode($this->json);
            if( $this->save()){
                $transaction->commit();
                echo "OK";
                exit;
            }
            else{
                $transaction->rollback();
                print_r($this->getErrors());
                echo "NO";
                exit;
            }
        }
        print_r($this->getErrors());
        echo "NO";
        throw new \Exception("Error Processing Request", 1);
        exit;
    }

    private function setPaymentData($invoiceCodes){
        if (is_object($this->invoiceModel)) {
            return array(
                'payment_id' => $this->json->id,
                'invoice_id' => ($this->invoiceModel->id) ? $this->invoiceModel->id : NULL,
                'destination' => $this->json->destination,
                'codes' => $invoiceCodes ? implode(', ', $invoiceCodes) : '',
                'amount' => $this->json->amount,
                'payed_at' => strtotime($this->json->technicalData->Fact->info->{'@postdate'}),
                'created_at' => strtotime($this->json->technicalData->Fact->info->{'@postdate'}),
            );
        }
        else{
            return array(
                'payment_id' => $this->json->id,
                'invoice_id' => NULL,
                'destination' => $this->json->destination,
                'codes' => $invoiceCodes ? implode(', ', $invoiceCodes) : '',
                'amount' => $this->json->amount,
                'payed_at' => strtotime($this->json->technicalData->Fact->info->{'@postdate'}),
                'created_at' => strtotime($this->json->technicalData->Fact->info->{'@postdate'}),
            );
        }
    }


    /**
     * Поиск частей в назначении платежа, похожих на код инвойса
     * номер договора - 26 символа ЕДРПОУ+ДДММГГГГЧЧММСС (3233323222062016222956)
     * номер инвойса - 27+ символов - номер догоувора +символ-разделитель+номерплатежа
     * 3233323222062016222956-12 или 3233323222062016222956/1 или 32333232220620162229569
     */
    private function findInvoiceCodes( $destination ){

        $rega = '~\d[\d\S]+\d~';
        $res = preg_match_all($rega, $destination, $matches);

        $min_str = Contracts::DOCNUM_LEN;

        $results = [];
        $current = '';

        foreach ($matches[0] as $code) {
            if( strlen($code) < Contracts::DOCNUM_LEN )
                continue;
            $results[] = Invoice::normalizeInvoiceCode($code);
        }

        $results = array_unique( $results );


        // sort by strlen DESC
        usort($results, function($a, $b){
            $sa = strlen($a);
            $sb = strlen($b);
            if ($sa == $sb) return 0;
            return ($sa > $sb) ? -1 : 1;
        });
        // var_dump($results); exit;

        return $results;
    }
}
