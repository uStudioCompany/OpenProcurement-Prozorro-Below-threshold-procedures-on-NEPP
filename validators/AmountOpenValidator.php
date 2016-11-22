<?php

namespace app\validators;

use yii\helpers\VarDumper;
use yii\validators\Validator;
use Yii;

class AmountOpenValidator extends Validator
{
    public function init()
    {
        parent::init();
        $this->message = [
            0 => \Yii::t('app', '"Очікувана вартість не може бути менше 3000"'),
        ];
    }

    public function validateAttribute($model, $attribute)
    {
        $post = Yii::$app->request->post();
        $tender = $post['Tender'];
        $tenderMethod = $post['tender_method'];
        $tenderType = $post['tender_type'];
        if (in_array($tenderMethod,['open_belowThreshold'])) {
            $tenderAmount = $model->$attribute;
            if ($tenderType == 2) {
                if ($tenderAmount < 3000) {
                    $model->addError($attribute, $this->message[0]);
                }
            }
        }
    }

    public function clientValidateAttribute($model, $attribute, $view)
    {
        return <<<JS
            if ($('.tender_method_select').val() == 'open_belowThreshold') {
                if ($('.tender_type_select').val() == 1) {
                    var tenderAmount = parseFloat($('.tender_full_amount').val());
                    if (tenderAmount < 3000) {
                        messages.push({$this->message[0]});
                    }
                }
            }
JS;
    }
}