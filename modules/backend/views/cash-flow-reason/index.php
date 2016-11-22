<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\backend\models\CashFlowReasonSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Cash Flow Reasons');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cash-flow-reason-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => [
            'class' => 'table table-striped'
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn', 'headerOptions' => ['width' => '30'],],
            [
                'attribute' => 'value',
                'headerOptions' => ['class' => 'hr-dt-table', 'width' => '*'],
                'format' => 'raw',
                'value' => function ($data) {
                    return Html::a($data->value, ['view', 'id' => $data->id]);
                },
            ],
            [
                'attribute' => 'value_en',
                'headerOptions' => ['class' => 'hr-dt-table', 'width' => '*'],
                'format' => 'raw',
                'value' => function ($data) {
                    return Html::a($data->value_en, ['view', 'id' => $data->id]);
                },
            ],
            [
                'attribute' => 'value_ru',
                'headerOptions' => ['class' => 'hr-dt-table', 'width' => '*'],
                'format' => 'raw',
                'value' => function ($data) {
                    return Html::a($data->value_ru, ['view', 'id' => $data->id]);
                },
            ],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
