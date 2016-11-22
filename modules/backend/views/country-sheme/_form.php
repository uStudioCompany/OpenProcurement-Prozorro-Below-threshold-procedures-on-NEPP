<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CountrySheme */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="country-sheme-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'country_id')->dropDownList(\yii\helpers\ArrayHelper::map(\app\models\Countries::find()->all(), 'id', 'name'),[
    'onchange'=>'$.post( "'.Yii::$app->urlManager->createUrl(["/backend/country-sheme/get-company-types-by-country-id"]).'",{id:$(this).val()},function(data){
                $("#countrysheme-company_type_ids").html(data);
                })'
    ]) ?>

    <?= $form->field($model, 'company_type_ids')->checkboxList(\yii\helpers\ArrayHelper::map(\app\models\CompanyType::find()->filterWhere(['country_id'=>isset($model->country_id) ? $model->country_id : 1])->all(), 'id', 'name')) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
