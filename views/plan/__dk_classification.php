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

echo $form->field( $classification, '['.$type.''.$k.']description', [
            'template' => '
                {label}
                <div class="col-md-6">{input}'.
                    Html::activeInput('hidden', $classification, '['.$type.''.$k.']id',
                        [
                            'name' => 'Plan['.$name.'][id]',
                            'class' => 'hidden_classificator hidden_dk_classificator_id',//. str_replace(['[',']'],'_',$name)
                        ]) .
                    Html::activeInput('hidden', $classification, '['.$type.''.$k.']scheme',
                        [
                            'name' => 'Plan['.$name.'][scheme]',
                            'class' => 'hidden_scheme hidden_scheme_dk',//. str_replace(['[',']'],'_',$name)
                        ]) .'
                </div>
                <div class="col-md-3">{error}</div>',
        ])
    ->textInput([
        'name' => 'Plan['.$name.'][description]',
        'class' => 'form-control classificator-input classificator-input-description',
        'url' => Url::to( ['classificator/'.$type] ),
        'parent-id'=>$parentId,
    ])->label(Yii::t('app','Додаткова класифікація'));

