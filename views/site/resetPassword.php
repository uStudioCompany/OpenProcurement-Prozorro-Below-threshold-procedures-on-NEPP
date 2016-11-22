<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Reset password';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-reset-password">

    <p><?=Yii::t('app','Пожалуйста введите новый пароль:')?></p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'reset-password-form']); ?>

                <?= $form->field($model, 'password')->passwordInput() ?>
                <?= $form->field($model, 'confirmPassword')->passwordInput() ?>
            <?php
//            Yii::$app->VarDumper->dump($model, 10, true);die;
            ?>
                <div class="form-group">
                    <?= Html::submitButton(Yii::t('app','Submit'), ['class' => 'btn btn-primary']) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
