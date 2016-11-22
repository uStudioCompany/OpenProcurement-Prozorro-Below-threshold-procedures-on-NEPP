<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\backend\models\DocumentTypesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Document Types');
$this->params['breadcrumbs'][] = ['label' => 'BackEnd', 'url' => '..'];
$this->params['breadcrumbs'][] = Yii::t('app', 'Document Types'); //$this->title;
?>
<div class="document-types-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Document Types'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => [
            'class' => 'table table-striped'
        ],
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'id',
                'headerOptions' =>['class' => 'hr-dt-table-id','width' => '5%'],
            ],
            //'tender_flag',
            [
                'label' => 'Tender',
                'attribute' => 'tender_flag',
                'format' => 'raw',
                'headerOptions' =>['class' => 'hr-dt-table', 'width' => '1%'],
                'filter'=>["1"=>Yii::t('app', 'Checked'),"0"=>Yii::t('app', 'Not checked'),],
                'value' => function($data) {
                    return ($data->tender_flag) ? '<span class="glyphicon glyphicon-ok"></span>' : ''; },
            ],
            //'bid_flag',
            [
                'label' => 'Bid',
                'attribute' => 'bid_flag',
                'format' => 'raw',
                'headerOptions' =>['class' => 'hr-dt-table', 'width' => '1%'],
                'filter'=>["1"=>Yii::t('app', 'Checked'),"0"=>Yii::t('app', 'Not checked'),],
                'value' => function($data) {
                    return ($data->bid_flag) ? '<span class="glyphicon glyphicon-ok"></span>' : ''; },
            ],
            //'award_flag',
            [
                'label' => 'Award',
                'attribute' => 'award_flag',
                'format' => 'raw',
                'headerOptions' =>['class' => 'hr-dt-table','width' => '1%'],
                'filter'=>["1"=>Yii::t('app', 'Checked'),"0"=>Yii::t('app', 'Not checked'),],
                'value' => function($data) {
                    return ($data->award_flag) ? '<span class="glyphicon glyphicon-ok"></span>' : ''; },
            ],
            //'contract_flag',
            [
                'label' => 'Contract',
                'attribute' => 'contract_flag',
                'format' => 'raw',
                'headerOptions' =>['class' => 'hr-dt-table','width' => '1%'],
                'filter'=>["1"=>Yii::t('app', 'Checked'),"0"=>Yii::t('app', 'Not checked'),],
                'value' => function($data) {
                    return ($data->contract_flag) ? '<span class="glyphicon glyphicon-ok"></span>' : ''; },
            ],
            //'cancellation_flag',
/*            [
                'label' => 'cl',
                'attribute' => 'cancellation_flag',
                'format' => 'raw',
                'value' => function($data) {
                    return Html::checkbox('zzz',$data->cancellation_flag,['disabled'=>'disabled']); },
            ],//*/
            //'recommended_flag',
/*            [
                'label' => 'rc',
                'attribute' => 'recommended_flag',
                'format' => 'raw',
                'value' => function($data) {
                    return Html::checkbox('zzz',$data->recommended_flag,['disabled'=>'disabled']); },
            ],*/
            [
                'attribute' => 'title',
                'headerOptions' =>['class' => 'hr-dt-table-title','width' => '*'],
            ],
            [
                'attribute' => 'description',
                'headerOptions' =>['class' => 'hr-dt-table-title','width' => '30%'],
                'value' => function($data) {
                    if ($data->description) {
                        return mb_substr($data->description, 0, 40, 'utf-8') . '...';
                    } else {
                        return ''; }
                    },
            ],
            // 'title_en',
            // 'description_en',
            // 'title_ru',
            // 'description_ru',
            // 'enabled',

            ['class' => 'yii\grid\ActionColumn','template'=>'{update} {delete}','headerOptions' =>['width' => '50'],],
        ],
    ]); ?>

</div>
