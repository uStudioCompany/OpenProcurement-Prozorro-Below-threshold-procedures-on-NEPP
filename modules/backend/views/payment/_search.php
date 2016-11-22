<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Companies;
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
    <div class="col-md-5">
        <?= $form->field($model, 'legalName')->textInput(['list' => 'listName']) ?>
        <?php
        $payments = \app\models\Payment::find()->joinWith(['invoice', 'companies'])->groupBy('companies.legalName')->asArray()->all();
        ?>
        <datalist id="listName">
            <?php
            foreach ($payments as $item) {
                echo '<option>' . $item['companies']['legalName'] . '</option>';
            }
            ?>
        </datalist>
    </div>
    <div class="col-md-2">
        <label for="" class="control-label"><?= Yii::t('app', 'status') ?></label>
        <?= Html::activeDropDownList($model, 'status',
            ArrayHelper::map(
                (new \yii\db\Query())
                    ->select(['status'])
                    ->from('payment')
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
    <div class="form-group col-md-3 clearfix">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-success top-buffer margin23']) ?>
        <?php ActiveForm::end(); ?>
    </div>
    <?php
    if (isset(Yii::$app->request->get()['PaymentSearch'])) {
        echo count(Yii::$app->request->get()['PaymentSearch']) ? Html::a(Yii::t('app', 'Reset'), Yii::$app->urlManager->createAbsoluteUrl([Yii::$app->urlManager->parseRequest(Yii::$app->request)[0]]), ['class' => 'btn btn-danger']) : '';
    } ?>

</div>
