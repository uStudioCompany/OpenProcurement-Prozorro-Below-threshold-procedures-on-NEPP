<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\backend\models\CashFlowReason */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Cash Flow Reason',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Cash Flow Reasons'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="cash-flow-reason-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
