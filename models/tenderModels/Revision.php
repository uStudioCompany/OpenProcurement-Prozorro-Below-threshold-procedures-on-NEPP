<?php

namespace app\models\tenderModels;

use app\models\tenderModels\BaseModel;

class Revision extends BaseModel
{
    public $date;
    public $changes;    // ???
    
    public function rules()
    {
        return [
           [['date'], 'safe'],
           [['changes'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'date' => \Yii::t('app', 'Дата, коли змiни були записанi'),
        ];
    }
}
