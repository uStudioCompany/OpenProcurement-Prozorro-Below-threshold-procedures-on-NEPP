<?php

namespace app\modules\seller\models\BidModels;



class Parameter extends BaseModel
{
    public $code;
    public $value;

    public function __construct($scenario='default')
    {
        
    }

    public function rules()
    {
        return [
            [['value'], \app\validators\bids\FeatureValidator::className()],
            [['code'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'value' => \Yii::t('app', 'Значення критерiю'),
            'code' => \Yii::t('app', 'Код критерiю'),
        ];
    }
}
