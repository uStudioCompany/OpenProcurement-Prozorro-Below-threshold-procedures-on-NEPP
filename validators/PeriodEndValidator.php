<?php
/**
 * Created by PhpStorm.
 * User: Oleg
 * Date: 14.01.2016
 * Time: 22:58
 */

namespace app\validators;

use yii\validators\Validator;
use Yii;
use app\components\ApiHelper;

class PeriodEndValidator extends Validator
{
    public function init()
    {
        parent::init();
        $this->message = [
            0 => \Yii::t('app', '"Кінцева дата подання пропозицій не може бути більше дати початку подачі пропозицій"')
        ];
    }

    public function validateAttribute($model, $attribute)
    {
//        $post = Yii::$app->request->post();
//        ApiHelper::FormatDate($post, true);
//        if(isset($post['Tender']['tenderPeriod']['startDate']) && $post['tender_method'] == 'open_belowThreshold') {
//            $periodStartDate = strtotime($post['Tender']['tenderPeriod']['startDate']);
//            $periodEndDate = strtotime($post['Tender']['tenderPeriod']['endDate']);
//            if ($periodStartDate >= $periodEndDate) {
//                $model->addError($attribute, $this->message[0]);
//            }
//        }

    }

    public function clientValidateAttribute($model, $attribute, $view)
    {
        return <<<JS
            var period_startdate = ComareDateFormat($('#period-startdate:visible').val());
            var period_enddate = ComareDateFormat($('#period-enddate:visible').val());
            if (period_startdate >= period_enddate) {
                // messages.push({$this->message[0]});
            }
JS;
    }
}