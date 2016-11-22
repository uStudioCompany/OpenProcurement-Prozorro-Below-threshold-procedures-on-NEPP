<?php

namespace app\models\planModels;

class DeliveryDate extends BaseModel
{
    public $endDate;

    public function rules()
    {
        return [
//            [['endDate'], 'required', 'whenClient' => 'function (attribute, value) { return $(attribute.input).is(":visible"); }'],
            //[['endDate'], 'match', 'pattern' => '/^\d{2}.\d{2}.\d{4} \d{2}:\d{2}$/i'],
            [['endDate'], 'match', 'pattern' => '/^\d{2}\/\d{2}\/\d{4} *?\d{0,2}[:]{0,1}\d{0,2}$/i'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'endDate' => \Yii::t('app', 'Дата кiнця поставки'),
        ];
    }

}
