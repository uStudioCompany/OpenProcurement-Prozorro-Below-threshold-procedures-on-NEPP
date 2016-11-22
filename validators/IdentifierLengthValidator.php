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

class IdentifierLengthValidator extends Validator
{
    public function init()
    {
        parent::init();
        $this->message = [
            0=>\Yii::t('app','"Довжина коду не вiдповiдає обранiй формi власностi"'),
        ];
    }

    public function validateAttribute($model, $attribute)
    {
        return true;
    }

    public function clientValidateAttribute($model, $attribute, $view)
    {

        return <<<JS
        var length = $(attribute.input).val().length;
        var needLength = $('#companies-legaltype option:selected').attr('code_length');
        var minNeedLength = 8;
            if (length > needLength || length < minNeedLength) {
                messages.push({$this-> message[0]});
            }
JS;
    }
}