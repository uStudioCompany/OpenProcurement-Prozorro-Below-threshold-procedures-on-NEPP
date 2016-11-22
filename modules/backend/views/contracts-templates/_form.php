<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use vova07\imperavi\Widget;

/* @var $this yii\web\View */
/* @var $model app\models\Contracts */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="contracts-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput() ?>

    <?= $form->field($model, 'description')->textarea() ?>

<!--    --><?//= $form->field($model, 'text')->widget(Widget::className(), [
//        'settings' => [
//            'lang' => 'ua',
//            'minHeight' => 200,
//            'plugins' => [
//                'clips',
//                'fullscreen'
//            ]
//        ]
//    ]); ?>
    <?= $form->field($model, 'text')->textarea() ?>


<!--    --><?//= $form->field($model, 'company_id')->textInput() ?>

<!--    --><?//= $form->field($model, 'created_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
