<?php

namespace app\models\tenderModels;

use app\models\DocumentType;
use Yii;

class Contract extends BaseModel
{
    public $id;
    public $awardID;
    public $title;
    public $description;
    public $status;
    public $period;             // class Period
    public $value;              // class Value
    public $dateSigned;
    public $contractNumber;
    public $documents;          // array of Document

    public function __construct($scenario='default')
    {
        $this->period = new ContractPeriod($scenario);
        $this->value  = new Value($scenario);

        $this->documents = ['iClass' => Document::className()];

        parent::__construct($scenario);
    }

    public function rules()
    {
        return [
            [['id'], 'safe'],
            [['awardID'], 'safe'],
            [['title'], 'safe'],
            [['description','contractNumber'], 'safe'],
            [['status'], 'safe'],
            [['dateSigned'], 'safe'],
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
        ];
    }

    public function getStatusDescr()
    {
        switch ($this->status) {
            case 'pending':
                return 'цей договiр запропоновано, але вiн ще не дiє';
            case 'active':
                return 'цей договiр пiдписаний всiма учасниками, i зараз дiє на законних пiдставах';
            case 'cancelled':
                return 'цей договiр було скасовано до пiдписання';
            case 'terminated':
                return 'цей договiр був пiдписаний та дiяв, але вже завершився';

            default:
                return 'undefined';
        }
    }
}