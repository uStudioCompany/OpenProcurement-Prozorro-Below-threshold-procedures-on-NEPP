<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\InvoiceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Invoices');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invoice-index m_viewlist-wrap">
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

    <p>
        <?= Html::a(Yii::t('app', 'Create Invoice'), ['create-invoice'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'contentOptions' => ['style' => 'vertical-align: middle;'],
            ],
            [
                'attribute' => 'destination',
                'label' => Yii::t('app', 'Опис платежу'),
                'headerOptions' => ['class' => 'hr-dt-table', 'width' => '*'],
                'format' => 'raw',
                'value' => function ($data) {
                    if (!empty($data->payment)){
                        $td1 = Html::tag('td', Yii::t('app', 'Destination'), ['style' => 'width:20%']);
                        $td2 = Html::tag('td', $data->payment['destination'], ['style' => 'padding: 0 10px 0 10px']);
                        $tr1 = Html::tag('tr', $td1 . $td2);

                        $td3 = Html::tag('td', Yii::t('app', 'Платник'));
                        $td4 = Html::tag('td', $data['companies']['legalName'], ['style' => 'padding: 0 10px 0 10px']);
                        $tr2 = Html::tag('tr', $td3 . $td4);

                        $td5 = Html::tag('td', Yii::t('app', 'Рахунок сформовано'));
                        $td6 = Html::tag('td', Yii::$app->formatter->asDatetime($data['created_at']), ['style' => 'padding: 0 10px 0 10px']);
                        $tr3 = Html::tag('tr', $td5 . $td6);

                        $td7 = Html::tag('td', Yii::t('app', 'Рахунок сплачено'));
                        $td8 = Html::tag('td', Yii::$app->formatter->asDatetime($data['payed_at']), ['style' => 'padding: 0 10px 0 10px']);
                        $tr4 = Html::tag('tr', $td7 . $td8);

                        $td9 = Html::tag('td', Yii::t('app', 'Сума платежу'));
                        $td10 = Html::tag('td', $data->payment['amount'] . Yii::t('app', 'UAH'), ['style' => 'padding: 0 10px 0 10px']);
                        $tr5 = Html::tag('tr', $td9 . $td10);

                        $table = Html::tag('table', $tr1 . $tr2 . $tr3 . $tr4 . $tr5, ['style' => 'width: 100%;']);
                    }

                    else{
                        $td1 = Html::tag('td', Yii::t('app', 'Destination'), ['style' => 'width:20%']);
                        $td2 = Html::tag('td', $data->destination, ['style' => 'padding: 0 10px 0 10px']);
                        $tr1 = Html::tag('tr', $td1 . $td2);

                        $td3 = Html::tag('td', Yii::t('app', 'Платник'));
                        $td4 = Html::tag('td', $data['companies']['legalName'], ['style' => 'padding: 0 10px 0 10px']);
                        $tr2 = Html::tag('tr', $td3 . $td4);

                        $td5 = Html::tag('td', Yii::t('app', 'Рахунок сформовано'));
                        $td6 = Html::tag('td', Yii::$app->formatter->asDatetime($data['created_at']), ['style' => 'padding: 0 10px 0 10px']);
                        $tr3 = Html::tag('tr', $td5 . $td6);

                        $td7 = Html::tag('td', Yii::t('app', 'Invoice amount'));
                        $td8 = Html::tag('td', $data->amount . Yii::t('app', 'UAH'), ['style' => 'padding: 0 10px 0 10px']);
                        $tr4 = Html::tag('tr', $td7 . $td8);

                        $table = Html::tag('table', $tr1 . $tr2 . $tr3 . $tr4, ['style' => 'width: 100%;']);
                    }
                    return $table;
                }
            ],
            [
                'attribute' => 'status',
                'value' => function($data){
                    switch ($data->status){
                        case 'payed' : return Html::tag('span', Yii::t('app', $data->status), ['style' => 'color: green']); break;
                        case 'pending' : return Html::tag('span', Yii::t('app', $data->status), ['style' => 'color: red']); break;
                        default : return Yii::t('app', $data->status);
                    }
                },
                'format' => 'raw',
                'contentOptions' => ['style' => 'vertical-align: middle;'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header'=> Yii::t('app', 'View invoice'),
                'headerOptions' => ['width' => '80'],
                'template' => '{view}',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        $res = Html::a(Yii::t('app', 'View'), ['/seller/cash-flow/shet-factura-pdf?id=' . $model->id], ['class' => 'btn btn-success', 'target' => '_blank', 'style' => 'width: 100%;']);
                        return $res;
                    },
                ],
                'contentOptions' => ['style' => 'vertical-align: middle;'],
            ],
        ],
    ]); ?>
</div>