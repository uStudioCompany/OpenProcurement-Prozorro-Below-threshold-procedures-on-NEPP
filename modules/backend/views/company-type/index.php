<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CompanyTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Company types');
$this->params['breadcrumbs'][] = ['label' => 'BackEnd', 'url' => '..'];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="company-type-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create'), ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a(Yii::t('app', 'Reset'), [''], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => [
            'class' => 'table table-striped'
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn','headerOptions' =>['width' => '40'],],

            //'id',
            ['attribute'=>'name','headerOptions' =>['width' => '120'],],
            [
                'label' => Yii::t('app', 'country'),
                'attribute' => 'country_id',
                'filter' => Html::activeDropDownList($searchModel, 'country_id',
                    \yii\helpers\ArrayHelper::map(
                        (new \yii\db\Query())
                            ->select(['id','name'])
                            ->from('countries')
                            ->all()
                        ,
                        'id','name'
                    ),
                    ['style' => 'width:150px;', 'class' => 'form-control', 'prompt' => Yii::t('app', 'not select')]),

                'value' => function ($model, $key, $index, $grid) {
                    return \app\models\Countries::findOne(['id'=>$model->country_id])->name;
                }
            ],
            'code_length',

            ['class' => 'yii\grid\ActionColumn','template'=>'{update} {delete}','headerOptions' =>['width' => '50'],],
        ],
    ]); ?>

</div>
