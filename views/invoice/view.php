<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Invoice */

$this->title = Yii::t('app', 'Invoice') . " #" . $model->code;
$this->params['breadcrumbs'][] = ['label' => 'Invoices', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invoice-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(\Yii::t('app', 'Back to cabinet'), ['/'], [
            'class' => 'btn btn-success',
        ]) ?>
        <?= Html::a(\Yii::t('app', 'Back to the list'), ['view-invoices'], [
            'class' => 'btn btn-success',
        ]) ?>
        <?php
            echo Html::a(Yii::t('app', 'View invoice'), ['/seller/cash-flow/shet-factura-pdf?id=' . $model->id], ['class' => 'btn btn-success', 'target' => '_blank']);
        ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            'code',
            'amount',
            //'balance_id',
            'destination',
            [
                'attribute' => 'status',
                'value' => Yii::t('app', $model->status),
            ],
            [
                'attribute' => 'created_at',
                'value' => $model->createdAt
            ],
            [
                'attribute' => 'payed_at',
                'value' => $model->payedAt
            ],
        ],
    ]) ?>

</div>
