<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\CompanyType */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Company Type',
]) . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Admin', 'url' => '..'];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Company types'), 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $model->name;
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="company-type-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
