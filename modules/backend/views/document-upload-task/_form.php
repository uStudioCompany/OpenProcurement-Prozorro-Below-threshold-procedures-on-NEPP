<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\DocumentUploadTask */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="document-upload-task-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'file')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'mime')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'document_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'tender_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'tender_token')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'document_type_id')->textInput() ?>

    <?= $form->field($model, 'document_of')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'related_item')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'upload_at')->textInput() ?>

    <?= $form->field($model, 'transaction_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
