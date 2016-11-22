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

class ItemDeliveryEndDate extends Validator
{
    public function init()
    {
        parent::init();
        $this->message = \Yii::t('app', '"Кінцева дата поставки не може починатися раніше ніж кінцева дата подачі пропозицій"');
    }

    public function validateAttribute($model, $attribute)
    {
        $post = Yii::$app->request->post();
        if(!in_array($post['tender_method'], ['limited_reporting','limited_negotiation','limited_negotiation.quick'])) {
            $deliveryEndDate = strtotime(date('c', strtotime(str_replace('/', '.', $model->$attribute))));
            $periodEndDate = strtotime(date('c', strtotime(str_replace('/', '.', $post['Tender']['tenderPeriod']['endDate']))));
            if ($periodEndDate > $deliveryEndDate) {
                $model->addError($attribute, $this->message);
            }
        }
    }

    public function clientValidateAttribute($model, $attribute, $view)
    {
        return <<<JS
            if($('#period-enddate').is(':visible')){
                var item_delivery_end_date = ComareDateFormat($(attribute.input).val());
                var period_enddate = ComareDateFormat($('#period-enddate').val());
                if (period_enddate > item_delivery_end_date) {
                    messages.push($this->message);
                }
            }
JS;
    }
}