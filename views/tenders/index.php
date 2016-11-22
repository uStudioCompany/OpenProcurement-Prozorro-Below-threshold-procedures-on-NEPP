<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ListView;
use kartik\daterange\DateRangePicker;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TendersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
//Yii::$app->VarDumper->dump($_SESSION, 10, true);die;
$this->title = Yii::t('app', 'Tenders');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tenders-index m_viewlist-wrap">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php //echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= \app\models\Companies::getCompanyBusinesType() == 'buyer' ? Html::a(Yii::t('app', 'Create Tenders'),  Yii::$app->urlManager->createAbsoluteUrl(['buyer/tender/create']), ['class' => 'btn btn-success pull-right']) : '' ?>

    <div class="student-form">
        <?php switch (Yii::$app->controller->action->id) {
            case 'archive':
                $statuses = Yii::$app->params['archive.status.tender'];
                $index = 'archive';
                break;
            case 'actual':
                $statuses = Yii::$app->params['actual.status.tender'];
                $index = 'actual';
                break;
            case 'current':
                $statuses = Yii::$app->params['current.status.tender'];
                $index = 'current';
                break;
            default:
                $index = 'index';
        }
        ?>
        <?php
        $form = ActiveForm::begin([
            'method' => 'get',
            'action' => $index
        ]); ?>

        <div class="col-md-2">
            <label for="" class="control-label"><?= Yii::t('app', 'status') ?></label>
            <?php

            if (isset($statuses)) {
                foreach ($statuses as $key => $status) {
                    $statusArray[$status] = Yii::t('app', 'tender_' . $status);
                }
            } else {
                $statusArray = ArrayHelper::map(
                    (new \yii\db\Query())
                        ->select(['status'])
                        ->from('tenders')
                        ->where(['!=', 'status', ''])
                        ->distinct()
                        ->all(),
                    'status',
                    function ($model, $defaultValue) {
                        return Yii::t('app', 'tender_' . $model['status']);
                    }
                );
            }
            ?>
            <?= Html::activeDropDownList($searchModel,
                'status',
                $statusArray,
                ['style' => 'width:150px;', 'class' => 'form-control', 'prompt' => Yii::t('app', 'not select')]);
            ?>
        </div>
        <div class="col-md-2">
            <label for="" class="control-label"><?= Yii::t('app', 'Процедури закупівлі') ?></label>
            <?= Html::activeDropDownList($searchModel, 'tender_method',
                ArrayHelper::map(
                    (new \yii\db\Query())
                        ->select(['tender_method'])
                        ->from('tenders')
                        ->where(['!=', 'tender_method',''])
                        ->distinct()
                        ->all()
                    ,
                    'tender_method',
                    function ($model, $defaultValue) {
                        return Yii::t('app', $model['tender_method']);
                    }
                ),
                ['style' => 'width:150px;', 'class' => 'form-control', 'prompt' => Yii::t('app', 'not select')]);
            ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($searchModel, 'tender_cbd_id')->textInput(['name' => 'TendersSearch[tender_cbd_id]'])->label(Yii::t('app', 'tenderID')) ?>
        </div>

        <div class="col-md-2">
            <?= $form->field($searchModel, 'title')->textInput(['name' => 'TendersSearch[title]'])->label(Yii::t('app', 'title')) ?>
        </div>
        <div class="form-group col-md-3 clearfix">
            <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-success top-buffer margin23', 'tid'=>'search']) ?>
        </div>

        <?php ActiveForm::end(); ?>
        <?
            if(isset(Yii::$app->request->get()['TendersSearch'])) {
                echo count(Yii::$app->request->get()['TendersSearch']) ? Html::a(Yii::t('app', 'Reset'), Yii::$app->urlManager->createAbsoluteUrl([Yii::$app->urlManager->parseRequest(Yii::$app->request)[0]]), ['class' => 'btn btn-danger']) : '';
            } ?>
    </div>
    <div class="clearfix"></div>
    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'itemView' => function ($model, $key, $index, $widget) {
            if (\app\models\Companies::checkCompanyIsSeller() || Yii::$app->user->isGuest) {
                $model = \app\models\TendersSearch::isActiveAward($model);
            }
            if ($model != false) {
                return $this->render('_item', [
                    'model' => $model,
                    'key' => $key
                ]);
            } else {
                return false;
            }
        },
    ]); ?>


    <!--    --><?php //echo GridView::widget([
    //        'dataProvider' => $dataProvider,
    //        'filterModel' => $searchModel,
    //        'columns' => [
    //            ['class' => 'yii\grid\SerialColumn'],
    ////            'id',
    //            [
    //                'label' => Yii::t('app', 'title'),
    //                'attribute' => 'title',
    //            ],
    //            [
    //                'label' => Yii::t('app', 'description'),
    //                'attribute' => 'description',
    //            ],
    //            [
    //                'label' => Yii::t('app', 'status'),
    //                'attribute' => 'status',
    //                'filter' => Html::activeDropDownList($searchModel, 'status',
    //                    ArrayHelper::map(
    //                        (new \yii\db\Query())
    //                            ->select(['status'])
    //                            ->from('tenders')
    //                            ->distinct()
    //                            ->all()
    //                        ,
    //                        'status',
    //                        function ($model, $defaultValue) {
    //                            return Yii::t('app', $model['status']);
    //                        }
    //                    ),
    //                    ['style' => 'width:150px;', 'class' => 'form-control', 'prompt' => Yii::t('app', 'not select')]),
    //                'value' => function ($item) {
    //                    return Yii::t('app', $item->status);
    //                }
    //            ],
    //            [
    //                'attribute' => 'created_at',
    //                'label' => Yii::t('app', 'created_at'),
    //                'format' => ['date', 'dd.MM.Y HH:mm:ss'],
    //                'options' => ['width' => '300'],
    //                'filter' => DateRangePicker::widget([
    //                    'name' => 'Tenders[created_at]',
    //                    'attribute' => 'created_at',
    //                    'model' => $searchModel,
    //                    'pluginOptions' => [
    //                        'locale' => [
    //                            'separator' => ' до ',
    //                            'format' => 'DD-MM-YYYY',
    //                        ],
    //
    //                        'opens' => 'left',
    //                    ]
    //                ])
    //
    //            ],
    //            [
    //                'attribute' => 'update_at',
    //                'label' => Yii::t('app', 'update_at'),
    //                'format' => ['date', 'dd.MM.Y HH:mm:ss'],
    //                'options' => ['width' => '300'],
    //                'filter' => DateRangePicker::widget([
    //                    'name' => 'Tenders[update_at]',
    //                    'attribute' => 'update_at',
    //                    'model' => $searchModel,
    //                    'pluginOptions' => [
    //                        'locale' => [
    //                            'separator' => ' до ',
    //                            'format' => 'DD-MM-YYYY',
    //                        ],
    //
    //                        'opens' => 'left',
    //                    ]
    //                ])
    //
    //            ],
    //            [
    //                'class' => 'yii\grid\ActionColumn',
    //                'headerOptions' => ['style' => 'width: 70px;'],
    //                'template' => '{view} {update} {delete}',
    //                'buttons' => [
    ////                    'create' => function ($url, $model) {
    ////                        return '/tender/create';
    ////                },
    //                    'update' => function ($url, $model) {
    //                        if ($model->status !== 'cancelled' && $model->status !== 'complete' && $model->status !== 'unsuccessful') {
    //                            return Html::a('<span class="glyphicon glyphicon-pencil"></span>',
    //                                $url,
    //                                ['data-pjax' => 0, 'data-method' => 'post', 'data-confirm' => Yii::t("app", "tender_delete_confirm_message")]);
    //                        }
    //                    },
    //                    'delete' => function ($url, $model) {
    //                        if ($model->status !== 'cancelled' && $model->status !== 'complete' && $model->status !== 'unsuccessful') {
    //                            return Html::a('<span class="glyphicon glyphicon-trash"></span>',
    //                                $url,
    //                                ['data-pjax' => 0, 'data-method' => 'post', 'data-confirm' => Yii::t("app", "tender_delete_confirm_message")]);
    //                        }
    //                    }
    //                ],
    //                'urlCreator' => function ($action, $model, $key) {
    //                    if ($action === 'update') {
    //                        return Yii::$app->urlManager->createAbsoluteUrl(['/tender/update', 'id' => $key]);
    //                    }
    //                    if ($action === 'view') {
    //                        return Yii::$app->urlManager->createAbsoluteUrl(['/tenders/view', 'id' => $key]);
    //                    }
    //                    if ($action === 'delete') {
    //                        return Yii::$app->urlManager->createAbsoluteUrl(['/tenders/delete', 'id' => $key]);
    //                    }
    //                }
    //            ],
    //        ],
    //    ]); ?>

</div>
