<?php

namespace app\models\tenderModels;

use Yii;

class Cancellation extends BaseModel
{
    public $id;
    public $reason;
    public $status;
    public $documents;          // array of Document
    public $date;
    public $cancellationOf;
    public $relatedLot;
    public $reasonType;


    public function __construct($scenario='default')
    {
        $this->documents = ['iClass' => Document::className()];

//        switch ($this->stage) {
//            case 'create':
//            case 'update':
//                $this->documents['__EMPTY_DOC__'] = new Document([], [], $this->stage);
//                $this->no_empty_fields = [Document::className()];
//                break;
//            case 'load':
//                break;
//        }

        parent::__construct($scenario);
    }

    public function rules()
    {
        return [
            [['reason'], 'required', 'whenClient' => 'function (attribute, value) { return $(attribute.input).is(":visible"); }'],
            [['reason','reasonType','id','status','cancellationOf','relatedLot'], 'string'],
            [['relatedLot'], 'required'],
            [['date'], 'match', 'pattern' => '/^\d{2}\/\d{2}\/\d{4}$/i'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'reason' => Yii::t('app','Причина, з якої скасовується закупiвля'),
            'date' => Yii::t('app','Дата скасування'),
            'documents' => Yii::t('app',"Супровiдна документацiя скасування"),
            'relatedLot' => Yii::t('app',"Обьект скасування"),
        ];
    }

    public function getStatusDescr()
    {
        switch ($this->status) {
            case 'pending':
                return 'Стандартно. Запит оформляється';
            case 'active':
                return 'Скасування активоване';

            default:
                return 'undefined';
        }
    }

    public static function getReasonType(){
        return [
            'cancelled'=>Yii::t('app','Торги були відмінені.'),
            'unsuccessful'=>Yii::t('app','Торги не відбулися.'),
        ];
    }
}
