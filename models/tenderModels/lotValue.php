<?php

namespace app\models\tenderModels;

use app\models\tenderModels\BaseModel;
use app\models\tenderModels\Value;

class lotValue extends BaseModel
{
    public $value;          // class Value
    public $relatedLot;
    public $date;
    public $participationUrl;         

    public function __construct($stage,$scenario='default')
    {
        $this->value = new Value($scenario);

        parent::__construct($scenario);
    }

    public function rules()
    {
        return [
            [['relatedLot'], 'safe'],
            [['date'], 'safe'],
            [['participationUrl'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'participationUrl' => \Yii::t('app', 'Веб-адреса для участi в аукцiонi'),
        ];
    }
}
