<?php

namespace app\models\tenderModels;

class QualificationPeriod extends BaseModel
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
            [['startDate', 'endDate'], 'safe', 'on'=>'eu_prequalification']
        ];
    }

    public function attributeLabels()
    {
        return [
            'startDate' => \Yii::t('app','Дата початку перiоду оскаржень'),
            'endDate' => \Yii::t('app','Дата кiнця перiоду оскаржень'),
        ];
    }
}
