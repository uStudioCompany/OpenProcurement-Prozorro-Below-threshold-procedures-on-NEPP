<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\InviteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Invites'); //echo '<pre>'; print_r($searchModel->statuses); die();
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invite-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Invite'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => [
            'class' => 'table table-striped'
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn', 'headerOptions' => ['width' => '30'],],
            [
                'attribute' => 'fio',
                'headerOptions' => ['class' => 'hr-dt-table','width' => '*'],
                'format' => 'raw',
                'value' => function($data) {
                    return Html::a($data->fio,['view','id'=>$data->id]); },
            ],
            'email:email',
            [
                'label' => 'S',
                'attribute' => 'status',
                'format' => 'raw',
                'headerOptions' =>['class' => 'hr-dt-table','width' => '1%'],
                'filter'=>["0"=>Yii::t('app', 'invite.new'),"1"=>Yii::t('app', 'invite.confirmed'),"2"=>Yii::t('app', 'invite.refused'),],
                'value' => function($data) {
                    return '<span class="glyphicon glyphicon-'. $data->statuses_icons[$data->status] .'" title="'. Yii::t('app',$data->statuses_values[$data->status]) .'"></span>'; },
            ],
            ['class' => 'yii\grid\ActionColumn','template'=>'{update} &nbsp; {delete}','headerOptions' =>['width' => '60']],
        ],
    ]); ?>

</div>
