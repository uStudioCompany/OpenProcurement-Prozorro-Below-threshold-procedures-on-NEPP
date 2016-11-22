<?php

namespace app\models\tenderModels;

class EnquiryPeriod extends BaseModel
{
    public $startDate;  // Формат даты: ISO 8601.
    public $endDate;

    public function rules()
    {
        return [
//            [['startDate'], 'required', 'whenClient' => 'function (attribute, value) { return $(attribute.input).is(":visible"); }', 'message'=>'Будь ласка, вкажіть дату завершення періоду обговорень '],
            [['endDate'], 'required', 'whenClient' => 'function (attribute, value) { return $(attribute.input).is(":visible"); }', 'message'=>'Будь ласка, вкажіть дату завершення періоду обговорень'],
            [['startDate', 'endDate'], 'match', 'pattern' => '/^\d{2}\/\d{2}\/\d{4} *?\d{0,2}[:]{0,1}\d{0,2}$/i'],
//            [['startDate'], \app\validators\EnquiryStartPeriodValidator::className(),'whenClient' => 'function (attribute, value) { return $(attribute.input).is(":visible"); }'],
            [['endDate'], \app\validators\EnquiryEndPeriodValidator::className(),'whenClient' => 'function (attribute, value) { return $(attribute.input).is(":visible"); }'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'startDate' => \Yii::t('app','Дата початку перiоду, коли дозволено задавати питання'),
            'endDate' => \Yii::t('app','Дата кiнця перiоду, коли дозволено задавати питання'),
        ];
    }

}
