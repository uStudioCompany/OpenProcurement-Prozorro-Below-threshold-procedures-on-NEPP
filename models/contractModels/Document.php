<?php

namespace app\models\contractModels;


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
        ];
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
}
