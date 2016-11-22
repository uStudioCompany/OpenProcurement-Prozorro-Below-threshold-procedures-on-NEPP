<?php

namespace app\models\tenderModels;


class Guarantee extends BaseModel
{
    public $amount;
    public $currency;

    public function rules()
    {
        return [
            [['amount'], 'required','when' => function ($model) {
                $post = is_a(\Yii::$app, 'yii\console\Application') ? [] : \Yii::$app->request->post();
//                \Yii::$app->VarDumper->dump($post['Tender']['guarantee']['amount'], 10, true);die;
                return ($post['tender_method'] == 'open_aboveThresholdUA' ||
                $post['tender_method'] == 'open_aboveThresholdUA.defense' ||
                $post['tender_method'] == 'open_aboveThresholdEU') && $post['Tender']['guarantee']['amount'] != null;
            },
                'whenClient' => 'function (attribute, value) { return $(attribute.input).is(":visible"); }'],
            [['amount'], 'double'],
//            ['amount', 'compare', 'compareValue' => 0.01, 'operator' => '>='],
            [['currency'], 'string', 'max'=>3],
        ];
    }


    public function attributeLabels()
    {
        return [
            'amount' => \Yii::t('app', 'Сума гарантii'),
            'currency' => \Yii::t('app', 'Валюта'),
        ];
    }
}
