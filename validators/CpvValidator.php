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

class CpvValidator extends Validator
{
    public function init()
    {
        parent::init();
        $this->message = [
            0 => \Yii::t('app', '"Першi три цифри коду повиннi бути однаковими"'),
        ];
    }

    public function validateAttribute($model, $attribute)
    {
        $post = Yii::$app->request->post();
//        Yii::$app->VarDumper->dump($post, 10, true);die;

        if (isset($post['Tender']['items']['__EMPTY_ITEM__'])) {
            unset($post['Tender']['items']['__EMPTY_ITEM__']);
            foreach ($post['Tender']['items'] as $k=>$item) {
                $firstCpvCode = substr($item['classification']['id'], 0, 3);
            }

        }

        if (substr($model->id, 0, 3) != $firstCpvCode) {
            $model->addError($attribute, $this->message[0]);
        }
    }

    public function clientValidateAttribute($model, $attribute, $view)
    {
        return <<<JS

            var firstCpvCode = $('.item:visible').find('.hidden_classification').val();
            firstCpvCode = firstCpvCode.substr(0, 3);

            $('.item:visible').find('.hidden_classification').each(function(){
                var currentCode = $(this).val();
                currentCode = currentCode.substr(0, 3);
                if(currentCode != firstCpvCode){
                     messages.push({$this->message[0]});
                }
            })
JS;
    }
}