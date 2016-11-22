<?php

namespace app\models;

use app\components\InvoiceBehavior;
use app\validators\InvoiceValidator;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "invoice".
 *
 * @property integer $id
 * @property string $code
 * @property double $amount
 * @property integer $balance_id
 * @property string $destination
 * @property string $status
 * @property integer $created_at
 * @property integer $payed_at
 */
class Invoice extends ActiveRecord
{
    const FIN_AUTH_DEFAULT_AMOUNT = 10;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'invoice';
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_at',
                ],
                'value' => function() { return date('U');},
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['amount'], 'required'],
            [['amount'], 'double', 'min' => 0.01, 'max' => 1000000, 'tooBig' => Yii::t('app', 'The maximum amount of the invoice should not exceed 1 million. UAH.')],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => Yii::t('app', 'Invoice Num'),
            'amount' => Yii::t('app', 'Amount'),
            'balance_id' => Yii::t('app', 'Balance ID'),
            'destination' => Yii::t('app', 'Destination'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'payed_at' => Yii::t('app', 'Payed At'),
            'legalName' => Yii::t('app', 'Legal name'),
        ];
    }


    /**
     * Вспомогательная ф-я для нормализации строки кодап инвойса
     */
    public static function normalizeInvoiceCode( $code ){
        $digits = preg_replace('~\D~','',$code);

        if( strlen($digits) >= Contracts::DOCNUM_LEN ) {
            return $code;
        }

        return substr($digits, 0, Contracts::DOCNUM_LEN) . '-' . substr($digits, Contracts::DOCNUM_LEN);

    }

    public function getBalance()
    {
        return $this->hasOne(Companies::className(), ['id' => 'balance_id']);
    }


    public function getTaxPayerType()
    {
        return $this->hasOne(TaxPayerType::className(), ['id' => 'tax_payer_type_id'])
            ->via('companies');
    }

    public function getCompanies()
    {
        return $this->hasOne(Companies::className(), ['id' => 'balance_id']);
    }

    public function getPayment()
    {
        return $this->hasOne(Payment::className(), ['invoice_id' => 'id']);
    }

    public function getContractItem()
    {
        return $this->hasOne(Contracts::className(), ['company_id' => 'id'])
            ->via('companies');
    }


    public function getInvoiceCode($company_id){
        $contractNum = Contracts::find()->where(['company_id' => $company_id])->asArray()->one();
        $contractNum = $contractNum['contract_num'];
        $invoceCount = $this::find()->where(['balance_id' => $company_id])->count();

        $this->code = $contractNum . "-" . ++$invoceCount;

        $this->code = $this::normalizeInvoiceCode( $this->code );

        return $this->code;
    }
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                if (!$this->balance_id) {
                    $this->balance_id = \Yii::$app->user->identity->company_id;
                }
                $company_id = $this->balance_id;
                $code = $this->getInvoiceCode($company_id);

                if (!$this->destination) {
                    $this->destination = \Yii::t('app', 'Payment for services acc.№') . $code;
                }
                $this->status = 'pending';
            }
            return true;
        }
        return false;
    }
}
