<?php

use yii\grid\GridView;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PaymentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Payments');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="payment-index tenders-index m_viewlist-wrap">
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => [
            'class' => 'table table-striped'
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn', 'headerOptions' => ['width' => '30'],],
            [
                'attribute' => 'payment_id',
                'headerOptions' => ['class' => 'hr-dt-table', 'width' => '30'],
                'format' => 'raw',
            ],
            [
                'attribute' => 'status',
                'headerOptions' => ['class' => 'hr-dt-table', 'width' => '*'],
                'format' => 'raw',
                'value' => function ($data) { return Yii::t('app', $data['status']); }
            ],
            [
                'attribute' => 'destination',
                'label' => Yii::t('app', 'Опис платежу'),
                'headerOptions' => ['class' => 'hr-dt-table', 'width' => '*'],
                'format' => 'raw',
                'value' => function ($data) {
                    $amountP = Html::tag('td', Yii::t('app', 'Отримано') . ': ' . $data->amount . Yii::t('app', 'UAH'));
                    $destinationP = ($data->status != 'unknown')? Html::tag('td', Yii::t('app', 'Компанія') . ': ' . $data['companies']['legalName'] . '<br>' . $data->destination) : Html::tag('td', Yii::t('app', 'EDRPOU') . ": " . json_decode($data->json)->payer->EDRPOU. '<br>' . $data->destination);
                    $dateP = Html::tag('td', Yii::$app->formatter->asDatetime($data['created_at']));
                    $trP = Html::tag('tr', $amountP . $destinationP . $dateP);

                    /////////////////
                    $amountI = Html::tag('td', '');
                    if (is_object($cashFlowModel = \app\models\CashFlow::find()->where(['payment_id' => $data->id])->one())){
                        $amountI = Html::tag('td', Yii::t('app', 'Виставлено') . ': ' . $data['invoice']['amount']  . Yii::t('app', 'UAH'));
                        $destinationI = Html::tag('td', $data['invoice']['destination']);
                        $dateI = Html::tag('td', Yii::$app->formatter->asDatetime($data['invoice']['created_at']));

                    }
                    $trI = Html::tag('tr', $amountI . $destinationI . $dateI);
                    $invoiceButton = Html::tag('td', Html::a(
                        Yii::t('app', 'View'),
                        ['/seller/cash-flow/shet-factura-pdf?id='.$data->invoice_id],
                        ['class' => 'btn btn-success', 'target' => '_blank', 'style' => 'width: 100%']
                    ));
                    if ($data['status'] == 'unknown'){
                        $invoiceButton = '';
                    }
                    $trInvoice = Html::tag('tr', Html::tag('td', '') . $invoiceButton . Html::tag('td', ''));
                    $table = Html::tag('table', $trP . $trI . $trInvoice);
                    return $table;
                }
            ],
            [
                'attribute' => 'companies.legalName',
                'headerOptions' => ['class' => 'hr-dt-table', 'width' => '0'],
                'format' => 'raw',
                'label' => '',
                'visible' => false,
            ],
        ],
    ]); ?>
</div>
