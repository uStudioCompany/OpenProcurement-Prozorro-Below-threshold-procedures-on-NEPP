<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Persons */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="persons-form">

    <?php $form = ActiveForm::begin(); ?>

    




            <?= $form->field($model, 'userName')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'userSurname')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'userPatronymic')->textInput(['maxlength' => true]) ?>



            <?= $form->field($model, 'userName_en')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'userSurname_en')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'userPatronymic_en')->textInput(['maxlength' => true]) ?>


<!--        <div id="panel3" class="tab-pane fade">-->
<!---->
<!--            --><?//= $form->field($model, 'userName_ru')->textInput(['maxlength' => true]) ?>
<!---->
<!--            --><?//= $form->field($model, 'userSurname_ru')->textInput(['maxlength' => true]) ?>
<!---->
<!--            --><?//= $form->field($model, 'userPatronymic_ru')->textInput(['maxlength' => true]) ?>
<!---->
<!--        </div>-->




    <?= $form->field($model, 'availableLanguage')->dropDownList(
        [
            'uk' => 'Украинский',
            'en' => 'English',
            'ru' => 'Русский',
        ]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'telephone')->textInput(['maxlength' => true])
        ->widget(\yii\widgets\MaskedInput::className(), [
        'mask' => '+38(999)999-99-99',
        'options' => [
            'class' => 'form-control tel_input',
        ],
        'clientOptions' => [
            'clearIncomplete' => true
        ]
    ]) ?>

    <?= $form->field($model, 'faxNumber')->textInput(['maxlength' => true])
        ->widget(\yii\widgets\MaskedInput::className(), [
            'mask' => '+38(999)999-99-99',
            'options' => [
                'class' => 'form-control tel_input',
            ],
            'clientOptions' => [
                'clearIncomplete' => true
            ]
        ]) ?>

    <?= $form->field($model, 'mobile')->textInput(['maxlength' => true])
        ->widget(\yii\widgets\MaskedInput::className(), [
            'mask' => '+38(999)999-99-99',
            'options' => [
                'class' => 'form-control tel_input',
            ],
            'clientOptions' => [
                'clearIncomplete' => true
            ]
        ]) ?>

    <?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
