<?php

namespace app\models\tenderModels;

class AuctionPeriod extends BaseModel
{
    public $startDate;  // Формат даты: ISO 8601.
    public $endDate;  // Формат даты: ISO 8601.
    public $shouldStartAfter;

//    public function __construct($scenario='default')
//    {
//        $this->scenario = $scenario;
//    }

    public function rules()
    {
        return [
            [['startDate', 'endDate', 'shouldStartAfter'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'startDate' => \Yii::t('app','Дата початку паукцiону'),
            'shouldStartAfter' => \Yii::t('app','Дата кiнця перiоду оскаржень'),
        ];
    }
}
