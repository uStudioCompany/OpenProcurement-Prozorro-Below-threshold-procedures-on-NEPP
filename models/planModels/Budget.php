<?php

namespace app\models\planModels;

use Yii;

class Budget extends BaseModel
{
    public $id;
    public $description;
    public $amount;
    public $currency;
    public $amountNet;
    public $valueAddedTaxIncluded = 1;
    public $project;             // class Project
    public $year;
    public $notes;

    public function __construct($data = [], $config = [], $stage='create')
    {
        $this->stage = $stage;
        $this->project = new Project($data, $config, $this->stage);
        parent::__construct($data, $config, $stage);
    }

    public function rules()
    {
        return [
            [['description','amount', 'year'], 'required'],  //'id',
            ['amount', 'compare', 'compareValue' => 0.00, 'operator' => '>='],
            [['amount'], 'double'],
            [['description',], 'string'],
            [['amountNet','valueAddedTaxIncluded','year'], 'integer'],
            [['currency',], 'string', 'max'=>3],
            [['notes'], 'string', 'max'=>350],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => \Yii::t('app', 'Назва органiзацiї'),
            'identifier' => \Yii::t('app', 'iдентифiкатор цiєї органiзацiї'),
            'year' => \Yii::t('app', 'Рiк'),
            'notes' => \Yii::t('app', 'Примiтки'),
            'description' => \Yii::t('app', 'description'),
            'amount' => \Yii::t('app', 'amount'),
            'currency' => \Yii::t('app', 'currency'),
            'amountNet' => \Yii::t('app', 'amountNet'),
        ];
    }
}
