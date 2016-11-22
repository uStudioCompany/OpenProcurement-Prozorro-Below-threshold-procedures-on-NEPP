<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cash_flow".
 *
 * @property integer $id
 * @property string $way
 * @property double $amount
 * @property integer $balance_id
 * @property integer $tender_id
 * @property string $lot_id
 * @property integer $payment_id
 * @property integer $cash_flow_reason_id
 * @property integer $created_at
 *
 * @property CashFlowReason $cashFlowReason
 * @property Tenders $tender
 * @property Payment $payment
 * @property Companies $balance
 */
class CashFlow extends \yii\db\ActiveRecord
{

    const CASHFLOW_REASON_TENDER_PARTICIPATION = 1;
    const CASHFLOW_REASON_BID_CANCEL = 2;
    const CASHFLOW_REASON_FIN_AUTH_OFF = 3;
    const CASHFLOW_REASON_PAY = 4;
    const CASHFLOW_WAY_IN = 'in';
    const CASHFLOW_WAY_OUT = 'out';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cash_flow';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['way', 'amount', 'cash_flow_reason_id', 'created_at', 'payed_at'], 'required'],
            [['way'], 'string'],
            [['amount'], 'number'],
            [['balance_id', 'tender_id', 'payment_id', 'cash_flow_reason_id', 'created_at', 'payed_at', 'invoice_id'], 'integer'],
            [['lot_id'], 'string', 'max' => 32],
            [['cash_flow_reason_id'], 'exist', 'skipOnError' => true, 'targetClass' => CashFlowReason::className(), 'targetAttribute' => ['cash_flow_reason_id' => 'id']],
            [['tender_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tenders::className(), 'targetAttribute' => ['tender_id' => 'id']],
            [['payment_id'], 'exist', 'skipOnError' => true, 'targetClass' => Payment::className(), 'targetAttribute' => ['payment_id' => 'id']],
            [['balance_id'], 'exist', 'skipOnError' => true, 'targetClass' => Companies::className(), 'targetAttribute' => ['balance_id' => 'id']],
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
            'way' => Yii::t('app', 'Way'),
            'amount' => Yii::t('app', 'Amount'),
            'balance_id' => Yii::t('app', 'Balance ID'),
            'tender_id' => Yii::t('app', 'ID Tender'),
            'tender_name' => Yii::t('app', 'Tender title'),
            'tender_json' => Yii::t('app', 'Lot description'),
            'lot_id' => Yii::t('app', 'ID Lot'),
            'payment_id' => Yii::t('app', 'Payment ID'),
            'invoice_id' => Yii::t('app', '№ Invoice'),
            'cash_flow_reason_id' => Yii::t('app', 'Cash Flow Reason'),
            'created_at' => Yii::t('app', 'Created At'),
            'payed_at' => Yii::t('app', 'Payed At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCashFlowReason()
    {
        return $this->hasOne(CashFlowReason::className(), ['id' => 'cash_flow_reason_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTender()
    {
        return $this->hasOne(Tenders::className(), ['id' => 'tender_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayment()
    {
        return $this->hasOne(Payment::className(), ['id' => 'payment_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBalance()
    {
        return $this->hasOne(Companies::className(), ['id' => 'balance_id']);
    }

    public function getInvoice()
    {
        return $this->hasOne(Invoice::className(), ['id' => 'invoice_id']);
    }
}
