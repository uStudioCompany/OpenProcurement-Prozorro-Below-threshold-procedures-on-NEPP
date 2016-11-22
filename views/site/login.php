<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

$this->title = 'Авторизация на площадке';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login m_login-wrap">

    <?php if (Yii::$app->session->hasFlash('message')) { ?>
        <div class="bs-example">
            <div class="alert alert-success fade in">
                <a href="#" class="close" data-dismiss="alert">&times;</a><?= Yii::$app->session->getFlash('message'); ?>
            </div>
        </div>
    <?php } ?>

    <?php
    if (Yii::$app->session->hasFlash('recovery_success')) {
        echo $this->render('../common/flash_success', [
            'data' => Yii::$app->session->getFlash('recovery_success')
        ]);
    }
    ?>

    <?php
    if (Yii::$app->session->hasFlash('recovery_success_finish')) {
        echo $this->render('../common/flash_success', [
            'data' => Yii::$app->session->getFlash('recovery_success_finish')
        ]);
    }
    ?>

    <?php
    if (Yii::$app->session->hasFlash('Forbidden')) {
        echo $this->render('../common/flash_fail', [
            'data' => Yii::$app->session->getFlash('Forbidden')
        ]);
    }
    ?>

<!--    <div class="jumbotron">-->
<!--        <div class="container">-->
<!--            <h1>--><?//= Yii::t('app', 'site_login') ?><!--</h1>-->
<!--        </div>-->
<!--    </div>-->


    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'options' => ['class' => 'form-horizontal'],
        'fieldConfig' => [
            // 'template' => "{label}\n<div class=\"col-md-3\">{input}</div>\n<div class=\"col-md-8\">{error}</div>",
            'labelOptions' => ['class' => 'col-md-3 control-label'],
        ],
    ]); ?>

    <?= $form->field($model, 'username', [
        'options'=>[
            'class'=>'form-group m_form-group',
        ],
        'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>"
    ]) ?>

    <?php
    $forgotten_url = Yii::$app->urlManager->createAbsoluteUrl(['/site/passwordreset']);
    echo $form->field($model, 'password', [
        'options'=>[
            'class'=>'form-group m_form-group',
        ],
        'template' => "{label}\n<div class=\"col-md-6\">{input}</div><div class=\"col-md-2 lnk-forgotten\"><a href=\"$forgotten_url\" class=\"\">" . Yii::t('app', 'remindPassword') . "</a></div>\n<div class=\"col-md-3\">{error}</div>",
    ])->passwordInput();
    ?>

    <!-- <div class="form-group"> -->
    <?php
    $btn = Html::submitButton('Вход', ['class' => 'btn btn-default btn-submitform', 'name' => 'login-button']);
    echo $form->field($model, 'rememberMe',[
        'options'=>[
            'class'=>'form-group m_form-group',
        ],
    ])->checkbox([
        'template' => "<div class=\"no-pad col-md-12 m_remember\">{input} {label}</div><div class=\"col-md-6\">$btn</div>",
    ]);

//    echo Html::a(Yii::t('app','Register'),Yii::$app->urlManager->createAbsoluteUrl(['register']));
    ?>

    <!-- </div> -->

    <?php ActiveForm::end(); ?>
<!--    --><?//= Html::a(Yii::t('app', 'Tenders'),  ['/tenders/'],  ['class' => 'btn btn-success']) ?>
<!--    --><?//= Html::a(Yii::t('app', 'View Plans'),  ['/plan/'],  ['class' => 'btn btn-success']) ?>
</div>
<br/>
