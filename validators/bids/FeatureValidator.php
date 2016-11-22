<?php
/**
 * Created by PhpStorm.
 * User: Oleg
 * Date: 14.01.2016
 * Time: 22:58
 */

namespace app\validators\bids;

use yii\validators\Validator;
use app\models\Status;
use Yii;

class FeatureValidator extends Validator
{
    public function init()
    {
        parent::init();
        $this->message = [
            0 => \Yii::t('app', '"Будь ласка, виберiть у списку значення"'),
        ];
    }

    public function validateAttribute($model, $attribute)
    {

    }

    public function clientValidateAttribute($model, $attribute, $view)
    {
        return <<<JS
            var obj =  $(attribute.input);
            var value =  $(attribute.input).val();
            var stavka = obj.closest('.panel-body').find('.bid_value').val();
            if(stavka && (value == '')){
                messages.push({$this->message[0]});
            }
JS;
    }
}