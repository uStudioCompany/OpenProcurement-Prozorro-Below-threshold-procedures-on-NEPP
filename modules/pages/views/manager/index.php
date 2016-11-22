<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\pages\Module;
use app\modules\pages\models\Page;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\pages\models\PageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Module::t('MODULE_NAME');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-header">
    <h1><?= Html::encode($this->title) ?></h1>
</div>
<p>
    <? if (\app\modules\pages\models\PagesTree::checkExistRoot(\app\modules\pages\models\PagesTree::find()->where(['icon' => 'folder'])->all())) : ?>
        <?= Html::a(Module::t('CREATE'), ['create'], ['class' => 'btn btn-success']); ?>
    <? endif; ?>
        <?= Html::a(Yii::t('app', 'Explorer'), ['pages-tree/index'], ['class' => 'btn btn-success']); ?>
</p>
<?php
if (Yii::$app->session->hasFlash('success')) { ?>
    <div class="alert alert-success fade in">
        <a href="#" class="close" data-dismiss="alert">&times;</a>
        <?= Yii::$app->session->getFlash('success'); ?>
    </div>
<?php } ?>
<?= GridView::widget([
    'tableOptions' => [
        'class' => 'table table-striped',
    ],
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
//        'id',
//        'title',
        [
            'attribute' => 'title',
            'format'=>'raw',
            'value' => function($model) {
                return Html::a(Html::encode($model->title),\yii\helpers\Url::base().'/pages/'.$model->alias, ['target' => '_blank']);
            },
        ],
        'alias',
        [
            'attribute' => 'created_at',
            'format'=>'datetime',
            'filter' => false,
        ],
        [
            'attribute' => 'updated_at',
            'format'=>'datetime',
            'filter' => false,
        ],
//        'created_at:datetime',
//        'updated_at:datetime',
        [
            'attribute' => 'published',
            'filter' => Page::publishedDropDownList(),
            'value' => function($model) {
                return Yii::$app->formatter->asBoolean($model->published);
            },
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => "{update}\n{delete}",
        ],
    ],
]); ?>
