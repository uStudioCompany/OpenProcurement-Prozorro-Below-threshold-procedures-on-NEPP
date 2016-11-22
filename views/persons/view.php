<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Persons */

$this->title = $model->userSurname . ' ' . $model->userName . ' ' . $model->userPatronymic;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Persons'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="persons-view">

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

    <ul class="nav nav-tabs">
        <li class="active"><a data-toggle="tab" href="#panel1">Укр.</a></li>
        <li><a data-toggle="tab" href="#panel2">Eng.</a></li>
<!--        <li><a data-toggle="tab" href="#panel3">Рус.</a></li>-->
    </ul>

    <div class="tab-content">
        <div id="panel1" class="tab-pane fade in active">

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'userName',
                    'userSurname',
                    'userPatronymic',
                    'email:email',
                    'telephone',
                    'faxNumber',
                    'mobile',
                    'url:url',
                    'company_id',
                    'availableLanguage'
                ],
            ]) ?>

        </div>
        <div id="panel2" class="tab-pane fade">

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
//            'id',
                    [
                        'label' => 'Name',
                        'attribute' => 'userName_en',
                    ],
                    [
                        'label' => 'Surname',
                        'attribute' => 'userSurname_en',
                    ],
                    [
                        'label' => 'Patrunomic',
                        'attribute' => 'userPatronymic_en',
                    ],
                    'email:email',
                    'telephone',
                    'faxNumber',
                    'mobile',
                    'url:url',
                    'company_id',
                ],
            ]) ?>

        </div>
        <div id="panel3" class="tab-pane fade">

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'label' => 'Имя',
                        'attribute' => 'userName_ru',
                    ],
                    [
                        'label' => 'Фамилия',
                        'attribute' => 'userSurname_ru',
                    ],
                    [
                        'label' => 'Отчество',
                        'attribute' => 'userPatronymic_ru',
                    ],
                    'email:email',
                    'telephone',
                    'faxNumber',
                    'mobile',
                    'url:url',
                    'company_id',
                ],
            ]) ?>

        </div>

    </div>

</div>
