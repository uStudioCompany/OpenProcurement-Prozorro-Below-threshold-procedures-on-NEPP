<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Invoice */

$this->title = (!$companyModel)? Yii::t('app', 'Create Invoice') : Yii::t('app', 'Реквізити компаніі');

$this->params['breadcrumbs'][] = ['label' => 'Invoices', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="col-md-12">

    <h1><?= Html::encode($this->title) ?>
    <?php if (!$companyModel){ ?>
        <span style="float: right;">
            <?= Html::a(\Yii::t('app', 'Back to the list'), ['view-invoices'], [
                'class' => 'btn btn-success',
            ]) ?>
        </span>
    <?php }?>
    </h1>
    <?= $this->render('_form', [
        'model' => $model,
        'companyModel' => $companyModel,
    ]) ?>

</div>
