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

class SummValidator extends Validator
{
    public function init()
    {
        parent::init();
        $this->message = [
            0 => \Yii::t('app', '"Будь ласка, введіть коректне значення кроку пониження ціни (не може бути більше розміру бюджета закупівлі)"'),
            1 => \Yii::t('app', '"Будь ласка, введіть коректне значення кроку пониження ціни (повинно складати від 0.5% до 3% розміру бюджета закупівлі)"'),
        ];
//        $this->message = \Yii::t('app', '"Мінімальний крок аукціону бiльше нiж повний доступний бюджет закупівлі"');
    }

    public function validateAttribute($model, $attribute)
    {
        $post = Yii::$app->request->post();
        $minimalStep = $model->$attribute;
        $tenderAmount = $post['Tender']['value']['amount'];

        if ($minimalStep > $tenderAmount) {
            $model->addError($attribute, $this->message[0]);
        }
    }

    public function clientValidateAttribute($model, $attribute, $view)
    {
        return <<<JS
            var tenderStepAmount =  $(attribute.input).val();

            if ($('.tender_type_select').val() == 1){
                var tenderAmount = $('.tender_full_amount').val();
                var procent = parseFloat(tenderAmount) / 100;
                procent = parseFloat(tenderStepAmount) / procent;

            }else if ($('.tender_type_select').val() == 2){
                var tenderAmount = $(attribute.input).closest('.lot_amount_block').find('.lot_amount').val();
                var procent = parseFloat(tenderAmount) / 100;
                procent = parseFloat(tenderStepAmount) / procent;
            }
            //если шаг больше суммы тендера или лота
            if (parseFloat(tenderStepAmount) > parseFloat(tenderAmount)) {
                messages.push({$this->message[0]});
            }

            //шаг должен быть в пределах 0,5-3%, если допороговая закупка
            if ($('.tender_method_select').val() == 'open_belowThreshold') {
                if(procent < 0.5 || procent > 3){
                    messages.push({$this->message[1]});
                }
            }
JS;
    }
}