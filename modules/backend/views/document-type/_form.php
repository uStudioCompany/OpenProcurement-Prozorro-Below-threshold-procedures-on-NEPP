<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\DocumentType */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="document-types-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-sm-8">
            <?= $form->field($model, 'id')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'enabled')->dropDownList(['0'=>'Disabled',1=>'Enabled']) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-2">
            <?= $form->field($model, 'tender_flag')->checkbox(['labelOptions'=>['style'=>'font-size:14px']]) ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'bid_flag')->checkbox(['labelOptions'=>['style'=>'font-size:14px']]) ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'award_flag')->checkbox(['labelOptions'=>['style'=>'font-size:14px']]) ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'contract_flag')->checkbox(['labelOptions'=>['style'=>'font-size:14px']]) ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'cancellation_flag')->checkbox(['labelOptions'=>['style'=>'font-size:14px']]) ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'recommended_flag')->checkbox(['labelOptions'=>['style'=>'font-size:14px']]) ?>
        </div>
    </div>

    <ul class="nav nav-tabs">
        <li class="active"><a data-toggle="tab" href="#panel1"><?=Yii::t('app', 'Ukrainian');?></a></li>
        <li><a data-toggle="tab" href="#panel2"><?=Yii::t('app', 'English');?></a></li>
        <li><a data-toggle="tab" href="#panel3"><?=Yii::t('app', 'Russian');?></a></li>
    </ul>

    <div class="tab-content">
        <div id="panel1" class="tab-pane fade in active">
            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'description')->textarea(['maxlength' => true]) ?>
                </div>
            </div>
        </div>
        <div id="panel2" class="tab-pane fade">
            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'title_en')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'description_en')->textarea(['maxlength' => true]) ?>
                </div>
            </div>
        </div>
        <div id="panel3" class="tab-pane fade">
            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'title_ru')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'description_ru')->textarea(['maxlength' => true]) ?>
                </div>
            </div>
        </div>
    </div>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
