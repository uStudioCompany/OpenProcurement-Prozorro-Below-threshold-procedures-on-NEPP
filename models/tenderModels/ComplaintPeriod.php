<?php

namespace app\models\tenderModels;

class ComplaintPeriod extends BaseModel
{
    public $startDate;  // Формат даты: ISO 8601.
    public $endDate;

    public function rules()
    {
        return [
            [['startDate', 'endDate'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'startDate' => \Yii::t('app', 'Дата початку перiоду, коли дозволено задавати скарги на умови закупiвлi'),
            'endDate' => \Yii::t('app', 'Дата кiнця перiоду, коли дозволено задавати скарги на умови закупiвлi'),
        ];
    }
//    public function validateDates(){
//        if(strtotime($this->end_date) <= strtotime($this->start_date)){
//            $this->addError('start_date','Please give correct Start and End dates');
//            $this->addError('end_date','Please give correct Start and End dates');
//        }
//    }
//    public function validateDate($attribute, $params)
//    {
//        $speakStart = strtotime($this->startDate);
//        $speakEnd = strtotime($this->endDate);
//
//        if (trim($speakStart) != '') {
//            if ($speakEnd <= $speakStart) {
//                $this->addError($attribute, 'Дата окончания обсуждения не может быть меньше или равной дате начала обсуждения.'.$speakStart);
//            }
//        }
//
//
//
//
//    }
//    public function validateDate($attribute, $params)
//    {
//
//        $deliveryEnd = strtotime($_POST['Tender']['items'][0]['deliveryDate']['endDate']);
//
//        $propositionStart = strtotime($_POST['Tender']['tenderPeriod']['startDate']);
//        $propositionEnd = strtotime($_POST['Tender']['tenderPeriod']['endDate']);
//
//        $speakStart = strtotime($_POST['Tender']['enquiryPeriod']['startDate']);
//        $speakEnd = strtotime($_POST['Tender']['enquiryPeriod']['endDate']);
//
//        if (trim($speakStart) != '') {
//            if ($speakEnd <= $speakStart) {
//                $this->addError($attribute, 'Дата окончания обсуждения не может быть меньше или равной дате начала обсуждения.'.$speakStart);
//            }
//        }
//
//            if ($propositionEnd <= $speakEnd) {
//                $this->addError($attribute, 'Дата окончания подачи предложений не может быть меньше или равной дате окончания обсуждения.'.$speakEnd.'---'.$propositionEnd);
//            }
//
//
//
//    }
}
