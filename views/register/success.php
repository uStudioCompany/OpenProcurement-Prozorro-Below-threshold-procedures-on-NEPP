<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
// use yii\helpers\ArrayHelper;
// use app\models\Countries;

/* @var $this yii\web\View */
/* @var $model app\models\Companies */
/* @var $form ActiveForm */

$this->title = Yii::t('app','Вiтаємо з успiшною реєстрацiєю!');
$persLabel = $persons->attributeLabels();
$companyLabel = $company->attributeLabels();
?>
<div class="register-success">
    <?php

    if (Yii::$app->session->hasFlash('join_success')) {
        echo $this->render('../common/flash_success', [
            'data' => Yii::$app->session->getFlash('join_success')
        ]);
    }
    if (Yii::$app->session->hasFlash('register_success')) {
        echo $this->render('../common/flash_success', [
            'data' => Yii::$app->session->getFlash('register_success')
        ]);
    }
    ?>

    <?=$this->render('/site/head', [
        'title' => $this->title,
        'descr' => Yii::t('app','Вам залишилось лише перевiрити коректнiсть наданих даних та пройти процедуру iдентифiкацiї')
    ]); ?>

<!--    --><?php //$form = ActiveForm::begin([
//        'action' => Url::to('register/eds', true),
//        'options' => ['class' => 'form-horizontal'],
//        'fieldConfig' => [
//            'labelOptions' => ['class' => 'col-md-3 control-label'],
//        ],
//    ]); ?>

    <div class="info-block">
        <h4 class="m_user-info"><?=Yii::t('app','AUTHORIZED CONTACT PERSON')?></h4>
        <div class="form-group">
            <label class="col-md-3 control-label" for="user-username"><?=$persLabel['userName'] ?></label>
            <div class="col-md-6">
                <span class="reg-info" id="user-username"><?=Html::encode($persons->userName) ?></span class="reg-info">
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label" for="user-userSurname"><?=$persLabel['userSurname'] ?></label>
            <div class="col-md-6">
                <span class="reg-info" id="user-userSurname"><?=Html::encode($persons->userSurname) ?></span class="reg-info">
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label" for="user-userPatronymic"><?=$persLabel['userPatronymic'] ?></label>
            <div class="col-md-6">
                <span class="reg-info" id="user-userPatronymic"><?=Html::encode($persons->userPatronymic) ?></span class="reg-info">
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label" for="user-email"><?=$persLabel['email'] ?></label>
            <div class="col-md-6">
                <span class="reg-info" id="user-email"><?=Html::encode($persons->email) ?></span class="reg-info">
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label" for="user-telephone"><?=$persLabel['telephone'] ?></label>
            <div class="col-md-6">
                <span class="reg-info" id="user-telephone"><?=Html::encode($persons->telephone) ?></span class="reg-info">
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label" for="user-faxNumber"><?=$persLabel['faxNumber'] ?></label>
            <div class="col-md-6">
                <span class="reg-info" id="user-faxNumber"><?=Html::encode($persons->faxNumber) ?></span class="reg-info">
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label" for="user-mobile"><?=$persLabel['mobile'] ?></label>
            <div class="col-md-6">
                <span class="reg-info" id="user-mobile"><?=Html::encode($persons->mobile) ?></span class="reg-info">
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label" for="user-url"><?=$persLabel['url'] ?></label>
            <div class="col-md-6">
                <span class="reg-info" id="user-url"><?=Html::encode($persons->url) ?></span class="reg-info">
            </div>
        </div>
    </div>

    <div class="info-block">
        <h4 class="m_org-info"><?=Yii::t('app','ABOUT PARTICIPANT')?></h4>
        <div class="form-group">
            <label class="col-md-3 control-label" for="company-LegalType"><?=$companyLabel['LegalType'] ?></label>
            <div class="col-md-6">
                <span class="reg-info" id="company-LegalType"><?=Html::encode($company->companyType->name) ?></span class="reg-info">
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label" for="company-legalName"><?=$companyLabel['legalName'] ?></label>
            <div class="col-md-6">
                <span class="reg-info" id="company-legalName"><?=Html::encode($company->legalName) ?></span class="reg-info">
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label" for="company-registrationCountryName"><?=$companyLabel['registrationCountryName'] ?></label>
            <div class="col-md-6">
                <span class="reg-info" id="company-registrationCountryName"><?=Html::encode($company->relationCountryName->name) ?></span class="reg-info">
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label" for="company-identifier"><?=$companyLabel['identifier'] ?></label>
            <div class="col-md-6">
                <span class="reg-info" id="company-identifier"><?=Html::encode($company->identifier) ?></span class="reg-info">
            </div>
        </div>
    </div>
    <div class="info-block">
        <h4 class="m_adress"><?=Yii::t('app','LEGAL ADDRESS')?></h4>
        <div class="form-group">
            <label class="col-md-3 control-label" for="company-countryName"><?=$companyLabel['countryName'] ?></label>
            <div class="col-md-6">
                <span class="reg-info" id="company-countryName"><?=Html::encode($company->countryNameSheme->name) ?></span class="reg-info">
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label" for="company-region"><?=$companyLabel['region'] ?></label>
            <div class="col-md-6">
                <span class="reg-info" id="company-region"><?=Html::encode($company->region0->name) ?></span class="reg-info">
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label" for="company-locality"><?=$companyLabel['locality'] ?></label>
            <div class="col-md-6">
                <span class="reg-info" id="company-locality"><?=Html::encode($company->locality) ?></span class="reg-info">
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label" for="company-address"><?=$companyLabel['streetAddress'] ?></label>
            <div class="col-md-6">
                <span class="reg-info" id="company-address"><?=Html::encode($company->streetAddress) ?></span class="reg-info">
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label" for="company-postalCode"><?=$companyLabel['postalCode'] ?></label>
            <div class="col-md-6">
                <span class="reg-info" id="company-postalCode"><?=Html::encode($company->postalCode) ?></span class="reg-info">
            </div>
        </div>
    </div>
    <div class="info-block">
        <h4><?=Yii::t('app','AUTHORIZED PERSON DATA (FOR CELEBRATION OF CONTRACT)')?></h4>
        <div class="form-group">
            <label class="col-md-3 control-label" for="company-fio"><?=$companyLabel['fio'] ?></label>
            <div class="col-md-6">
                <span class="reg-info" id="company-fio"><?=Html::encode($company->fio) ?></span class="reg-info">
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label" for="company-userPosition"><?=$companyLabel['userPosition'] ?></label>
            <div class="col-md-6">
                <span class="reg-info" id="company-userPosition"><?=Html::encode($company->userPosition) ?></span class="reg-info">
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label" for="company-userDirectionDoc"><?=$companyLabel['userDirectionDoc'] ?></label>
            <div class="col-md-6">
                <span class="reg-info" id="company-userDirectionDoc"><?=Html::encode($company->userDirectionDoc) ?></span class="reg-info">
            </div>
        </div>
    </div>

<!--    <div class="form-group">-->
<!--        <div class="col-md-offset-3 col-md-6">-->
<!--            --><?//= Html::submitButton(Yii::t('app', 'Пiдтвердити даннi шляхом накладання ЕЦП'), ['class' => 'btn btn-default btn-submitform']) ?>
<!--        </div>-->
<!--    </div>-->
    <?php // ActiveForm::end(); ?>

</div><!-- register-success -->