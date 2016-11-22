<?php
/**
 * Created by PhpStorm.
 * User: Oleg
 * Date: 14.01.2016
 * Time: 22:58
 */

namespace app\modules\seller\validators;

use yii\validators\Validator;
use app\models\Status;
use Yii;

class SubcontractingDetailsValidator extends Validator
{
    public function init()
    {
        parent::init();
        $this->message = [
            0 => \Yii::t('app', '"Необхідно заповнити Iнформацiя про пiдрядника."'),
        ];
    }

    public function validateAttribute($model, $attribute)
    {
//        $post = Yii::$app->request->post();
//
//        if (isset($post['Tender']['items']['__EMPTY_ITEM__'])) {
//            unset($post['Tender']['items']['__EMPTY_ITEM__']);
//            $firstCpvCode = substr($post['Tender']['items'][0]['classification']['id'], 0, 3);
//        }
//
//        if (substr($model->id, 0, 3) != $firstCpvCode) {
//            $model->addError($attribute, $this->message[0]);
//        }
    }

    public function clientValidateAttribute($model, $attribute, $view)
    {
        return <<<JS
        if ($(attribute.input).closest('.row').find('.bid_value').val() != ''){
            if($(attribute.input).val() == ''){
                messages.push({$this->message[0]});
            }
        }
JS;
    }
}