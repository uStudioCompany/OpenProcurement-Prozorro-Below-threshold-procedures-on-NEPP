<?php
/**
 * Created by PhpStorm.
 * User: Oleg
 * Date: 14.01.2016
 * Time: 22:58
 */

namespace app\validators;

use yii\validators\Validator;
use app\models\Status;
use Yii;

class EnquiryStartPeriodValidator extends Validator
{
    public function init()
    {
        parent::init();
        $this->message = [
            0=>\Yii::t('app','"Дата початку обговорення не може бути більше або дорівнювати даті кінця обговорення"'),
        ];
    }

    public function validateAttribute($model, $attribute)
    {
        $post = Yii::$app->request->post();

        $enquiryPeriodStartDate = strtotime(date('c', strtotime( str_replace('/','.',$model->$attribute) )));
        $enquiryPeriodEndDate = strtotime(date('c', strtotime( str_replace('/','.',$post['Tender']['enquiryPeriod']['endDate']) )));

        if($enquiryPeriodStartDate >= $enquiryPeriodEndDate){
            $model->addError($attribute, $this->message[0]);
        }

    }

    public function clientValidateAttribute($model, $attribute, $view)
    {

        return <<<JS
            var enquiryperiod_startdate = ComareDateFormat($('#enquiryperiod-startdate').val());
            var enquiryperiod_enddate = ComareDateFormat($('#enquiryperiod-enddate').val());

            if (enquiryperiod_startdate >= enquiryperiod_enddate) {
                messages.push({$this->message[0]});
            }

JS;
    }
}