<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ListView;
use kartik\daterange\DateRangePicker;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use app\models\Companies;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PlansSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Plan');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tenders-index m_viewlist-wrap">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= \app\models\Companies::getCompanyBusinesType() == 'buyer' ? Html::a(Yii::t('app', 'Create Plan'),  Yii::$app->urlManager->createAbsoluteUrl(['buyer/plan/create']), ['class' => 'btn btn-success pull-right']) : '' ?>

    <div class="student-form">
        <?php $form = ActiveForm::begin([
            'method' => 'get'
        ]); ?>

        <div class="col-md-6">
            <?= $form->field($searchModel, 'plan_cbd_id')->textInput(['name' => 'PlansSearch[plan_cbd_id]'])->label(Yii::t('app', 'PlanID')) ?>
        </div>

        <div class="col-md-3">
            <?php if(!in_array(Companies::getCompanyBusinesType(), ['seller',''])){ ?>
                <label for="" class="control-label"><?= Yii::t('app', 'status') ?></label>
                <?= Html::activeDropDownList($searchModel, 'status',
                    ArrayHelper::map(
                        (new \yii\db\Query())
                            ->select(['status'])
                            ->from('plans')
                            ->where(['!=', 'status',''])
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
            <?php } ?>
        </div>

        <div class="form-group col-md-3 clearfix">
            <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-success top-buffer margin23']) ?>
        </div>

        <?php ActiveForm::end(); ?>
        <?
        if(isset(Yii::$app->request->get()['PlansSearch'])) {
            echo count(Yii::$app->request->get()['PlansSearch']) ? Html::a(Yii::t('app', 'Reset'), Yii::$app->urlManager->createAbsoluteUrl([Yii::$app->urlManager->parseRequest(Yii::$app->request)[0]]), ['class' => 'btn btn-danger']) : '';
        } ?>
    </div>
    <div class="clearfix"></div>

    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'itemView' => function ($model, $key, $index, $widget) {
            return $this->render('_item_list', [
                'model' => $model,
                'key' => $key
            ]);
        },
    ]); ?>

</div>
