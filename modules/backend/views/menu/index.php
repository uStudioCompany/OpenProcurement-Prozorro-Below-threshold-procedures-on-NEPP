<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\MenuSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Menus');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="menu-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

//            'id',
//            'pid',
            [
                'attribute' => 'pid',
                'label' => Yii::t('app','Parent page'),
                'value' => function($model){
                    return (\app\modules\backend\models\Menu::findOne(['id'=>$model->pid]) !== NULL) ? \app\modules\backend\models\Menu::findOne(['id'=>$model->pid])->name : Yii::t('app','none');
                }
            ],
            'name',
            'name_en',
            'name_ru',
            // 'url:url',
             'order',
            [
                'attribute' => 'published',
                'filter' => \app\modules\backend\models\Menu::publishedDropDownList(),
                'value' => function($model) {
                    return Yii::$app->formatter->asBoolean($model->published);
                },
            ],
//             'published',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?></div>
