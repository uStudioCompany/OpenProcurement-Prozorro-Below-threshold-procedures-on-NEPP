<?php

namespace app\modules\seller\models\BidModels;



use app\modules\seller\validators\BidValueValidator;

class Value extends BaseModel
{
    public $amount;
    public $currency;             
    public $valueAddedTaxIncluded;

    public function __construct($scenario='default')
    {
        $this->scenario = $scenario;
        parent::__construct($scenario);
    }

    public function rules()
    {
        //die();
        return [

//          [['amount'], 'match', 'pattern' => '/^\d{1,11}$|^\d{1,11}\.\d{1,2}$/i', 'message'=>\Yii::t('app','Будь ласка, введіть коректне значення бюджету (допустимі символи [0-9] та "."')],
            [['amount'], 'double','message'=>\Yii::t('app','Будь ласка, введіть коректне значення пропозиції (допустимі символи [0-9] та ".")')],
            ['amount', 'compare', 'compareValue' => 0, 'operator' => '>', 'message'=>\Yii::t('app','Будь ласка, введіть коректне значення пропозиції (пропозиції не може бути менше або дорівнювати "0")')],
//          [['amount'], 'required','whenClient' => 'function (attribute, value) { return $(attribute.input).is(":visible"); }', 'message'=>\Yii::t('app','Будь ласка, введіть бюджет закупівлі4444')],
//          [['amount'], 'filter', 'filter' => 'trim', 'skipOnEmpty' => true],
            [['currency',], 'safe'],
            [['amount'], BidValueValidator::className()],
        ];
    }

    public function attributeLabels()
    {
        return [
            'amount' => \Yii::t('app','Повний бюджет'),
            'currency' => \Yii::t('app','Валюта'),
        ];
    }
    public static function getPDV()
    {
        return [
            '0' => \Yii::t('app','plan.PDV.0'),
            '1' => \Yii::t('app','plan.PDV.1')
        ];
    }
    public static function getCurrency()
    {
        return [
            'UAH' => 'UAH',
            'USD' => 'USD',
            'EUR' => 'EUR',
            'RUB' => 'RUB',
            'GBP' => 'GBP'
        ];
    }
}
