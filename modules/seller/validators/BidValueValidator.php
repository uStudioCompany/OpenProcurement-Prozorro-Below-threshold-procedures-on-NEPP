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

class BidValueValidator extends Validator
{
    public function init()
    {
        parent::init();
        $this->message = [
            0 => \Yii::t('app', '"Зробить ставку"'),
            1 => \Yii::t('app', '"Не може перевищувати суму лоту"'), //2 => \Yii::t('app', '"Норм!"'),
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
//attribute.addError('ddddddddddddd');

            var obj = $(attribute.input);

            //console.log(obj.attr('id'), '-', obj.val(), '-', obj.data('error'));

            var val = parseFloat(obj.val());

            if ( val > parseFloat(obj.data('max'))  ) {
                messages.push({$this->message[1]});
                //console.log(attribute);
                return; }

            if (obj.val() == '') {
                obj.data('error',0);
                obj.data('prev','');

            }

            if (isNaN(val)) { val = ""; }
            //debugger;

            var elemCount = 0;
            var bids = [];
            $('.bid_value').each(function(){
                if($(this).val() != ''){
                    elemCount++;
                    if ($(this).data('need_check') == '1') {
                        bids.push( {id:$(this).data('lid'), amount:$(this).val(), tid:$(this).data('tid')} );
                    }
                } else {
                    if($(this).attr('id') != obj.attr('id')) {
                        //console.log('iii',this.id);
                        $(\$form).yiiActiveForm('updateAttribute', this.id, ''); }
                }
            });



            if (elemCount == 0) {
                //console.log('sss');
                 messages.push({$this->message[0]});
            } else {
                var pre_val = parseFloat(obj.data('prev'));
                if (isNaN(pre_val)) { pre_val = ""; }
                if (obj.val() == '') {
                    obj.data('error',0);
                } else if (val !== pre_val ) {
                    obj.data('prev',val);
                    if ($(attribute.input).data('need_check') == '1' && bids.length > 0) {
                        //console.log(bids);
                        bidFinancialViability(\$form,attribute,obj,bids,this,messages);
                    } else { console.log(bids.length); }
                } else {
                    if (obj.data('error') && obj.data('error')=='1') {
                        messages.push( obj.find(attribute.error).html() ); }
                }
                //$('.bid_value').blur();
            }
JS;
    }
}