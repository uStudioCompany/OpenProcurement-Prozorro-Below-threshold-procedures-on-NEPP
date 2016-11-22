<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CashFlowSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Payments');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cash-flow-index m_viewlist-wrap">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            //'id',
            [
                'attribute' => 'way',
                'value' => function($data){
                    return Yii::t('app', $data->way);
                }
            ],
            [
                'attribute' => 'amount',
                'value' => function($data){
                    return $data->amount. Yii::t('app', 'UAH');
                },
                'format' => 'raw',

            ],
            //'balance_id',
            //'tender_id',
            [
                'attribute' => 'tender_name',
                'value' => function($data){
                    $tender_title = $data->tender->title;
                    $tender_id = $data->tender->id;
                    if($tender_id){
                        return Html::a($tender_title, ['/tender/view', 'id' =>  $tender_id]);
                    }
                    return '-';
                },
                'format' => 'raw',

            ],
            [
                'attribute' => 'tender_json',
                'value' => function($data) {
                    //return $data->lot_id;
                    $tender = json_decode($data->tender->json)->{'Tender'};
                    $descr = "-";
                    if (isset($tender->{'lots'})) {
                        foreach ($tender->{'lots'} as $item) {
                            if ($item->id == $data->lot_id) {
                                $descr = $item->{'description'};
                            }
                        }
                    }
                    return $descr;
                },
            ],
            //'lot_description',
//            [
//                'attribute' => 'payment_id',
//                'value' => function($data){
//                    $payment = '-';
//                    if ($data->payment_id){
//                        $payment = $data->payment_id;
//                    }
//
//                    return Html::a(
//                        $data->payment->codes,
//                        ['/payment/view?id='.$payment]
//                    );
//                },
//                'format' => 'raw',
//            ],
            [
                'attribute' => 'invoice_id',
                'value' => function($data){
                    if ($data->way == 'in' ){
                        return $data->invoice->code . "<br>" . Html::a(
                            Yii::t('app', 'View'),
                            ['/seller/cash-flow/shet-factura-pdf?id='.$data->invoice_id],
                            ['class' => 'btn btn-success', 'target' => '_blank', 'style' => 'width: 100%']
                        );
                    }
                    return '';
                },
                'format' => 'raw',
                'headerOptions' => ['width' => '240'],
            ],
            [
                'attribute' => 'cash_flow_reason_id',
                'value' => function($data){
                    switch (Yii::$app->language){
                        case 'uk-UA': return ($data->cashFlowReason->value) ? $data->cashFlowReason->value : '-';
                            break;
                        case 'ru-RU': return ($data->cashFlowReason->value_ru) ? $data->cashFlowReason->value_ru : '-';
                            break;
                        case 'en-US': return ($data->cashFlowReason->value_en) ? $data->cashFlowReason->value_en : '-';
                            break;
                        default: return ($data->cashFlowReason->value) ? $data->cashFlowReason->value : '-';
                            break;
                    }
                },
            ],
//            [
//                'attribute' => 'created_at',
//                'value' => function($data){
//                    return $data->createdAt;
//                },
//
//            ],

            //'created_at:datetime',
            'payed_at:datetime',

            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
