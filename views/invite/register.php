<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Invite */

$this->title = $model->fio;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Invites'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invite-register">

    <?php $form = ActiveForm::begin(); ?>

    <?php if ($model->hasErrors()) { ?>

        <div class="alert alert-danger fade in">
            <a href="#" class="close" data-dismiss="alert">&times;</a>
            <strong>Error!</strong>
            <?= $form->errorSummary([$model]) ?>
        </div>

    <?php } ?>

    <h1><?= Yii::t('app', 'Username and password to connection') ?></h1>

    <?= $form->field($user_model, 'fio')->textInput(['value'=>$model->fio]) ?>
    <?= $form->field($user_model, 'username')->textInput(['readonly'=>'readonly','value'=>$model->email]) ?>
    <?= $form->field($user_model, 'password')->passwordInput() ?>
    <?= $form->field($user_model, 'confirmPassword')->passwordInput() ?>
    <?= Html::activeHiddenInput($user_model, 'company_id') ?>
    <?= Html::activeHiddenInput($user_model, 'is_owner') ?>

    <?= $form->field($user_model, 'info1')->checkbox() ?>
    <?= $form->field($user_model, 'info2')->checkbox() ?>
    <?= $form->field($user_model, 'info3')->checkbox() ?>
    <?= $form->field($user_model, 'subscribe_status')->checkbox() ?>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div><!-- register-invite -->
