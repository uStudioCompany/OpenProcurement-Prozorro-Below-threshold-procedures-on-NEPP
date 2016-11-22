<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Companies;
use kartik\daterange\DateRangePicker;


?>
<div class="student-form">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="col-md-3"></div>
    <div class="col-md-3">
        <label for="" class="control-label"><?= Yii::t('app', 'Way') ?></label>
        <?= Html::activeDropDownList($model, 'way',
            ArrayHelper::map(
                (new \yii\db\Query())
                    ->select(['way'])
                    ->from('cash_flow')
                    ->where(['!=', 'way', ''])
                    ->distinct()
                    ->all()
                ,
                'way',
                function ($model, $defaultValue) {
                    return Yii::t('app', $model['way']);
                }
            ),
            ['style' => 'width:150px;', 'class' => 'form-control', 'prompt' => Yii::t('app', 'not select')]);
        ?>
    </div>
    <div class="col-md-3">
        <label for="" class="control-label"><?= Yii::t('app', 'Payed At') ?></label>
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

    <div class="form-group col-md-3 clearfix">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-success top-buffer margin23']) ?>
        <?php ActiveForm::end(); ?>
    </div>
    <?php
    if (isset(Yii::$app->request->get()['CashFlowSearch'])) {
        echo count(Yii::$app->request->get()['CashFlowSearch']) ? Html::a(Yii::t('app', 'Reset'), Yii::$app->urlManager->createAbsoluteUrl([Yii::$app->urlManager->parseRequest(Yii::$app->request)[0]]), ['class' => 'btn btn-danger']) : '';
    } ?>
</div>
