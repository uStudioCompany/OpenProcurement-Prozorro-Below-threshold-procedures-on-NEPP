<?php

namespace app\models\contractModels;

use app\models\DocumentUploadTask;
use Yii;

class Contract extends BaseModel
{
    public $id;
    public $status;
    public $documents;          // array of Document
    public $tender_id;
    public $items;
    public $suppliers;
    public $contractNumber;
    public $period;             // class Period
    public $value;              // class Value
    public $dateModified;
    public $procuringEntity;
    public $dateSigned;
    public $owner;
    public $awardID;
    public $contractID;
    public $changes;
    public $amountPaid;
    public $terminationDetails;
    public $terminateType;



    public function __construct($scenario = 'default')
    {

        $this->documents = ['iClass' => Document::className()];
        $this->items = ['iClass' => Item::className()];
        $this->period = new Period($scenario);
        $this->value = new Value($scenario);
        $this->amountPaid = new Value($scenario);
        $this->suppliers = ['iClass' => Organization::className()];
        $this->changes = ['iClass' => Changes::className()];
        $this->procuringEntity = new Organization($scenario);

        parent::__construct($scenario);
    }

    public function rules()
    {
        return [
            [['id'], 'safe'],
            [['terminationDetails'], 'required', 'whenClient' => 'function (attribute, value) { return $(attribute.input).is(":visible"); }'],
            [['awardID'], 'safe'],
            [['status'], 'safe'],
            [['tender_id', 'contractNumber', 'dateModified'], 'safe'],
            [['dateSigned', 'owner', 'awardID', 'contractID', 'terminationDetails'], 'safe'],
            [['terminateType'], 'required'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => \Yii::t('app', ' iдентифiкатор цього договору'),
            'awardID' => \Yii::t('app', 'ID рiшення, згiдно якого видається договiр'),
            'title' => \Yii::t('app', 'Назва договору'),
            'description' => \Yii::t('app', 'Опис договору'),
            'status' => \Yii::t('app', 'Поточний статус договору'),
            'period' => \Yii::t('app', 'Дата початку та завершення договору'),
            'value' => \Yii::t('app', 'Загальна вартiсть договору'),
            'documents' => \Yii::t('app', "документи та додатки пов\'язанi з договором"),
            'dateSigned' => \Yii::t('app', 'Дата пiдписання договору'),
            'contractNumber' => \Yii::t('app', 'Номер договору'),
            'terminationDetails' => \Yii::t('app', 'Причини розірвання договору'),
        ];
    }


    /**
     * Причини додання змін до договору
     */
    public static function getContractChangesValue()
    {

        return [
            'volumeCuts' => Yii::t('app', 'Зменшення обсягів закупівлі'),
            'itemPriceVariation' => Yii::t('app', 'Зміна ціни за одиницю товару'),
            'qualityImprovement' => Yii::t('app', 'Покращення якості предмета закупівлі'),
            'durationExtension' => Yii::t('app', 'Продовження строку дії договору (через документально підтверджені об’єктивні обставини)'),
            'priceReduction' => Yii::t('app', 'Узгоджене зменшення ціни'),
            'taxRate' => Yii::t('app', 'Зміна ціни у зв’язку із зміною ставок податків і зборів'),
            'thirdParty' => Yii::t('app', 'Зміна сторонніх показників (курсу, тарифів...)'),
            'fiscalYearExtension' => Yii::t('app', 'Продовження строку дії договору на наступний рік')
        ];

    }

    public static function getContractUploadedDocument($contractId)
    {
        return DocumentUploadTask::find()
            ->where(['type'=>'contracting', 'status'=> '0', 'transaction_id'=>''])
            ->andFilterWhere(['tid'=>$contractId])
            ->count();
    }

}