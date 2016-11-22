<?php
use yii\helpers\Html;
use yii\helpers\Json;
use app\models\Companies;

$isTenderOwner = Companies::checkCompanyIsTenderOwner($tenders->id, $tenders);
if ($tenders->tender_type == 1) {
    foreach ($tender->qualifications as $q => $qualification) {
        if ($qualification->bidID == $bid['id'] && $qualification->status == 'pending') {
            echo $form->field($tender->qualifications[$q], '[' . $k . ']id')->hiddenInput()->label(false);
            $qualId = $tender->qualifications[$q]->id;
        }
    }
} elseif ($tenders->tender_type == 2) {
    echo Html::hiddenInput('Qualifications[' . $k . '][id]', $qualificationId);
    $qualId = $qualificationId;
}

//проверяем была ли нажата квалификация
$needForm = true;
if ($userAction = \app\models\Tenders::findOne($tenders->id)->user_action) {
    $userAction = Json::decode($userAction);
    foreach ($userAction['Qualifications'] as $i => $item) {
        if ($i == $qualId) {
            $action = $item;
            $needForm = false;
            break;
        }
    }
};


?>

<?php if ($needForm && $isTenderOwner) { ?>

    <?php
    echo Html::dropDownList('Qualifications[' . $k . '][action]', null,
        [
            'active' => Yii::t('app','qualification_active'), 'unsuccessful' => Yii::t('app','qualification_unsuccessful')
        ], [
            'class' => 'choose_prequalification',
        ]);

    ?>

    <a role="button" class="btn btn-warning col-md-5 uploadfile"
                 href="javascript:void(0)"><?= Yii::t('app', 'add file') ?></a>

    <div class="active">

        <?php
        $tender->qualifications[$k]->qualified = false;
        $tender->qualifications[$k]->eligible = false;
        echo $form->field($tender->qualifications[$k], '[' . $k . ']qualified')->checkbox();
        echo $form->field($tender->qualifications[$k], '[' . $k . ']eligible')->checkbox();

        echo Html::submitButton(Yii::t('app', 'qualification_active'), ['class' => 'btn btn-success btn-submitform_qualification', 'name' => 'send_prequalification']);
        ?>
    </div>

    <div class="unsuccessful">

        <div class="cause">

            <?= $form->field($tender->qualifications[$k], 'cause')->checkboxList(
                [
                    'Не вiдповiдає квалiфiкацiйним критерiям.' => '(Учасник не вiдповiдає квалiфiкацiйним (квалiфiкацiйному) критерiям, установленим в тендернiй документацiї)',
                    'Наявнi пiдстави, зазначенi у статтi 17.' => '(Наявнi пiдстави для вiдхилення тендерної пропозицiї, зазначенi у статтi 17 i частинi сьомiй статтi 28 Закону Про публiчнi закупiвлi)',
                    'Не вiдповiдає вимогам тендерної документацiї.' => '(Тендерна пропозицiя не вiдповiдає вимогам тендерної документацiї)',
                ]); ?>
        </div>

        <?= $form->field($tender->qualifications[$k], '[' . $k . ']description')->textarea();

        echo Html::submitButton(Yii::t('app', 'qualification_unsuccessful'), ['class' => 'btn btn-danger btn-submitform_qualification', 'name' => 'send_prequalification']);
        ?>

    </div>

<?php } else {
    if (Companies::getCompanyBusinesType() == 'buyer' && $isTenderOwner) { //если это покупатель и хозяин тендера
//        Yii::$app->VarDumper->dump($qualId, 10, true, true);
        echo Html::button(Yii::t('app', 'Накласти ЕЦП'), [
            'class' => 'sign_btn_qualification btn btn-warning',
            'qualId' => $qualId,
            'tenderId' => $tenders->id,
            'tid' => $tenders->tender_id,
            'action' => $action,
            'data-loading-text' => '<i class=\'fa fa-spinner fa-spin \'></i>' . Yii::t('app', ' Зачекайте')
        ]);
    }

} ?>

