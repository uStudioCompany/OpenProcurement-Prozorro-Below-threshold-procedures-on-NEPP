<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Companies */

$this->title = $model->legalName;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Companies'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="companies-view">

    <h1><?= Html::encode($this->title) ?>

            <?= Html::a(Yii::t('app', 'Edit company'), ['update'], ['class' => 'btn btn-primary pull-right margin_r_20']) ?>
            <!--        --><? //= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            //            'class' => 'btn btn-danger',
            //            'data' => [
            //                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
            //                'method' => 'post',
            //            ],
            //        ]) ?>
        </h1>

    <?php if (Yii::$app->user->identity->is_owner == 1) { ?>



    <?php } ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            //'LegalType',
            [
                'attribute' => 'LegalType',
                'value' => $model->getCompanyType()->one()->name
            ],
            [
                'attribute' => 'customer_type',
                'label' => Yii::t('app','Customer Type'),
                'value' => $model->getCompanyCustomerType()->one()->name
            ],
            'legalName',
            'legalName_en',
            //'legalName_ru',
            [
                'attribute' => 'registrationCountryName',
                'value' => $model->getrelationCountryName()->one()->name
            ],

//            'moneygetId',
            'identifier',
            [
                'attribute' => 'mfo',
                'value' => $model->mfoId
            ],
            [
                'attribute' => 'bank_account',
                'value' => $model->bankAccount
            ],
            [
                'attribute' => 'bank_branch',
                'value' => $model->bankBranch
            ],
            [
                'attribute' => 'payer_pdv',
                'value' => $model->payerPdv
            ],
            [
                'attribute' => 'parent_identifier',
                'value' => $model->parentIdentifier
            ],
            [
                'attribute' => 'ipn_id',
                'value' => $model->ipnId
            ],
            'fio',
            //'fio_en',
            //'fio_ru',
            'userPosition',
            //'userPosition_en',
            //'userPosition_ru',
            'userDirectionDoc',
            //'userDirectionDoc_en',
            //'userDirectionDoc_ru',
            [
                'attribute' => 'countryName',
                'value' => $model->getrelationCountryName()->one()->name
            ],
            [
                'attribute' => 'region',
                'value' => $model->getRegion0()->one()->name
            ],


            'locality',
            //'locality_en',
            //'locality_ru',
            'streetAddress',
            //'streetAddress_en',
            //'streetAddress_ru',
            'postalCode',
//            'preferLang',
        ],
    ]) ?>

<!--    --><?php //if (count($user_list)) { ?>
<!--        <h3>--><?//= Yii::t('app', 'Persons'); ?><!--</h3>-->
<!--        --><?php //foreach ($user_list AS $user) { ?>
<!--            --><?//= DetailView::widget([
//                'model' => $user,
//                'options' => [
//                    'class' => 'table table-striped table50 table-bordered'],
//                'attributes' => [
//                    //'userName',
//                    [
//                        'label' => Yii::t('app', 'fio'),
//                        'value' => $user->userSurname. ' '. $user->userName . ' ' . $user->userPatronymic,
//                    ],
//                    //'userPatronymic',
//                    //'userSurname',
//                    'email',
//                    'telephone',
//                    'mobile',
//                ],
//            ]) ?>
<!--        --><?php //}
//    } ?>

</div>
