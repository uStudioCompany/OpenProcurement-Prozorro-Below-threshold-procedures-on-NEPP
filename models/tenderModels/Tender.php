<?php

namespace app\models\tenderModels;

use app\models\Companies;
use Yii;

class Tender extends BaseModel
{

    public $id;
    public $title;
    public $title_en;
    public $titleRu;
    public $description;
    public $description_en;
    public $descriptionRu;
    public $procurementMethodRationale;
    public $cause;
    public $causeDescription;
    public $procurementMethod;
    public $procurementMethodType;
    public $tenderID;
    public $procuringEntity;    // class Organization
    public $value;              // class Value
    public $items;              // array of Item
    public $features;           // array of Feature
    public $documents;          // array of Document
    public $questions;          // array of Question
    public $complaints;         // array of Complaint
    public $bids;               // array of Bid
    public $minimalStep;        // class Value
    public $awards;             // array of Award
    public $contracts;          // array of Contract
    public $enquiryPeriod;      // class Period
    public $tenderPeriod;       // class Period
    public $auctionPeriod;      // class Period
    public $complaintPeriod;      // class Period
    public $auctionUrl;
    public $awardPeriod;        // class Period
    public $status;
    public $lots;               // array of Lot
    public $cancellations;      // array of Cancellation
    public $revisions;          // array of Revision
    public $qualifications;
    public $guarantee;
    public $qualificationPeriod;
    public $stage2TenderID;

    public function formName() {
        return 'Tender';
    }

//    public function emptyFields() {
//        return [
//            'items'         => '__EMPTY_ITEM__',
//            'lots'          => '__EMPTY_LOT__',
//            'documents'     => '__EMPTY_DOC__',
//            'features'      => '__EMPTY_FEATURE__',
//            'cancellations' => '__EMPTY_CANCEL__',
//        ];
//    }
//
//    public function zeroFields() {
//        return ['items','lots'];
//    }

    public function __construct($scenario='default')
    {
        $this->value           = new Value($scenario);
        $this->minimalStep     = new MinimalStepValue($scenario);
        $this->enquiryPeriod   = new EnquiryPeriod($scenario);
        $this->complaintPeriod   = new ComplaintPeriod($scenario);
        $this->tenderPeriod    = new Period($scenario);
        $this->qualificationPeriod    = new QualificationPeriod($scenario);
        $this->procuringEntity = new Organization($scenario);
        $this->guarantee       = new Guarantee ($scenario);
        $this->auctionPeriod       = new AuctionPeriod($scenario);

        $this->items           = ['iClass' => Item::className()];
        $this->documents       = ['iClass' => Document::className()];
        $this->features        = ['iClass' => Feature::className()];
        $this->lots            = ['iClass' => Lot::className()];
        $this->cancellations   = ['iClass' => Cancellation::className()];
        $this->questions       = ['iClass' => Question::className()];
        $this->awards          = ['iClass' => Award::className()];
        $this->bids            = ['iClass' => Bid::className()];
        $this->complaints      = ['iClass' => Complaint::className()];
        $this->contracts       = ['iClass' => Contract::className()];
        $this->qualifications  = ['iClass' => Qualifications::className()];

//        switch ($this->stage) {
//            case 'create':
//            case 'update':
//                $this->items['__EMPTY_ITEM__']           = new Item([], [], $this->stage);
//                $this->lots['__EMPTY_LOT__']             = new Lot([], [], $this->stage);
//                $this->documents['__EMPTY_DOC__']        = new Document([], [], $this->stage);
//                $this->features['__EMPTY_FEATURE__']     = new Feature([], [], $this->stage);
//                $this->cancellations['__EMPTY_CANCEL__'] = new Cancellation([], [], $this->stage);
//
//                if ($this->stage === 'create') {
//                    $this->no_empty_fields = [Document::className(), Feature::className(), Cancellation::className()];
//                } elseif ($this->stage === 'update') {
//                    $this->no_empty_fields = [Document::className(), Feature::className(), Cancellation::className(), Item::className(), Lot::className()];
//                }
//                break;
//            case 'load':
//                break;
//        }

        parent::__construct($scenario);
    }

