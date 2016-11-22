<?php

namespace app\modules\seller\models\BidModels;



use app\modules\seller\validators\SubcontractingDetailsValidator;

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
    public $_counted_history;
    public $eligibilityDocuments;
    public $financialDocuments;
    public $qualificationDocuments;
    public $subcontractingDetails;
    public $selfEligible;
    public $selfQualified;

    public function __construct($scenario='default')
    {

        $this->value      = new Value($this->scenario);

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


            [['selfEligible','selfQualified'], 'required', 'requiredValue' => 1, 'message' => \Yii::t('app', 'need confirm'),'whenClient' => 'function (attribute, value) { return $(attribute.input).is(":visible"); }'],
            [['subcontractingDetails'], 'string', 'max'=>300],
//            [['subcontractingDetails'], SubcontractingDetailsValidator::className()],

        ];
    }

    public function attributeLabels()
    {
        return [
            'participationUrl' => \Yii::t('app','Веб-адреса для участi в аукцiонi'),
            'subcontractingDetails' => \Yii::t('app', 'Subcontracting Details'),
            'selfEligible' => \Yii::t('app','selfEligible'),
            'selfQualified' => \Yii::t('app','selfQualified'),

        ];
    }

    public function getStatusDescr()
    {
        if(in_array($this->status, ['registration', 'validBid', 'invalidBid'])) {
            return \Yii::t('app', $this->status);
        } 
        
        return \Yii::t('app', 'undefined_bid');
    }
}
