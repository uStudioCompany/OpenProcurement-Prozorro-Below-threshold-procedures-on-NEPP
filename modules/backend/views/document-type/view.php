<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\DocumentTypes */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'BackEnd', 'url' => '..'];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Document Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="document-types-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'tender_flag',
            'bid_flag',
            'award_flag',
            'contract_flag',
            'cancellation_flag',
            'recommended_flag',
            'title',
            'description',
            'title_en',
            'description_en',
            'title_ru',
            'description_ru',
            'enabled',
        ],
    ]) ?>

</div>
