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

class IpnStructureValidator extends Validator
{
    private $needLength = 12;
    public function init()
    {
        parent::init();
        $message1 = Yii::t('app', 'INN does not meet the EDRPOU code');
        $message2 = Yii::t('app', 'INN does not meet the EDRPOU Parent Company code');
        $message3 = Yii::t('app', 'If your company pays VAT under the VAT certificates of Parent Company will first fill in field "EDRPOU parent company"');
        $this->message = [
            0=> $message1,
            1=> $message2,
            2=> $message3,
        ];
    }

    public function validateAttribute($model, $attribute)
    {
        $this->message[0] = $this->message[0] . '. ' . Yii::t('app', 'Identifier') . ': ' . $model->identifier;
        $identifier = $model->identifier;
        if ($model->isDaughter){
            $identifier = $model->parent_identifier;
        }
        if (mb_strlen($identifier) == 0) {
            $this->addError($model, $attribute, $this->message[2]);
        }
        else{
            $subject = $model->ipn_id;
            $first_part_ipn = substr($identifier, 0, 7);
            $pattern = "/^($first_part_ipn\d{5})$/";
            preg_match($pattern, $subject, $matches);
            if (!count($matches)) {
                if($model->isDaughter){
                    $this->addError($model, $attribute, $this->message[1]);
                }
                else{
                    $this->addError($model, $attribute, $this->message[0]);
                }

            }
        }
    }

    public function clientValidateAttribute($model, $attribute, $view)
    {
        $this->message[0] = $this->message[0] . '. ' . Yii::t('app', 'Identifier') . ': ' . $model->identifier;
        return <<<JS
        var identifier = $model->identifier;
        if ($("#companies-isdaughter input:checked").val() == "1"){
            identifier = $("#companies-parent_identifier").val();
        }
        identifier = identifier + "";
        var length = $(attribute.input).val().length;
        var ipn_id = $(attribute.input).val();
        var needLength = $this->needLength;
        if (identifier.length == 0) {
            messages.push('{$this->message[2]}');
        }
        else{
            var first_part_ipn = identifier.substring(0, 7);
            var pattern = new RegExp('^(' + first_part_ipn + '\\\d{5})$','i');
            var ipn_id_control = pattern.test(ipn_id);
            if (!ipn_id_control) {
                if ($("#companies-isdaughter input:checked").val() == 1){
                    messages.push('{$this->message[1]}');
                }
                else{
                    messages.push('{$this->message[0]}');
                }
            }
        }
JS;
    }
}