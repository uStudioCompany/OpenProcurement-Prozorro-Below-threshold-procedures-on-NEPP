<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CompaniesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Companies');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="companies-index">

    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <?php
        echo \yii\bootstrap\Alert::widget([
            'options' => [
                'class' => 'alert-danger'
            ],
            'body' => Yii::$app->session->getFlash('error')
        ]);
        ?>
    <?php endif;?>
    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <?php
        echo \yii\bootstrap\Alert::widget([
            'options' => [
                'class' => 'alert-success'
            ],
            'body' => Yii::$app->session->getFlash('success')
        ]);
        ?>
    <?php endif;?>

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Companies'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'LegalType',
            'customer_type',
            'legalName',
            'legalName_en',
//            'legalName_ru',
            // 'registrationCountryName',
            // 'identifier',
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
            // 'streetAddress',
            // 'streetAddress_en',
            // 'streetAddress_ru',
            // 'postalCode',
            // 'preferLang',
            // 'status',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
