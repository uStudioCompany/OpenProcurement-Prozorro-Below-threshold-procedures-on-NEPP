<?php

namespace app\models\tenderModels;

use app\models\tenderModels\BaseModel;
use app\models\tenderModels\Value;
use app\models\tenderModels\Period;

class Lot extends BaseModel
{
    public $id;
    public $title;
    public $title_en;
    public $description;
    public $description_en;
    public $value;              // class Value
    public $minimalStep;        // class Value
    public $auctionPeriod;      // class Period
    public $auctionUrl;
    public $status;
    public $guarantee;

    public function __construct($scenario='default')
    {
        $this->value         = new ValueLot($scenario);
        $this->minimalStep   = new MinimalStepValue($scenario);
        $this->auctionPeriod = new AuctionPeriod($scenario);
        $this->guarantee     = new Guarantee($scenario);

        parent::__construct($scenario);
    }

    public function rules()
    {
        $post = is_a(\Yii::$app, 'yii\console\Application') ? [] : \Yii::$app->request->post();
        return [
            [['id'], 'safe'],
            [['title','description'], 'required','whenClient' => 'function (attribute, value) { return $(attribute.input).is(":visible"); }'],
            [['title_en'], 'required','whenClient' => 'function (attribute, value) { return $(".tender_method_select").val() != "open_aboveThresholdUA.defense" && $(attribute.input).is(":visible"); }', 'when'=>function($model) {return isset($post['tender_method']) ? $post['tender_method'] == 'open_aboveThresholdEU' : false;}],
            [['description_en'], 'string', 'max'=>500],
            [['status'], 'safe'],
            [['auctionUrl'], 'safe','except'=>'eu_prequalification'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'title' => \Yii::t('app','Назва лота закупiвлi'),
            'description' => \Yii::t('app', 'Детальний опис лота закупiвлi'),
            'title_en' => \Yii::t('app','Lot title en'),
            'description_en' => \Yii::t('app', 'Lot description en'),
            'status' => \Yii::t('app', 'Статус лота'),
            'auctionPeriod' => \Yii::t('app', 'Перiод проведення аукцiону'),
            'value' => \Yii::t('app', 'Повний доступний бюджет лота закупiвлi'),
            'minimalStep' => \Yii::t('app', "Мiнiмальний крок аукцiону (редукцiону)"),
            'auctionUrl' => \Yii::t('app', 'Веб-адреса для перегляду аукцiону'),
        ];
    }

    public function getStatusDescr()
    {
        switch ($this->status) {
            case 'unsuccessful':
                return 'Неуспiшна закупiвля (не вiдбулась)';
            case 'active':
                return 'Перiод уточнень (уточнення)';
            case 'complete':
                return 'Завершено лот закупiвлi (завершено)';
            case 'cancelled':
                return 'Скасовано лот закупiвлi (скасовано)';

            default:
                return 'undefined';
        }
    }

    public static function getLotById($tender, $id){
        $tender = (array)$tender;
        foreach ($tender['lots'] as $k=>$lot) {
            if($lot['id'] == $id){
                return $lot;
            }
        }
    }
}
