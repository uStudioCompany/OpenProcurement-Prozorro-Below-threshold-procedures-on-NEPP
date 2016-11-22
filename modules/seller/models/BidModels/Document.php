<?php

namespace app\modules\seller\models\BidModels;


class Document extends BaseModel
{
    public $id;
    public $documentType;             
    public $title;            
    public $description; 
    public $format;                       
    public $url;
    public $datePublished;               
    public $dateModified;            
    public $language;           
    public $documentOf;
    public $relatedItem;
    public $realName;
    public $confidentiality;
    public $confidentialityRationale;
    public $is_old;

    public function rules()
    {
        return [
            [['id'], 'safe'],
            [['documentType'], 'safe'],
            [['title'], 'safe'],
            [['description'], 'safe'],
            [['format'], 'safe'],
            [['url'], 'safe'],
            [['datePublished'], 'safe'],
            [['dateModified'], 'safe'],
            [['language'], 'safe'],
            [['documentOf'], 'safe'],
            [['relatedItem','realName','is_old'], 'safe'],


            [['confidentiality',], 'safe'],
            [['confidentialityRationale'], 'required', 'whenClient' => 'function (attribute, value) { return $(attribute.input).is(":visible"); }'],
            [['confidentialityRationale'], 'string', 'min'=>30],

        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => \Yii::t('app', 'ID'),
            'title' => \Yii::t('app', 'Назва документа'),
            'description' => \Yii::t('app', 'Короткий опис документа'),
            'format' => \Yii::t('app', 'Формат документа'),
            'url' => \Yii::t('app', 'Пряме посилання на документ'),
            'datePublished' => \Yii::t('app', 'Дата, коли документ був опублiкований вперше'),
            'dateModified' => \Yii::t('app', 'Дата, коли документ був змiнений востаннє'),
            'language' => \Yii::t('app', 'Мова документа'),
            'confidentialityRationale' => \Yii::t('app', 'confidentialityRationale'),
        ];
    }

    public function getDocumentTypeDescr($valueFor)
    {
        switch ($valueFor) {
            case 'Tender':
                switch ($this->documentType) {
                    case 'notice':
                        return 'Повiдомлення про закупiвлю';
                    case 'biddingDocuments':
                        return 'Документи закупiвлi';
                    case 'technicalSpecifications':
                        return 'Технiчнi специфiкацiї';
                    case 'evaluationCriteria':
                        return 'Критерiї оцiнки';
                    case 'clarifications':
                        return 'Пояснення до питань заданих учасниками';
                    case 'eligibilityCriteria':
                        return 'Критерiї прийнятностi';
                    case 'shortlistedFirms':
                        return 'Фiрми у короткому списку';
                    case 'riskProvisions':
                        return 'Положення для управлiння ризиками та зобов’язаннями';
                    case 'billOfQuantity':
                        return 'Кошторис';
                    case 'bidders':
                        return 'iнформацiя про учасникiв';
                    case 'conflictOfInterest':
                        return 'Виявленi конфлiкти iнтересiв';
                    case 'debarments':
                        return 'Недопущення до закупiвлi';
                    
                    default:
                        return 'undefined';
                }
            case 'Award':
                switch ($this->documentType) {
                    case 'notice':
                        return 'Повiдомлення про рiшення';
                    case 'evaluationReports':
                        return 'Звiт про оцiнку';
                    case 'winningBid':
                        return 'Пропозицiя, що перемогла';
                    case 'complaints':
                        return 'Скарги та рiшення';
                    
                    default:
                        return 'undefined';
                }
            case 'Contract':
                switch ($this->documentType) {
                    case 'notice':
                        return 'Повiдомлення про договiр';
                    case 'contractSigned':
                        return 'Пiдписаний договiр';
                    case 'contractArrangements':
                        return 'Заходи для припинення договору';
                    case 'contractSchedule':
                        return 'Розклад та етапи';
                    case 'contractAnnexe':
                        return 'Додатки до договору';
                    case 'contractGuarantees':
                        return 'Гарантiї';
                    case 'subContract':
                        return 'Субпiдряд';
                    
                    default:
                        return 'undefined';
                }    

            default:
                return 'undefined';
        }
    }
}
