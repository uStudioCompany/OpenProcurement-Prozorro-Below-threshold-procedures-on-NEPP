<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ContractsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Contracts');
$this->params['breadcrumbs'][] = $this->title;
\app\modules\backend\assets\BackendAsset::register($this);
?>
<div class="contracts-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Contracts'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

//            'id',
            'name',
            'description',
//            'text',
//            'company_id',
            [
                'attribute' => 'create_at',
                'label' => Yii::t('app', 'create_at'),
                'format' => ['date', 'dd.MM.Y HH:mm:ss'],
                'options' => ['width' => '300'],
                'filter' => DateRangePicker::widget([
                    'name' => 'Contracts[create_at]',
                    'attribute' => 'create_at',
                    'model' => $searchModel,
                    'pluginOptions' => [
                        'depends'=>[
                            'yii\web\JqueryAsset',
                            'js/moment.js',
                        ],
                        'locale' => [
                            'separator' => ' до ',
                            'format' => 'DD-MM-YYYY',
                        ],

                        'opens' => 'left',
                    ]
                ])

            ],
//            'created_at:datetime',
//            'updated_at',

            ['class' => 'yii\grid\ActionColumn',
                'headerOptions' => ['style' => 'width: 70px;'],
                'template' => '{view} {update}',
            ]
        ],
    ]); ?>
</div>