<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\Companies */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="companies-form">

    <?php $form = ActiveForm::begin(); ?>

    <h1>ЮРИДИЧНА АДРЕСА</h1>

    <?= $form->field($model, 'registrationCountryName')->dropDownList(ArrayHelper::map(\app\models\Countries::find()->all(), 'id', 'name')) ?>

    <?= $form->field($model, 'region')->dropDownList(ArrayHelper::map(\app\models\Regions::find()->all(), 'id', 'name')) ?>

    <?= $form->field($model, 'locality')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'streetAddress')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'postalCode')->textInput(['maxlength' => true]) ?>


    <h1><?= Yii::t('app', 'ABOUT PARTICIPANT') ?></h1>
    <?= $form->field($model, 'LegalType')->dropDownList(ArrayHelper::map(\app\models\CompanyType::find()->all(), 'id', 'name')) ?>

    <?= $form->field($model, 'customer_type')->dropDownList(ArrayHelper::map(\app\models\CompanyCustomerType::find()->all(), 'id', 'name')) ?>

    <?= $form->field($model, 'legalName')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'legalName_en')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'countryName')->dropDownList(ArrayHelper::map((new \app\models\CountrySheme)->find()->where(['country_id'=>1])->all(), 'id', 'name')) ?>


    <?= $form->field($model, 'identifier')->textInput(['maxlength' => true]) ?>



    <?= $form->field($model, 'fio')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'userPosition')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'userDirectionDoc')->textInput(['maxlength' => true]) ?>



    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
