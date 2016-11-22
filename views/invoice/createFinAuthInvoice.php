<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Invoice */

$this->title = Yii::T('app', 'Create Invoice');
$this->params['breadcrumbs'][] = ['label' => 'Invoices', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invoice-create">

    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a(\Yii::t('app', 'Back to the list'), ['view-invoices'], [
            'class' => 'btn btn-success',
        ]) ?>
    </p>

    <?= $this->render('_form', [
        'model' => $model,
        'companyModel' => $companyModel,
    ]) ?>

</div>
