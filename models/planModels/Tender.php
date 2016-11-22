<?php

namespace app\models\planModels;

class Tender extends BaseModel
{
    public $procurementMethod;
    public $procurementMethodType;
    public $tenderPeriod;                // class tenderPeriod


    public function __construct($data = [], $config = [], $stage='create')
    {
        $this->stage = $stage;
        switch ($this->stage) {
            case 'create':
                $this->tenderPeriod = new TenderPeriod($data, $config, $this->stage); break;
            case 'update':
                $this->tenderPeriod = new TenderPeriod($data, $config, $this->stage); break;
        }
        parent::__construct($data, $config, $stage);
    }

    public function rules()
    {
        return [
            [['procurementMethod'], 'string', 'max'=>30],
            [['procurementMethodType'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'procurementMethod' => \Yii::t('app', 'Можливi варiанти: “open”'),
        ];
    }
}
