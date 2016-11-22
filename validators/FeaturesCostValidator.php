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

class FeaturesCostValidator extends Validator
{
    public function init()
    {
        parent::init();
        $this->message = [
            0 => \Yii::t('app', '"Таке значення вже є"'),
            1 => \Yii::t('app', '"Загальна вага нецінових показників не повинна перевищувати 30%"'),
            2 => \Yii::t('app', '"Увага. Одним iз значення критерiю має бути значення 0"'),
            3 => \Yii::t('app', '"Загальна вага нецінових показників не повинна перевищувати 99%"'),
        ];
    }

    public function validateAttribute($model, $attribute)
    {
//        $value = $model->$attribute;
//        if (!Status::find()->where(['id' => $value])->exists()) {
//            $model->addError($attribute, $this->message);
//        }
    }

    public function clientValidateAttribute($model, $attribute, $view)
    {
        return <<<JS
            if($('.tender_method_select').val() != 'open_competitiveDialogueUA' &&
                $('.tender_method_select').val() != 'open_competitiveDialogueEU') {
                    var current_enum = $(attribute.input);
                    var curernt_block = $(attribute.input).closest('.enum_block');
                    curernt_block.find('.feature_enum_input:visible').not(current_enum).each(function () {
                        if ($(this).val() == current_enum.val()) {
                            messages.push({$this->message[0]});
                        }
                    })
                
                    //проверяем сумму всех показателей
                    var FeatureCost = 0;
                    $('.features_block:visible').find('.feature:visible').each(function () {
                        var enum_cost_val = 0;
                        var enum_cost_sum = 0;
                        $(this).find('.feature_enum_input:visible').each(function () {
                            if (parseInt($(this).val()) > parseInt(enum_cost_val)) {
                                enum_cost_val = $(this).val();
                            }
                            if (enum_cost_sum > 30) {
                                messages.push({$this->message[1]});
                            }
                
                        })
                        FeatureCost += parseInt(enum_cost_val);
                    })
                    if (FeatureCost > 30) {
                        messages.push({$this->message[1]});
                    }
                
                
                    if($(attribute.container).closest('.features_block:visible').parent().hasClass('lots_marker')){
                        FeatureCost=0;
                        $(attribute.container).closest('.features_block:visible').find('.feature:visible').each(function () {
                            var enum_cost_val = 0;
                            var enum_cost_sum = 0;
                            $(this).find('.feature_enum_input:visible').each(function () {
                                if (parseInt($(this).val()) > parseInt(enum_cost_val)) {
                                    enum_cost_val = $(this).val();
                                }
                                if (enum_cost_sum > 30) {
                                    messages.push({$this->message[1]});
                                }
                            })
                            FeatureCost += parseInt(enum_cost_val);
                        })
                
                        if (FeatureCost > 30) {
                            messages.push({$this->message[1]});
                        }
                    }
                
                    // проверяем что бы был 0
                    $('.features_block:visible').each(function() {
                        $(this).find('.feature:visible').each(function () {
                            needError = true;
                            $(this).find('.feature_enum_input:visible').each(function () {
                                if ($(this).val() == 0) {
                                    needError = false;
                                }
                            })
                            if (needError) {
                                messages.push({$this->message[2]});
                            }
                        })
                    })
                
                    $('.feature_enum_input:visible').blur();
            }else{
                
                
                    var current_enum = $(attribute.input);
                    var curernt_block = $(attribute.input).closest('.enum_block');
                    curernt_block.find('.feature_enum_input:visible').not(current_enum).each(function () {
                        if ($(this).val() == current_enum.val()) {
                            messages.push({$this->message[0]});
                        }
                    })
                
                    //проверяем сумму всех показателей
                    var FeatureCost = 0;
                    $('.features_block:visible').find('.feature:visible').each(function () {
                        var enum_cost_val = 0;
                        var enum_cost_sum = 0;
                        $(this).find('.feature_enum_input:visible').each(function () {
                            if (parseInt($(this).val()) > parseInt(enum_cost_val)) {
                                enum_cost_val = $(this).val();
                            }
                            if (enum_cost_sum > 99) {
                                messages.push({$this->message[3]});
                            }
                
                        })
                        FeatureCost += parseInt(enum_cost_val);
                    })
                    if (FeatureCost > 99) {
                        messages.push({$this->message[3]});
                    }
                
                
                    if($(attribute.container).closest('.features_block:visible').parent().hasClass('lots_marker')){
                        FeatureCost=0;
                        $(attribute.container).closest('.features_block:visible').find('.feature:visible').each(function () {
                            var enum_cost_val = 0;
                            var enum_cost_sum = 0;
                            $(this).find('.feature_enum_input:visible').each(function () {
                                if (parseInt($(this).val()) > parseInt(enum_cost_val)) {
                                    enum_cost_val = $(this).val();
                                }
                                if (enum_cost_sum > 99) {
                                    messages.push({$this->message[3]});
                                }
                            })
                            FeatureCost += parseInt(enum_cost_val);
                        })
                
                        if (FeatureCost > 99) {
                            messages.push({$this->message[3]});
                        }
                    }
                
                    
                
                
            }
JS;
    }
}