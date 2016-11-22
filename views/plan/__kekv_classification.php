<?php

use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var $classification app\models\planModels\Classification
 * @var $form yii\widgets\ActiveForm
 * @var $name string
 * @var $type string
 * @var $k string
 * @var $parentId string
 */

//$enableClientValidation = true;
//if ($k === '[++__EMPTY__]') {
//    $enableClientValidation = false; } //'enableClientValidation'=>$enableClientValidation


echo $form->field( $classification, '['.$type.''.$k.']description', [
            'template' => ''.'
                {label}
                <div class="col-md-6">{input}'.
                    Html::activeInput('hidden', $classification, '['.$type.''.$k.']id',
                        [
                            'name' => 'Plan['.$name.'][id]',
                            'class' => 'hidden_classificator',//. str_replace(['[',']'],'_',$name)
                        ]) .
                    Html::activeInput('hidden', $classification, '['.$type.''.$k.']scheme',
                        [
                            'name' => 'Plan['.$name.'][scheme]',
                            'class' => 'hidden_scheme',//. str_replace(['[',']'],'_',$name)
                        ]) .'
                </div>
                <div class="col-md-3">{error}</div>',
        ])
    ->textInput([
        'name' => 'Plan['.$name.'][description]',
        'class' => isset($no_head_select) ? 'form-control classificator-input no-head-select' : 'form-control classificator-input',
        'url' => Url::to( ['classificator/'.$type] ),
        'parent-id'=>$parentId, ]);

