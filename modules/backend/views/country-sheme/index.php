<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CountryShemeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Country Shemes');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="country-sheme-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Country Sheme'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

//            'id',
            'name',
            [
                'label' => Yii::t('app', 'country'),
                'attribute' => 'country_id',
                'filter' => Html::activeDropDownList($searchModel, 'country_id',
                    \yii\helpers\ArrayHelper::map(
                        (new \yii\db\Query())
                            ->select(['id', 'name'])
                            ->from('countries')
                            ->all()
                        ,
                        'id', 'name'
                    ),
                    ['style' => 'width:150px;', 'class' => 'form-control', 'prompt' => Yii::t('app', 'not select')]),

                'value' => function ($model, $key, $index, $grid) {
                    return \app\models\Countries::findOne(['id' => $model->country_id])->name;
                }
            ],
            [
                'label' => Yii::t('app', 'Customer Type'),
                'filter' =>'',
                'attribute' => 'company_type_ids',
                'value' => function ($model, $key, $index, $grid) {

                    $res = \app\models\CompanyType::find()
                        ->where(['in', 'id', explode(',',$model->company_type_ids)])
                        ->all();

                    if ($res) {
                        $html = [];
                        foreach ($res as $k => $v) {
                            $html[] = $v['name'];
                        }
                        return implode(',',$html);
                    }

                }
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