    public function rules()
    {
        return [
            [['title'], 'required', 'on'=>'default', 'message'=>'Будь ласка, введіть назву закупівлі',
                'whenClient' => 'function (attribute, value) { return $(".tender_method_select").val() != "open_aboveThresholdUA.defense" && $(attribute.input).is(":visible") && !$(attribute.input).is(":disabled"); }',
                'when' => function ($model) {
                    $post = is_a(\Yii::$app, 'yii\console\Application') ? [] : \Yii::$app->request->post();
                    return !in_array($post['tender_method'], Yii::$app->params['2stage.tender']);
                },
            ],
            [['title'], 'string', 'max'=>255, 'on'=>'default', 'message'=>'Назва закупівлі не може складатися з більше ніж 255 символів'],

            [['title_en', 'titleRu', 'description', 'description_en', 'descriptionRu', 'procurementMethodRationale','procurementMethodType'], 'string', 'max'=>255, 'on'=>'default'],
            [['auctionUrl','status','tenderID','id'], 'safe', 'on'=>'default'],
            [['procurementMethod','stage2TenderID'], 'safe'],

            [['title', 'description','procurementMethod'], 'safe', 'on' => ['limitedavards','eu_prequalification']],

            // европейская процедура
            [['title_en','description_en'], 'required', 'when' => function ($model) {
                $post = is_a(\Yii::$app, 'yii\console\Application') ? [] : \Yii::$app->request->post();
                //@todo может тут нужно добавить еще европейский диалог?
                return $post['tender_method'] == 'open_aboveThresholdEU';
            },
                'whenClient' => 'function (attribute, value) { return $(".tender_method_select").val() != "open_aboveThresholdUA.defense" && $(attribute.input).is(":visible"); }'
            ],

            // переговорная процедура 5 и 10 дней
            [['cause','causeDescription'], 'required', 'when' => function ($model) {
                $post = \Yii::$app->request->post();
                return $post['tender_method'] == 'limited_negotiation' || $post['tender_method'] == 'limited_negotiation.quick';
            },
                'whenClient' => 'function (attribute, value) { return $(attribute.input).is(":visible"); }'
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => \Yii::t('app', 'ID'),
            'title' => \Yii::t('app', 'Назва тендера'),
            'title_en' => \Yii::t('app', 'Tender name'),
            'titleRu' => \Yii::t('app', 'Название тендера'),
            'description' => \Yii::t('app', 'Детальний опис закупiвлi'),
            'description_en' => \Yii::t('app', 'Tender description'),
            'descriptionRu' => \Yii::t('app', 'Детальное описание закупки'),
            'procurementMethodRationale'=> \Yii::t('app', 'Обгрунтування використання такого методу закупiвлi'),
            'tenderID' => \Yii::t('app', 'iдентифiкатор закупiвлi'),
            'procuringEntity' => \Yii::t('app', 'Органiзацiя, що проводить закупiвлю'),
            'value' => \Yii::t('app', 'Повний доступний бюджет закупiвлi'),
            'minimalStep' => \Yii::t('app', 'Мiнiмальний крок аукцiону (редукцiону)'),
            'enquiryPeriod' => \Yii::t('app', 'Перiод, коли дозволено задавати питання'),
            'tenderPeriod' => \Yii::t('app', 'Перiод, коли подаються пропозицiї'),
            'qualificationPeriod' => \Yii::t('app', 'Перiод, коли подаються скарги'),
            'auctionPeriod' => \Yii::t('app', 'Перiод, коли проводиться аукцiон'),
            'auctionUrl' => \Yii::t('app', 'Веб-адреса для перегляду аукцiону'),
            'awardPeriod' => \Yii::t('app', 'Перiод, коли вiдбувається визначення переможця'),
            'status' => \Yii::t('app', 'Статус Закупiвлi'),
            'cause' => \Yii::t('app', 'Пiдстава для використання'),
            'causeDescription' => \Yii::t('app', 'Обгрунтування використання такої процедури закупiвлi'),
        ];
    }

    public static function isCanCancel($tenders,&$tender,$lot=false){
        $tender = (array)$tender;
        $lot = $lot ? (array)$lot : false;
        if (!Companies::checkCompanyIsTenderOwner($tenders->id) || !\app\models\Tenders::CheckAllowedStatus($tenders->id, 'cancelation', $tenders)) return false;
        if($lot && $lot['status']!='active') return false;

        //проверяем на unsucesfull
        $return =true;
        return $return;
    }


}
