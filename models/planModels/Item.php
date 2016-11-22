<?php

namespace app\models\planModels;

class Item extends BaseModel
{
    public $id;
    public $description;             
    public $classification;             // class Classification
    public $additionalClassifications;  // array of Classification
    public $quantity;
    public $unit;                       // class Unit
    public $deliveryDate;               // class DeliveryDate

    public function __construct($data = [], $config = [], $stage)
    {
//        parent::__construct($config);
        $this->stage = $stage;

        $this->classification            = new Classification($data, $config, $this->stage);
        $this->additionalClassifications = ['iClass' => Classification::className()];
        $this->unit                      = new Unit($data, $config, $this->stage);
        $this->deliveryDate              = new DeliveryDate($data, $config, $this->stage);

        switch ($this->stage) {
            case 'create':
                $this->classification->scheme = 'CPV';
                $this->additionalClassifications[0] = new Classification($data, $config, $this->stage);
                $this->additionalClassifications[0]->scheme = 'None';
                break;
        }
        parent::__construct($data, $config, $stage);
    }

    public function rules()
    {
        return [
            [['description'], 'required', 'whenClient' => 'function (attribute, value) { return $(attribute.input).is(":visible"); }'], // console.log(attribute); return true; //$().is(":visible")
            [['id'], 'safe'],
            [['quantity'], 'integer'],
            [['description',], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => \Yii::t('app', 'ID'),
            'description' => \Yii::t('app', 'Опис товарiв та послуг'),
            'classification' => \Yii::t('app', 'Початкова класифiкацiя елемента'),
            'additionalClassifications' => \Yii::t('app', 'Додаткова классификация'),
            'unit' => \Yii::t('app', 'Опис одиницi вимiру'),
            'quantity' => \Yii::t('app', 'Кiлькiсть'),
            'deliveryDate' => \Yii::t('app', 'Перiод, протягом якого елемент повинен бути доставлений'),
        ];
    }
}
