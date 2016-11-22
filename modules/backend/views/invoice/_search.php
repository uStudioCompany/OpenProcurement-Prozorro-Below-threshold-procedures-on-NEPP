<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\daterange\DateRangePicker;


/* @var $this yii\web\View */
/* @var $model app\models\PaymentSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<h1><?= Html::encode($this->title) ?></h1>
<div class="student-form">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="col-md-4">
        <?= $form->field($model, 'destination')->textInput(['list' => 'destination'])->label(Yii::t('app', 'За призначенням платежу')) ?>
        <?php
        $invoices = \app\models\Invoice::find()->joinWith(['companies'])->groupBy('destination')->asArray()->all();
        ?>
        <datalist id="destination">
            <?php
            foreach ($invoices as $item) {
                echo '<option>' . $item['destination'] . '</option>';
            }
            ?>
        </datalist>

        <!--div class="col-md-4">
            <?//= $form->field($model, 'legalName')->textInput(['list' => 'listName']) ?>
        <?php
        //$invoices = \app\models\Invoice::find()->joinWith(['companies'])->groupBy('companies.legalName')->asArray()->all();
        ?>
        <datalist id="listName">
            <?php
        //            foreach ($invoices as $item) {
        //                echo '<option>' . $item['companies']['legalName'] . '</option>';
        //            }
        ?>
        </datalist-->
    </div>
    <div class="col-md-2">
        <label for="" class="control-label"><?= Yii::t('app', 'created_at') ?></label>
        <?= DateRangePicker::widget([
            'model' => $model,
            'attribute' => 'created_at',
            'pluginOptions' => [
                'locale' => [
                    'separator' => ' до ',
                    'format' => 'DD-MM-YYYY',
                ],
                'opens' => 'left',
            ]
        ]); ?>
    </div>
    <div class="col-md-2">
        <label for="" class="control-label"><?= Yii::t('app', 'payed_at') ?></label>
        <?= DateRangePicker::widget([
            'model' => $model,
            'attribute' => 'payed_at',
            'pluginOptions' => [
                'locale' => [
                    'separator' => ' до ',
                    'format' => 'DD-MM-YYYY',
                ],
                'opens' => 'left',
            ]
        ]); ?>
    </div>
    <div class="col-md-2">
        <label for="" class="control-label"><?= Yii::t('app', 'status') ?></label>
        <?= Html::activeDropDownList($model, 'status',
            ArrayHelper::map(
                (new \yii\db\Query())
                    ->select(['status'])
                    ->from('invoice')
                    ->where(['!=', 'status', ''])
                    ->distinct()
                    ->all()
                ,
                'status',
                function ($model, $defaultValue) {
                    return Yii::t('app', $model['status']);
                }
            ),
            ['style' => 'width:150px;', 'class' => 'form-control', 'prompt' => Yii::t('app', 'not select')]);
        ?>
    </div>
    <div class="form-group col-md-2 clearfix">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-success top-buffer margin23']) ?>
        <?php ActiveForm::end(); ?>
    </div>
    <?php
    if (isset(Yii::$app->request->get()['InvoiceSearch'])) {
        echo count(Yii::$app->request->get()['InvoiceSearch']) ? Html::a(Yii::t('app', 'Reset'), Yii::$app->urlManager->createAbsoluteUrl([Yii::$app->urlManager->parseRequest(Yii::$app->request)[0]]), ['class' => 'btn btn-danger', 'style' => 'margin-right: -36px']) : '';
    } ?>

</div>