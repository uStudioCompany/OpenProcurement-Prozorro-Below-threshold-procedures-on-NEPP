<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PersonsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Persons');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invite-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Persons'), ['create'], ['class' => 'btn btn-success']) ?>

        <?= Html::a(Yii::t('app', 'Reset'), ['/persons/'], ['class' => 'btn btn-success']) ?>
    </p>

    <ul class="nav nav-tabs">
        <li class="active"><a data-toggle="tab" href="#panel1">Укр.</a></li>
        <li><a data-toggle="tab" href="#panel2">Eng.</a></li>
        <li><a data-toggle="tab" href="#panel3">Рус.</a></li>
    </ul>

    <div class="tab-content">
        <div id="panel1" class="tab-pane fade in active">
            <?php Pjax::begin(); ?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'tableOptions' => [
                    'class' => 'table table-striped'
                ],
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn', 'headerOptions' => ['width' => '30'],],
                    [
                        'attribute' => 'userSurname',
                        'headerOptions' => ['class' => 'hr-dt-table', 'width' => '*'],
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'userName',
                        'headerOptions' => ['class' => 'hr-dt-table', 'width' => '*'],
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'email',
                        'headerOptions' => ['class' => 'hr-dt-table', 'width' => '*'],
                        'format' => 'email',

                    ],
                    [
                        'attribute' => 'telephone',
                        'headerOptions' => ['class' => 'hr-dt-table', 'width' => '*'],
                        'format' => 'raw',
                    ],
                    ['class' => 'yii\grid\ActionColumn', 'template' => '{view}{update}{link}',],
                ],
            ]);
            Pjax::end();
            ?>
        </div>
        <div id="panel2" class="tab-pane fade">
            <?php Pjax::begin(); ?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'tableOptions' => [
                    'class' => 'table table-striped'
                ],
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn', 'headerOptions' => ['width' => '30'],],
                    [
                        'attribute' => 'userSurname_en',
                        'headerOptions' => ['class' => 'hr-dt-table', 'width' => '*'],
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'userName_en',
                        'headerOptions' => ['class' => 'hr-dt-table', 'width' => '*'],
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'email',
                        'headerOptions' => ['class' => 'hr-dt-table', 'width' => '*'],
                        'format' => 'email',

                    ],
                    [
                        'attribute' => 'telephone',
                        'headerOptions' => ['class' => 'hr-dt-table', 'width' => '*'],
                        'format' => 'raw',
                    ],
                    ['class' => 'yii\grid\ActionColumn', 'template' => '{view}{update}{link}',],
                ],
            ]);
            Pjax::end();
            ?>
        </div>
        <div id="panel3" class="tab-pane fade">
            <?php Pjax::begin(); ?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'tableOptions' => [
                    'class' => 'table table-striped'
                ],
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn', 'headerOptions' => ['width' => '30'],],
                    [
                        'attribute' => 'userSurname_ru',
                        'headerOptions' => ['class' => 'hr-dt-table', 'width' => '*'],
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'userName_ru',
                        'headerOptions' => ['class' => 'hr-dt-table', 'width' => '*'],
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'email',
                        'headerOptions' => ['class' => 'hr-dt-table', 'width' => '*'],
                        'format' => 'email',

                    ],
                    [
                        'attribute' => 'telephone',
                        'headerOptions' => ['class' => 'hr-dt-table', 'width' => '*'],
                        'format' => 'raw',
                    ],
                    ['class' => 'yii\grid\ActionColumn', 'template' => '{view}{update}{link}',],
                ],
            ]);
            Pjax::end();
            ?>
        </div>

    </div>


</div>
