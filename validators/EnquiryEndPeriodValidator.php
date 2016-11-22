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

class EnquiryEndPeriodValidator extends Validator
{
    public function init()
    {
        parent::init();
        $this->message = [
            0=>\Yii::t('app','"Дата закінчення обговорення не може бути менше або дорівнювати даті початку обговорення"'),
            1=>\Yii::t('app','"Дата закінчення обговорення не може бути більше дати початку подачі пропозицій"'),
            2=>\Yii::t('app','"Перiод обговорення не може бути меньше однiєї доби"'),
            3=>\Yii::t('app','"Перiод обговорення не може бути меньше трьох дiб"')
        ];
    }

    public function validateAttribute($model, $attribute)
    {
//        $post = Yii::$app->request->post();
//        $enquiryPeriodStartDate = strtotime(date('c', strtotime( str_replace('/','.',$post['Tender']['enquiryPeriod']['startDate']) )));
//        $enquiryPeriodEndDate = strtotime(date('c', strtotime( str_replace('/','.',$model->$attribute) )));
//        $tenderPeriodStartDate = strtotime(date('c', strtotime( str_replace('/','.',$post['Tender']['tenderPeriod']['startDate']) )));
//
//        if($enquiryPeriodStartDate > $enquiryPeriodEndDate){
//            $model->addError($attribute, $this->message[0]);
//        }
//
//        if($enquiryPeriodEndDate > $tenderPeriodStartDate){
//            $model->addError($attribute, $this->message[1]);
//        }
    }

    public function clientValidateAttribute($model, $attribute, $view)
    {

        return <<<JS
            var enquiryperiod_startdate = ComareDateFormat($('#enquiryperiod-startdate').val());
            var enquiryperiod_enddate = ComareDateFormat($('#enquiryperiod-enddate').val());
            var period_startdate = ComareDateFormat($('#period-startdate').val());
            if (enquiryperiod_startdate >= enquiryperiod_enddate) {
                // messages.push({$this-> message[0]});
            }
            if (enquiryperiod_enddate > period_startdate) {
                // messages.push({$this-> message[1]});
            }

            //В диапазоне от 3 до 50 тыс минимум 1 день на вопросы и 1 день на подачу предложений
            var tenderAmount = parseFloat($('.tender_full_amount').val());
            if (enquiryperiod_startdate === undefined || enquiryperiod_startdate == '') {
                enquiryperiod_startdate = new Date();
            }

            var period_start_date_plus_one = new Date();
            period_start_date_plus_one.setDate(enquiryperiod_startdate.getDate() + 1);

            var period_start_date_plus_tree = new Date();
            period_start_date_plus_tree.setDate(enquiryperiod_startdate.getDate() + 3);

            if ($('.tender_method_select').val() == 'open_belowThreshold') {
                if ($('.tender_type_select').val() == 1) {
                    if (tenderAmount > 3000 && tenderAmount < 50000) {
                        if (period_start_date_plus_one >= enquiryperiod_enddate) {
                            //messages.push({$this->message[2]});
                        }
                    } else if (tenderAmount >= 50000) {//> 50000 - 3 дня
                        if (period_start_date_plus_tree >= enquiryperiod_enddate) {
                            // messages.push({$this->message[3]});
                        }
                    }
                } else if ($('.tender_type_select').val() == 2) {
                    $('.lot_amount_block .lot_amount:visible').each(function () {
                        var lotAmount = $(this).val();
                        if (lotAmount > 3000 && lotAmount < 50000) {
                            if (period_start_date_plus_one >= enquiryperiod_enddate) {
                                // messages.push({$this->message[2]});
                            }
                        } else if (lotAmount >= 50000) {
                            if (period_start_date_plus_tree >= enquiryperiod_enddate) {
                               // messages.push({$this->message[3]});
                            }
                        }
                    })

                }

            }
            
JS;
    }
}