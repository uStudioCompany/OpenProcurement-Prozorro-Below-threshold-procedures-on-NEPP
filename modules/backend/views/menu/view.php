<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Menu */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Menus'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="menu-view">

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
//            'id',
            [
                'attribute' => 'pid',
                'label' => Yii::t('app', 'Pid'),
                'value' => isset(\app\modules\backend\models\Menu::findOne(['id'=>$model->pid])->name) ? \app\modules\backend\models\Menu::findOne(['id'=>$model->pid,'published'=>1])->name : Yii::t('app','none'),
            ],
            'name',
            'name_en',
            'name_ru',
            'url:url',
            'order',
            'published',
        ],
    ]) ?>

</div>
