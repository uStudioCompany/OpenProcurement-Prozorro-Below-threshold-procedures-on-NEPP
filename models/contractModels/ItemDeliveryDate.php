<?php

namespace app\models\contractModels;

class ItemDeliveryDate extends BaseModel
{
    public $endDate;

//    public function __construct($data = [], $config = [], $stage, $scenario = 'default')
//    {
//        $this->stage = $stage;
//        $this->scenario = $scenario;
//    }

    public function rules()
    {
        return [
            [['endDate'], 'required', 'whenClient' => 'function (attribute, value) { return $(attribute.input).is(":visible"); }', 'message'=>\Yii::t('app','Будь ласка, вкажіть кінцеву дату поставки/виконання робіт/надання послуг')],
            [['endDate'], 'match', 'pattern' => '/^\d{2}\/\d{2}\/\d{4} *?\d{0,2}[:]{0,1}\d{0,2}$/i'],
            [['endDate'], \app\validators\ItemDeliveryEndDate::className()],
        ];
    }

    public function attributeLabels()
    {
        return [
            'endDate' => \Yii::t('app', 'Дата кiнця поставки'),
        ];
    }
}
