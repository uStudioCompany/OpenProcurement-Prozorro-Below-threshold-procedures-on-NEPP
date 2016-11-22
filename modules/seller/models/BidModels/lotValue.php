<?php

namespace app\modules\seller\models\BidModels;




class lotValue extends BaseModel
{
    public $value;          // class Value
    public $relatedLot;
    public $date;
    public $participationUrl;
    public $subcontractingDetails;
    public $status;

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
            [['subcontractingDetails', 'status'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'participationUrl' => \Yii::t('app', 'Веб-адреса для участi в аукцiонi'),
        ];
    }
}
