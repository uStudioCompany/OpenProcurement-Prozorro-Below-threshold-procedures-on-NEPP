<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\backend\models\CompaniesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Companies');
$this->params['breadcrumbs'][] = ['label' => 'BackEnd', 'url' => '..'];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="companies-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Company'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <!--    <pre>-->
    <!--        --><? // /*print_r($dataProvider->getModels());//*/ ?>
    <!--    </pre>-->

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => [
            'class' => 'table table-striped'
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn', 'headerOptions' => ['width' => '30'],],

            //'id',
            ['label' => '', 'value' => 'companyType.name', 'headerOptions' => ['width' => '30'],],
            [
                'attribute' => 'legalName',
                'headerOptions' => ['class' => 'hr-dt-table', 'width' => '*'],
                'format' => 'raw',
                'value' => function ($data) {
                    return Html::a($data->legalName, ['view', 'id' => $data->id]);
                },
            ],
            // TEST
            // 'legalName_en',
            // 'legalName_ru',
            // 'registrationCountryName',
            //'identifier',
            ['attribute' => 'identifier', 'headerOptions' => ['class' => 'hr-dt-table', 'width' => '10%'],],
            // 'moneygetId',
            // 'fio',
            // 'fio_en',
            // 'fio_ru',
            // 'userPosition',
            // 'userPosition_en',
            // 'userPosition_ru',
            // 'userDirectionDoc',
            // 'userDirectionDoc_en',
            // 'userDirectionDoc_ru',
            // 'countryName',
            // 'region',
            // 'locality',
            // 'locality_en',
            // 'locality_ru',
            // 'address',
            // 'address_en',
            // 'address_ru',
            // 'postalCode',
            // 'preferLang',
            //'status',
            [
                'label' => Yii::t('app','status'),
                'attribute' => 'status',
                'format' => 'raw',
                'headerOptions' => ['class' => 'hr-dt-table', 'width' => '1%'],
                'filter' => Html::activeDropDownList($searchModel, 'status',
                    \app\modules\backend\models\Companies::getAllStatuses(),
                    ['style' => 'width:150px;', 'class' => 'form-control', 'prompt' => Yii::t('app', 'not select')]),
//                'value' => function ($item) {
//                    return Yii::t('app', $item->status);
//                },

                'value' => function ($data) {
                    $statuses_tpl = ['<span class="glyphicon glyphicon glyphicon-question-sign"></span> '.Yii::t('app', 'Just register'), '<span class="glyphicon glyphicon-ok"></span> ' .Yii::t('app', 'Approved'), '<span class="glyphicon glyphicon-ban-circle"></span> '.Yii::t('app', 'Blocked')];
                    return $statuses_tpl[$data->status];
                },
            ],

//            ['class' => 'yii\grid\ActionColumn', 'template' => '{update} &nbsp; {delete}', 'headerOptions' => ['width' => '60'],],
        ],
    ]); ?>

</div>
