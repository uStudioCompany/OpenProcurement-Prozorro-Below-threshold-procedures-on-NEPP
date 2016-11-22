<?php

namespace app\models\tenderModels;

use app\models\tenderModels\BaseModel;

class ItemDeliveryDate extends BaseModel
{
    public $startDate;
    public $endDate;

//    public function __construct($data = [], $config = [], $stage, $scenario = 'default')
//    {
//        $this->stage = $stage;
//        $this->scenario = $scenario;
//    }

    public function rules()
    {
        return [
            [['startDate', 'endDate'], 'required', 'whenClient' => 'function (attribute, value) { return $(attribute.input).is(":visible"); }', 'message'=>\Yii::t('app','Будь ласка, вкажіть кінцеву дату поставки/виконання робіт/надання послуг')],
            [['startDate','endDate'], 'match', 'pattern' => '/^\d{2}\/\d{2}\/\d{4} *?\d{0,2}[:]{0,1}\d{0,2}$/i'],
            [['endDate'], \app\validators\ItemDeliveryEndDate::className()],
            [['startDate'], \app\validators\ItemDeliveryStartDate::className()],
        ];
    }

    public function attributeLabels()
    {
        return [
            'startDate' => \Yii::t('app', 'Дата початку поставки'),
            'endDate' => \Yii::t('app', 'Дата кiнця поставки'),
        ];
    }
}
