<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CompaniesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="companies-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'LegalType') ?>

    <?= $form->field($model, 'legalName') ?>

    <?= $form->field($model, 'legalName_en') ?>

    <?= $form->field($model, 'legalName_ru') ?>

    <?php // echo $form->field($model, 'registrationCountryName') ?>

    <?php // echo $form->field($model, 'identifier') ?>

    <?php // echo $form->field($model, 'moneygetId') ?>

    <?php // echo $form->field($model, 'fio') ?>

    <?php // echo $form->field($model, 'fio_en') ?>

    <?php // echo $form->field($model, 'fio_ru') ?>

    <?php // echo $form->field($model, 'userPosition') ?>

    <?php // echo $form->field($model, 'userPosition_en') ?>

    <?php // echo $form->field($model, 'userPosition_ru') ?>

    <?php // echo $form->field($model, 'userDirectionDoc') ?>

    <?php // echo $form->field($model, 'userDirectionDoc_en') ?>

    <?php // echo $form->field($model, 'userDirectionDoc_ru') ?>

    <?php // echo $form->field($model, 'countryName') ?>

    <?php // echo $form->field($model, 'region') ?>

    <?php // echo $form->field($model, 'locality') ?>

    <?php // echo $form->field($model, 'locality_en') ?>

    <?php // echo $form->field($model, 'locality_ru') ?>

    <?php // echo $form->field($model, 'streetAddress') ?>

    <?php // echo $form->field($model, 'streetAddress_en') ?>

    <?php // echo $form->field($model, 'streetAddress_ru') ?>

    <?php // echo $form->field($model, 'postalCode') ?>

    <?php // echo $form->field($model, 'preferLang') ?>

    <?php // echo $form->field($model, 'status') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
