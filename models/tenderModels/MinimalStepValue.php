<?php

namespace app\models\tenderModels;

use app\models\tenderModels\BaseModel;
use Yii;

class MinimalStepValue extends BaseModel
{
    public $amount;
    public $amountProcent;
    public $currency;
    public $valueAddedTaxIncluded;

    public function rules()
    {
        return [
            [['amount'], 'required',
                'whenClient' => 'function (attribute, value) { return $(attribute.input).is(":visible") && !$(attribute.input).is(":disabled"); }',
                'when' => function ($model) {
                    $post = is_a(\Yii::$app, 'yii\console\Application') ? [] : \Yii::$app->request->post();
                    return !in_array($post['tender_method'], Yii::$app->params['2stage.tender']);
                },
                'message'=>\Yii::t('app','Будь ласка, введіть мiнiмальний крок пониження ціни')],
            ['amount', 'compare', 'compareValue' => 0, 'operator' => '>='],
            [['amount','amountProcent'], 'double','message'=>\Yii::t('app','Будь ласка, введіть коректне значення кроку пониження ціни (допустимі символи [0-9] та ".")')],
            [['amount'], \app\validators\SummValidator::className(),'whenClient' => 'function (attribute, value) { return $(attribute.input).is(":visible"); }'],
            ['amountProcent', 'compare', 'compareValue' => 0.01, 'operator' => '>='],
            ['amountProcent', 'compare', 'compareValue' => 100.00, 'operator' => '<='],
            [['currency'], 'safe'],
            [['valueAddedTaxIncluded'], 'boolean'],
        ];
    }


    public function attributeLabels()
    {
        return [
            'amount' => \Yii::t('app', 'Мiнiмальний крок аукцiону'),
            'amountProcent' => \Yii::t('app', 'Мiнiмальний крок аукцiону у вiдсотках'),
            'currency' => \Yii::t('app', 'Валюта'),
        ];
    }
}
