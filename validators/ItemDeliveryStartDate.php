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

class ItemDeliveryStartDate extends Validator
{
    public function init()
    {
        parent::init();
        $this->message = \Yii::t('app', '"Строк початку поставки не може бути бiльше нiж кінцева дата поставки"');
    }

    public function validateAttribute($model, $attribute)
    {
        $post = Yii::$app->request->post();
        if(!in_array($post['tender_method'], ['limited_reporting','limited_negotiation','limited_negotiation.quick'])) {
            $deliveryStartDate = strtotime(date('c', strtotime(str_replace('/', '.', $model->$attribute))));
            $deliveryEndDate = strtotime(date('c', strtotime(str_replace('/', '.', $model->endDate))));
            if ($deliveryStartDate > $deliveryEndDate) {
                $model->addError($attribute, $this->message);
            }
        }
    }

    public function clientValidateAttribute($model, $attribute, $view)
    {
        return <<<JS
        if ($(attribute.input).is(':visible')) {
            var item_delivery_start_date = ComareDateFormat($(attribute.input).val());
            var item_delivery_end_date = ComareDateFormat($(attribute.input).closest('.form-group').next('div').find('input').val());
        }
        if (item_delivery_start_date > item_delivery_end_date) {
            messages.push($this->message);
        }
JS;
    }
}