<?php

namespace app\models\planModels;

class Plan extends BaseModel
{

    public $id;
    public $planID;
    public $procuringEntity;           // class Organization
    public $budget;                    // class Budget
    public $tender;                    // class Tender
    public $classification;            // class Classification
    public $additionalClassifications; // array class Classification
    public $items;                     // array of Item


    public function __construct($data = [], $config = [], $stage = 'create')
    {
        $this->stage = $stage;

        $this->procuringEntity = new Organization($data, $config, $this->stage);
        $this->budget = new Budget($data, $config, $this->stage);
        $this->tender = new Tender($data, $config, $this->stage);
        $this->classification = new Classification($data, $config, $this->stage);
        $this->additionalClassifications = ['iClass' => AdditionalClassification::className()];
        $this->items = ['iClass' => Item::className()];

        switch ($this->stage) {
            case 'create':
                $this->classification->scheme = 'CPV';
                $this->additionalClassifications[0] = new Classification;
//                $this->additionalClassifications[0]->scheme = 'ДКПП';
                $this->additionalClassifications[1] = new Classification;
                $this->additionalClassifications[1]->scheme = 'КЕКВ';
                $this->additionalClassifications[2] = new Classification;
                $this->additionalClassifications[2]->scheme = 'КЕКВ';
                $this->additionalClassifications[3] = new Classification;
                $this->additionalClassifications[3]->scheme = 'КЕКВ';
                break;
        }
        $this->items['__EMPTY_ITEM__'] = new Item([], [], 'create');

        parent::__construct($data, $config, $stage);
    }

    public function rules()
    {
        return [
            [['id', 'planID'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => \Yii::t('app', 'ID'),
            'planID' => \Yii::t('app', 'iдентифiкатор плану'),
            'procuringEntity' => \Yii::t('app', 'Органiзацiя, що проводить закупiвлю'),
            'budget' => \Yii::t('app', 'Повний доступний бюджет плану'),
            'tender' => \Yii::t('app', 'Планова дата старту процедури'),
        ];
    }
}
