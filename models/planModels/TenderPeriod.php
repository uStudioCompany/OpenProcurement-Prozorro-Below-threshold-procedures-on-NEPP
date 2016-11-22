<?php

namespace app\models\planModels;

class TenderPeriod extends BaseModel
{
    public $startDate;

    public function rules()
    {
        return [
            [['startDate'], 'required'],
//            [['startDate'], 'match', 'pattern' => '/^\d{2}.\d{2}.\d{4} \d{2}:\d{2}$/i'],
            [['startDate'], 'string', 'max'=>30],
        ];
    }

    public function attributeLabels()
    {
        return [
            'startDate' => \Yii::t('app', 'Планова дата старту процедури'),
        ];
    }
}
