<?php

namespace app\models\tenderModels;

use app\validators\LimitedAwardAmount;
use app\validators\AmountOpenValidator;
use Yii;

class Value extends BaseModel
{
    public $amount;
    public $currency;             
    public $valueAddedTaxIncluded;

//    public function __construct($scenario='default')
//    {
//        $this->scenario = $scenario;
//        parent::__construct($scenario);
//    }

    public function rules()
    {
        return [

//           [['amount'], 'match', 'pattern' => '/^\d{1,11}$|^\d{1,11}\.\d{1,2}$/i', 'message'=>Yii::t('app','Будь ласка, введіть коректне значення бюджету (допустимі символи [0-9] та "."')],
           [['amount'], 'double','message'=>Yii::t('app','Будь ласка, введіть коректне значення бюджету (допустимі символи [0-9] та ".")')],
            ['amount', 'compare', 'compareValue' => 0, 'operator' => '>=', 'message'=>Yii::t('app','Будь ласка, введіть коректне значення бюджету (бюджет не може бути менше "0")')],
           [['amount'], 'required',
               'whenClient' => 'function (attribute, value) { return $(attribute.input).is(":visible") && !$(attribute.input).is(":disabled"); }',
               'when' => function ($model) {
                   $post = is_a(\Yii::$app, 'yii\console\Application') ? [] : \Yii::$app->request->post();
                   return !in_array($post['tender_method'], Yii::$app->params['2stage.tender']);
               },
               'message'=>Yii::t('app','Будь ласка, введіть бюджет закупівлі')],
           [['currency',], 'string', 'max'=>3],
           [['valueAddedTaxIncluded'], 'boolean'],

//            [['amount'], LimitedAwardAmount::className(), 'on'=>['limitedavards', 'eu_prequalification']],
            [['amount'], AmountOpenValidator::className()],
        ];
    }

    public function attributeLabels()
    {
        return [
            'amount' => Yii::t('app','Повний бюджет'),
            'currency' => Yii::t('app','Валюта'),
        ];
    }
    public static function getPDV()
    {
        return [
            '0' => Yii::t('app','без урахування ПДВ'),
            '1' => Yii::t('app','з урахуванням ПДВ')
        ];
    }
    public static function getCurrency()
    {
        return [
            'UAH' => 'UAH',
            'USD' => 'USD',
            'EUR'=>'EUR',
            'RUB'=>'RUB',
            'GBP'=>'GBP'
        ];
    }
}