<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\DeliverySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Deliveries');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="delivery-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Delivery'), ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a(Yii::t('app', 'Reset'), ['/delivery/'], ['class' => 'btn btn-success']) ?>
    </p>

    <ul class="nav nav-tabs">
        <li class="active"><a data-toggle="tab" href="#panel1">Укр.</a></li>
        <li><a data-toggle="tab" href="#panel2">Eng.</a></li>
        <li><a data-toggle="tab" href="#panel3">Рус.</a></li>
    </ul>

    <div class="tab-content">
        <div id="panel1" class="tab-pane fade in active">
            <?php Pjax::begin(); ?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'label' => 'countryName',
                        'attribute' => 'countryName',
                        'filter' => Html::activeDropDownList($searchModel, 'countryName', ArrayHelper::map(\app\models\Countries::find()->asArray()->all(), 'id', 'name'), ['style' => 'width:150px;', 'class' => 'form-control', 'prompt' => 'Не выбрано']),
                        'value' => 'country.name',
                    ],
                    [
                        'label' => 'region',
                        'attribute' => 'region',
                        'filter' => Html::activeDropDownList($searchModel, 'region', ArrayHelper::map(\app\models\Regions::find()->asArray()->all(), 'id', 'name'), ['style' => 'width:150px;', 'class' => 'form-control', 'prompt' => 'Не выбрано']),
                        'value' => 'dregion.name',
                    ],
                    'locality',
                    'postalCode',
                    ['class' => 'yii\grid\ActionColumn'],
                ],
            ]);
            Pjax::end();
            ?>
        </div>
        <div id="panel2" class="tab-pane fade">
            <?php Pjax::begin(); ?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'label' => 'countryName',
                        'attribute' => 'countryName',
                        'filter' => Html::activeDropDownList($searchModel, 'countryName', ArrayHelper::map(\app\models\Countries::find()->asArray()->all(), 'id', 'en_name'), ['style' => 'width:150px;', 'class' => 'form-control', 'prompt' => 'Не выбрано']),
                        'value' => 'country.en_name',
                    ],
                    [
                        'label' => 'region',
                        'attribute' => 'region',
                        'filter' => Html::activeDropDownList($searchModel, 'region', ArrayHelper::map(\app\models\Regions::find()->asArray()->all(), 'id', 'en_name'), ['style' => 'width:150px;', 'class' => 'form-control', 'prompt' => 'Не выбрано']),
                        'value' => 'dregion.en_name',
                    ],
                    'locality_en',
                    'postalCode',
                    ['class' => 'yii\grid\ActionColumn'],
                ],
            ]);
            Pjax::end();
            ?>

        </div>
        <div id="panel3" class="tab-pane fade">
            <?php Pjax::begin(); ?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'label' => 'countryName',
                        'attribute' => 'countryName',
                        'filter' => Html::activeDropDownList($searchModel, 'countryName', ArrayHelper::map(\app\models\Countries::find()->asArray()->all(), 'id', 'ru_name'), ['style' => 'width:150px;', 'class' => 'form-control', 'prompt' => 'Не выбрано']),
                        'value' => 'country.en_name',
                    ],
                    [
                        'label' => 'region',
                        'attribute' => 'region',
                        'filter' => Html::activeDropDownList($searchModel, 'region', ArrayHelper::map(\app\models\Regions::find()->asArray()->all(), 'id', 'ru_name'), ['style' => 'width:150px;', 'class' => 'form-control', 'prompt' => 'Не выбрано']),
                        'value' => 'dregion.ru_name',
                    ],
                    'locality_ru',
                    'postalCode',
                    ['class' => 'yii\grid\ActionColumn'],
                ],
            ]);
            Pjax::end();
            ?>
        </div>
    </div>
</div>
