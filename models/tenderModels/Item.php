<?php

namespace app\models\tenderModels;


use Yii;

class Item extends BaseModel
{
    public $id;
    public $description;             
    public $description_en;
    public $descriptionRu;
    public $classification;             // class Classification
    public $additionalClassifications;  // array of Classification
    public $unit;                       // class Unit
    public $quantity;
    public $deliveryDate;               // class Period
    public $deliveryAddress;            // class Address
    public $deliveryLocation;           
    public $relatedLot;

    public function __construct($scenario='default')
    {
        $this->unit            = new Unit($scenario);
        $this->deliveryDate    = new ItemDeliveryDate($scenario);
        $this->deliveryAddress = new DeliveryAddress($scenario);
        $this->deliveryLocation = new DeliveryLocation($scenario);
        $this->classification  = new Classification($scenario);

        //$this->additionalClassifications = ['iClass' => Classification::className()];
        $this->additionalClassifications = ['iClass' => additionalClassifications::className()];

//        $this->classification->scheme = 'CPV';
//        $this->additionalClassifications = ['iClass' => AdditionalClassifications::className()];

//        switch ($this->stage) {
//            case 'create':
//            case 'update':
//                $this->additionalClassifications[0] = new AdditionalClassifications([],[],$stage, $scenario);
//                $this->additionalClassifications[0]->scheme = 'ДКПП';
//                break;
//
//            case 'load':
//                break;
//
//            default:
//        }


        parent::__construct($scenario);
    }

    public function rules()
    {
        return [
            [['id'], 'safe'],

            [['description'], 'required',
                'whenClient' => 'function (attribute, value) { return $(attribute.input).is(":visible") && !$(attribute.input).is(":disabled"); }',
                'when' => function ($model) {
                    $post = is_a(\Yii::$app, 'yii\console\Application') ? [] : \Yii::$app->request->post();
                    return !in_array($post['tender_method'], Yii::$app->params['2stage.tender']);
                },
                'message'=>\Yii::t('app','Будь ласка, введіть опис товару або послуги')],
            [['quantity'], 'required',
                'whenClient' => 'function (attribute, value) { return $(attribute.input).is(":visible"); }',
                'when' => function ($model) {
                    $post = is_a(\Yii::$app, 'yii\console\Application') ? [] : \Yii::$app->request->post();
                    return !in_array($post['tender_method'], Yii::$app->params['2stage.tender']);
                },
                'message'=>\Yii::t('app','Будь ласка, введіть кiлькість товару або послуг')],

            [['description_en'], 'required', 'when' => function ($model) {
                $post = is_a(\Yii::$app, 'yii\console\Application') ? [] : \Yii::$app->request->post();
                return $post['tender_method'] == 'open_aboveThresholdEU';
            },
                'whenClient' => 'function (attribute, value) { return $(".tender_method_select").val() != "open_aboveThresholdUA.defense" && $(attribute.input).is(":visible"); }'
            ],

            ['quantity', 'compare', 'compareValue' => 0, 'operator' => '>=','message'=>\Yii::t('app','Будь ласка, введіть коректне значення кiлькості товару або послуги (повинно бути цілим і більше ніж "0")')],
            [['quantity'], 'integer', 'message'=>\Yii::t('app','Будь ласка, введіть коректне значення кiлькості товару або послуги (допустимі символи [0-9])')],
            [['description','description_en','descriptionRu'], 'string', 'max'=>255, 'message'=>\Yii::t('app','Опис товару або послуги закупівлі не може складатися з більше ніж 255 символів')],



//            [['deliveryLocation'], 'safe'],
            [['relatedLot'], 'safe'],


        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => \Yii::t('app', 'ID'),
            'description' => \Yii::t('app', 'Опис товарiв та послуг'),
            'descriptionRu' => \Yii::t('app', 'Описание товара и услуг'),
            'description_en' => \Yii::t('app', 'Goods and services description'),
            'classification' => \Yii::t('app', 'Початкова класифiкацiя елемента'),
            'additionalClassifications' => \Yii::t('app', 'Додаткова классификация'),
            'unit' => \Yii::t('app', 'Опис одиницi вимiру'),
            'quantity' => \Yii::t('app', 'Кiлькiсть'),
            'deliveryDate' => \Yii::t('app', 'Перiод, протягом якого елемент повинен бути доставлений'),
            'deliveryAddress' => \Yii::t('app', 'Адреса мiсця, куди елемент повинен бути доставлений'),
            'deliveryLocation' => \Yii::t('app', 'Географiчнi координати мiсця доставки'),
            'relatedLot' => \Yii::t('app', 'ID Lot'),
        ];
    }

    public static function getItemById($tender, $id, $mode = 'model')
    {
        if ($mode == 'model') {
            foreach ($tender->items as $k => $item) {
                if ($item->id == $id) {
                    return $item;
                }
            }
        } else {
            $tender = (array)$tender;
            foreach ($tender['items'] as $k => $item) {
                if ($item['id'] == $id) {
                    return $item;
                }
            }
        }
    }
}
