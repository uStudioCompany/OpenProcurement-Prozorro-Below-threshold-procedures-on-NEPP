<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\ListView;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ContractingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Contractings');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contracting-index tenders-index m_viewlist-wrap">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="student-form">
        <?php $form = ActiveForm::begin([
            'method' => 'get'
        ]); ?>

        <div class="col-md-3">
            <label for="" class="control-label"><?= Yii::t('app', 'status') ?></label>
            <?= Html::activeDropDownList($searchModel, 'status',
                ArrayHelper::map(
                    (new \yii\db\Query())
                        ->select(['status'])
                        ->from('contracting')
                        ->where(['!=', 'status',''])
                        ->distinct()
                        ->all()
                    ,
                    'status',
                    function ($model, $defaultValue) {
                        return Yii::t('app', 'contract_'.$model['status']);
                    }
                ),
                ['style' => 'width:150px;', 'class' => 'form-control', 'prompt' => Yii::t('app', 'not select')]);
            ?>
        </div>

        <div class="col-md-3">
            <?= $form->field($searchModel, 'contract_cbd_id')->textInput(['name' => 'ContractingSearch[contract_cbd_id]'])->label(Yii::t('app', 'ContractID')) ?>
        </div>

        <div class="col-md-3">
            <?= $form->field($searchModel, 'tender_cbd_id')->textInput(['name' => 'ContractingSearch[tender_cbd_id]'])->label(Yii::t('app', 'tenderID')) ?>
        </div>

        <div class="form-group col-md-3 clearfix">
            <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-success top-buffer margin23']) ?>
        </div>

        <?php ActiveForm::end(); ?>
        <?
        if(isset(Yii::$app->request->get()['ContractingSearch'])) {
            echo count(Yii::$app->request->get()['ContractingSearch']) ? Html::a(Yii::t('app', 'Reset'), Yii::$app->urlManager->createAbsoluteUrl([Yii::$app->urlManager->parseRequest(Yii::$app->request)[0]]), ['class' => 'btn btn-danger']) : '';
        } ?>
    </div>
    <div class="clearfix"></div>
    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'itemView' => function ($model, $key, $index, $widget) {
                return $this->render('_item', [
                    'model' => $model,
                    'key' => $key
                ]);
        },
    ]); ?>
</div>
