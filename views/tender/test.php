<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
//use Yii;
use yii\helpers\ArrayHelper;
use app\models\Countries;

    $form = ActiveForm::begin(); ?>

    <?php if ($company->hasErrors()) { ?>

        <div class="alert alert-danger fade in">
            <a href="#" class="close" data-dismiss="alert">&times;</a>
            <strong>Error!</strong>
            <?= $form->errorSummary([$company]) ?>
        </div>

    <?php } ?>



        <?= $form->field($company, 'identifier') ?>

        <?= $form->field($company, 'streetAddress') ?>


    <div class="form-group">
        <div class="col-md-offset-3 col-md-6">
            <?= Html::submitButton(Yii::t('app', 'Register'), ['class' => 'btn btn-default btn-submitform']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>