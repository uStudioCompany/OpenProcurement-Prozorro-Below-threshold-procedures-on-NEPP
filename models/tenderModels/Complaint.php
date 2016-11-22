<?php

namespace app\models\tenderModels;


class Complaint extends BaseModel
{
    public $id;
    public $author;         // class Organization            
    public $title; 
    public $description;
    public $date;
    public $status;
    public $resolution;
    public $resolutionType;
    public $relatedLot;
    public $documents;      // array of Document
    public $tendererAction;
    public $cancellationReason;
    public $dateCanceled;
    public $dateAnswered;
    public $satisfied;


    public function __construct($scenario='default')
    {
        $this->author    = new Organization($scenario);

        $this->documents = ['iClass' => Document::className()];

        parent::__construct($scenario);
    }

    public function rules()
    {
        return [
            [['title','description'], 'required'],
            [['description'], 'string', 'min'=>30],
            [['id', 'satisfied'], 'safe'],
            [['relatedLot', 'tendererAction','dateAnswered'], 'safe'],
            [['tendererAction'], 'string', 'min'=>30, 'whenClient' => 'function (attribute, value) { return $(attribute.input).is(":visible"); }'],
            [['date','dateCanceled'], 'safe'],
            [['status'], 'safe'],
//            [['resolution'], 'safe'],
            [['cancellationReason'], 'required', 'whenClient' => 'function (attribute, value) { return $(attribute.input).is(":visible"); }'],
            [['cancellationReason'], 'string','min'=>30, 'whenClient' => 'function (attribute, value) { return $(attribute.input).is(":visible"); }'],

            [['resolution','resolutionType'], 'required', 'whenClient' => 'function (attribute, value) { return $(attribute.input).is(":visible"); }'],
            [['resolution'], 'string','min'=>30, 'whenClient' => 'function (attribute, value) { return $(attribute.input).is(":visible"); }'],

            [['title'], 'safe', 'on'=>'eu_prequalification'],
            [['tendererAction'], 'required', 'on'=>'eu_prequalification'],
            [['tendererAction'], 'string', 'min'=>30, 'on'=>'eu_prequalification'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'title' => \Yii::t('app', 'Заголовок'),
            'description' => \Yii::t('app', 'Опис Скарги/Вимоги'),
            'date' => \Yii::t('app', 'Дата подання'),
            'tendererAction' => \Yii::t('app', 'Вiдповiдь на рiшення'),
            'relatedLot' => \Yii::t('app', 'Вiдноситься до'),
            'cancellationReason' => \Yii::t('app', 'Причина скасування'),
            'resolution' => \Yii::t('app', 'Вiдповiдь'),
            'resolutionType'=>\Yii::t('app','Тип вiдповiдi')
        ];
    }

    /**
     * @param $tender
     * @return array
     * к чему привязана жалоба
     */


    public static function getSellerRelatedLot($tender)
    {

        $items = [];
        $items['tender'] = \Yii::t('app', 'Тендеру');

        if (count($tender->lots)) {
            foreach ($tender->lots as $k => $val) {
                if ($k === '__EMPTY_LOT__') continue;
                if ($val['id']) {
                    $items['lot_' . $val['id']] = \Yii::t('app', 'Лоту') . '-' . $val['title'];
                }
            }
        }
        return $items;


    }
}
