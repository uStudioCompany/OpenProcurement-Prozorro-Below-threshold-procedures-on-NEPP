<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\CountrySheme */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Country Sheme',
]) . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Country Shemes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="country-sheme-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
