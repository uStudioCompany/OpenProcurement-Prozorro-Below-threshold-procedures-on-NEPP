<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Users');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create User'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

//            'id',
            [
                'attribute' => 'id',
                'filter' => false
            ],
            'username',
//            'fio',
//            'phone',
//            'auth_key',
            // 'password_hash',
            // 'password_reset_token',
            // 'email:email',
            [
                'attribute' => 'status',
                'filter' => Html::activeDropDownList($searchModel, 'status', \app\models\User::getAllStatuses(), ['style' => 'width:150px;', 'class' => 'form-control', 'prompt' => 'Не выбрано']),
                'value' => function ($model) {
                    return \app\models\User::getAllStatuses()[$model->status];
                },
            ],
//             'status',
            // 'created_at',
            // 'updated_at',
//             'company_id',
            [
                'label'=>'Название компании',
                'attribute' => 'company_id',
//                'value' => 'company_id.legalName'
//                'filter' => Html::activeDropDownList($searchModel, 'company_id', \yii\helpers\ArrayHelper::map(\app\models\Companies::find()->asArray()->all(), 'id', 'legalName'), ['style' => 'width:150px;', 'class' => 'form-control', 'prompt' => 'Не выбрано']),
                'value' => function ($model) {
                    return \app\models\Companies::findOne($model->company_id)->legalName;
                },
            ],
            // 'is_owner',
            // 'activationcode',
            // 'subscribe_status',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
