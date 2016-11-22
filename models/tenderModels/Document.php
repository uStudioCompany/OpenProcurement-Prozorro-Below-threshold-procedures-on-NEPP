<?php

namespace app\models\tenderModels;

use app\models\tenderModels\BaseModel;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

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
    public $is_old;
    public $author;

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
            [['relatedItem', 'realName', 'is_old'], 'safe'],
            [['author'], 'safe'],

            [['title'], 'safe', 'on' => ['limitedavards']],
            [['id'], 'safe', 'on' => 'eu_prequalification'],
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
            'author' => \Yii::t('app', 'Автор документа'),
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

    public static function getLastVersionDocuments($documents)
    {

        if (empty($documents) || !is_array($documents)) return array();
        $return = array();
        $ids = array();//id=>num

        foreach ($documents as $k => $v) {
            $ids[@$v['id'] ?: $k][] = $v;
        }
        foreach ($ids as $k => $v) {
            $return = array_merge($return, array_reverse($v));
        }

        //формируем массив из последних версий файлов.
        $fileId = '';
        $realDocuments = [];

        foreach ($return as $d => $document) {
            if ($document->id != '') {
                if ($fileId == $document->id) {
                    $document->is_old = 1;
                } else {
                    $document->is_old = 0;
                }

                $realDocuments[] = $document;
                $fileId = $document->id;
            }
        }

        return $realDocuments;
    }


    /**Return array only with latest
     *
     * @param $documents
     * @return array
     */
    public static function getLatestDocuments($documents)
    {
        if (empty($documents) || !is_array($documents)) return array();
        $return = [];
        $ids = [];
        foreach ($documents as $k => $v) {
            $ids[@$v['id'] ?: $k][] = $v;
        }
        foreach ($ids as $k => $v) {
            $return = array_merge($return, array_reverse($v));
        }
        //формируем массив из последних версий файлов.
        $fileId = '';
        $newDocs = [];
        foreach ($return as $d => $document) {
            if ($document['id'] != '' && $document['id'] != '__EMPTY_DOC__') {
                if ($fileId != $document['id']) {
                    $newDocs[] = $document;
                }
                $fileId = $document['id'];
            }
        }
        return $newDocs;
    }
}
