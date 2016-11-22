<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PersonsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="persons-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'userName') ?>

    <?= $form->field($model, 'userName_en') ?>

    <?= $form->field($model, 'userName_ru') ?>

    <?= $form->field($model, 'userSurname') ?>

    <?php // echo $form->field($model, 'userSurname_en') ?>

    <?php // echo $form->field($model, 'userSurname_ru') ?>

    <?php // echo $form->field($model, 'userPatronymic') ?>

    <?php // echo $form->field($model, 'userPatrunomic_en') ?>

    <?php // echo $form->field($model, 'userPatrunomic_ru') ?>

    <?php // echo $form->field($model, 'email') ?>

    <?php // echo $form->field($model, 'telephone') ?>

    <?php // echo $form->field($model, 'faxNumber') ?>

    <?php // echo $form->field($model, 'mobile') ?>

    <?php // echo $form->field($model, 'url') ?>

    <?php // echo $form->field($model, 'company_id') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
