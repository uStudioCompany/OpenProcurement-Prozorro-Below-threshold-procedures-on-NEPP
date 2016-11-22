<?php

namespace app\models\tenderModels;

use app\models\tenderModels\BaseModel;
use app\models\tenderModels\Organization;
use app\models\tenderModels\Value;
use app\models\tenderModels\Document;
use app\models\tenderModels\Parameter;
use app\models\tenderModels\lotValue;
use yii\helpers\VarDumper;

class Bid extends BaseModel
{
    public $tenderers;      // array of Organization
    public $date;                  
    public $id; 
    public $status;
    public $value;          // class Value
    public $documents;      // array of Document
    public $parameters;     // array of Parameter
    public $lotValues;      // array of lotValue
    public $participationUrl;
    public $_counted_amount;
    public $_counted_num;
    public $_counted_history;
    public $eligibilityDocuments;
    public $financialDocuments;
    public $qualificationDocuments;
    public $subcontractingDetails;

    public function __construct($scenario='default')
    {
        $this->value      = new Value($scenario);

        $this->tenderers  = ['iClass' => Organization::className()];
        $this->documents  = ['iClass' => Document::className()];
        $this->eligibilityDocuments  = ['iClass' => Document::className()];
        $this->financialDocuments  = ['iClass' => Document::className()];
        $this->qualificationDocuments  = ['iClass' => Document::className()];
        $this->parameters = ['iClass' => Parameter::className()];
        $this->lotValues  = ['iClass' => lotValue::className()];

        parent::__construct($scenario);
    }


    public function rules()
    {
        return [
            [['date'], 'safe'],
            [['id'], 'safe'],
            [['status'], 'safe'],
            [['participationUrl'], 'safe'],
            [['subcontractingDetails'], 'safe'],

            [['id'], 'safe','on'=>'eu_prequalification']
        ];
    }

    public function attributeLabels()
    {
        return [
            'participationUrl' => \Yii::t('app','Веб-адреса для участi в аукцiонi'),
        ];
    }

    public function getStatusDescr()
    {
        switch ($this->status) {
            case 'registration':
                return 'реєстрацiя';
            case 'validBid':
                return 'дiйсна пропозицiя';
            case 'invalidBid':
                return 'недiйсна пропозицiя';
            
            default:
                return 'undefined';
        }
    }

    public static function getTenderBid($bidId, $tender){
        if(isset($tender->bids)){
            foreach ($tender->bids as $b=>$bid){
                if($bid->id == $bidId){
                    return $bid;
                }
            }
        }
    }

    public static function getTextActiveStatusBid($tender, $bid, $type)
    {
        if ($type == 2) {
            $awardStatus = [];
            $lotsId = [];
            foreach ($tender->lots as $lot) {
                $lotsId[] = $lot->id;
            }
            $bidsInLot = [];
            foreach ($tender->bids as $tenderBid) {
                foreach ($lotsId as $lotId) {
                    foreach ($tenderBid->lotValues as $lotValue) {
                        if ($lotValue->relatedLot == $lotId) {
                            $bidsInLot[$lotId][] = $tenderBid;
                        }
                    }
                }
            }
            foreach ($bidsInLot as $tenderBids) {
                foreach ($tenderBids as $tenderBid) {
                    foreach ($tender->awards as $tenderAward) {
                        if ($tenderBid->id == $tenderAward->bid_id && $tenderAward->status == 'active') {
                            $awardStatus[$tenderAward->lotID] = 'active';
                            break(2);
                        }
                    }
                }
            }
            $text = [];
            foreach ($awardStatus as $key => $item) {
                foreach ($bid->lotValues as $lotValue) {
                    if ($lotValue->relatedLot == $key) {
                        $text[$bid->id] = $item;
                    }
                }
            }
            return $text;
        }
        if ($type == 1){
            foreach ($tender->awards as $award) {
                if ($award->status == 'active') {
                    return 'active';
                }
            }
        }
    }
}
