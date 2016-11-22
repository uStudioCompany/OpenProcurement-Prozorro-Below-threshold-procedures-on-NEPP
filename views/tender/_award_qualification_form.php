<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Json;

//проверяем была ли нажата квалификация
$needForm = true;
if ($userAction = \app\models\Tenders::findOne($tendersId)->user_action) {
    $userAction = Json::decode($userAction);
//    Yii::$app->VarDumper->dump($userAction, 10, true);die;
    if (isset($userAction['Awards'])) {
        foreach ($userAction['Awards'] as $i => $item) {
            if ($i == $awardId) {
                $action = $item;
                $needForm = false;
                break;
            }
        }
    }

};

echo Html::hiddenInput(null, $tenders->tender_method, [
    'id' => 'tender_method'
]);

if ($needForm && \app\models\tenderModels\Award::checkAllowedQualificationAward($currentAward, $tenders)) {

    echo Html::dropDownList('Award[' . $k . '][action]', null,
        [
            'active' => 'Визнати переможцем', 'unsuccessful' => 'Дисквалiфiкувати'
        ], [
//                                                'class' => 'form-control col-md-3',
            'class' => 'choose_prequalification',
        ]);

//if ($tenders->tender_type == 1) {
//    foreach ($tender->qualifications as $q => $qualification) {
//        if ($qualification->bidID == $bid['id'] && $qualification->status == 'pending') {
//            echo $form->field($tender->qualifications[$q], '[' . $k . ']id')->hiddenInput()->label(false);
//        }
//    }
//} elseif ($tenders->tender_type == 2) {
//    echo Html::hiddenInput('Award['.$k.'][id]',$awardId );
//}


    ?>


    <div class="active">
        <? $form = ActiveForm::begin(); ?>
        <a role="button" class="btn btn-warning col-md-2 uploadfile"
           href="javascript:void(0)"><?= Yii::t('app', 'add protocol') ?></a>

        <input type="hidden" value="active" name="type">
        <?php
        echo Html::hiddenInput('awardId', $awardId);

            $tender->awards[$k]->qualified = false;
            $tender->awards[$k]->eligible = false;
            echo $form->field($tender->awards[$k], '[' . $k . ']qualified')->checkbox();

            echo $form->field($tender->awards[$k], '[' . $k . ']eligible')->checkbox();

        if ($tenders->tender_method == 'open_belowThreshold') {
            echo Html::checkbox('need_award_esign', false, ['class' => 'need_tender_esign', 'label' => Yii::t('app', 'Накласи ЕЦП')]);
        }
        echo Html::submitButton(Yii::t('app', 'Визнати переможцем'), ['class' => 'btn btn-success btn-submitform_award', 'name' => 'send_prequalification']);
        //    ?>
        <?php ActiveForm::end(); ?>
    </div>

    <div class="unsuccessful">
        <? $form = ActiveForm::begin(); ?>
        <a role="button" class="btn btn-warning col-md-2 uploadfile"
           href="javascript:void(0)"><?= Yii::t('app', 'add protocol') ?></a>

        <input type="hidden" value="unsuccessful" name="type">
        <div class="cause">

            <?= $form->field($tender->awards[$k], 'cause')->checkboxList(
                [
                    'Не вiдповiдає квалiфiкацiйним критерiям.' => '(Учасник не вiдповiдає квалiфiкацiйним (квалiфiкацiйному) критерiям, установленим в тендернiй документацiї)',
                    'Наявнi пiдстави, зазначенi у статтi 17.' => '(Наявнi пiдстави для вiдхилення тендерної пропозицiї, зазначенi у статтi 17 i частинi сьомiй статтi 28 Закону Про публiчнi закупiвлi)',
                    'Не вiдповiдає вимогам тендерної документацiї.' => '(Тендерна пропозицiя не вiдповiдає вимогам тендерної документацiї)',
                ]); ?>
        </div>

        <?= $form->field($tender->awards[$k], 'description')->textarea();
        echo Html::hiddenInput('awardId', $awardId);

        if ($tenders->tender_method == 'open_belowThreshold') {
            echo Html::checkbox('need_award_esign', false, ['class' => 'need_tender_esign', 'label' => Yii::t('app', 'Накласи ЕЦП')]);
        }
        echo Html::submitButton(Yii::t('app', 'Дисквалiфiкувати'), ['class' => 'btn btn-danger btn-submitform_award', 'name' => 'send_prequalification']);
        ?>
        <?php ActiveForm::end(); ?>
    </div>

<?php } else {

    echo Html::button(Yii::t('app', 'Накласти ЕЦП'), [
        'class' => 'sign_btn_award btn btn-warning',
        'awardId' => $awardId,
        'tenderId' => $tenders->id,
        'tid' => $tenders->tender_id,
        'action' => $action,
        'data-loading-text' => '<i class=\'fa fa-spinner fa-spin \'></i>' . Yii::t('app', ' Зачекайте')
    ]);

}
?>
<div id="sign_block_<?= $awardId ?>"></div>