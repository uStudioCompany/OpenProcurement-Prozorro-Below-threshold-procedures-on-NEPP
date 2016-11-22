<?php

namespace app\models\tenderModels;

class ContractPeriod extends BaseModel
{
    public $startDate;  // Формат даты: ISO 8601.
    public $endDate;

//    public function __construct($scenario='default')
//    {
//        $this->scenario = $scenario;
//    }

    public function rules()
    {
        return [
            [['startDate', 'endDate'], 'required','whenClient' => 'function (attribute, value) { return $(attribute.input).is(":visible"); }', 'except' => 'limitedavards'],
            [['startDate', 'endDate'], 'match', 'pattern' => '/^\d{2}\/\d{2}\/\d{4} *?\d{0,2}[:]{0,1}\d{0,2}$/i', 'except' => 'limitedavards'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'startDate' => \Yii::t('app', 'Дата початку дii договору'),
            'endDate' => \Yii::t('app', 'Дата кiнця дii договору'),
        ];
    }
}
