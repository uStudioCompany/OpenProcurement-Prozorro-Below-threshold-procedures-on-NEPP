<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\CountrySheme */

$this->title = Yii::t('app', 'Create Country Sheme');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Country Shemes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="country-sheme-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
