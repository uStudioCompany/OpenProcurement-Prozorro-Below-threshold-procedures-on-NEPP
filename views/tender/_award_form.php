<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/**
 * @var $form yii\widgets\ActiveForm
 * @var $tender app\models\tenderModels\Tender
 * @var $tendersId int
 * @var $tenders app\models\Tenders
 * @var $type string
 * @var $award array   Award
 */

$template = "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>";

$form = ActiveForm::begin([
    'enableClientValidation' => true,
    'enableAjaxValidation' => true,
    'options' => [
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data',
        'onSubmit' => 'return submitAward(this);',
    ]]);

echo
    Html::hiddenInput('tendersId', $tendersId) .
    Html::hiddenInput('awardId', $award['id']) .
    Html::hiddenInput('type', $type);

if ($type === 'unsuccessful') {
    echo
        '<h3>' . Yii::t('app', 'Unsuccessful Award Title') . '</h3><hr />' .
        //Html::label('Вкажiть причину дисквалiфiкацiї:', 'description') .
        //Html::textarea('description', '', ['class' => 'form-control description']) . '<br />' .
        Html::label('Завантажте протокол тендерного комiтету:', 'file') .
        Html::fileInput('file', null, ['class' => 'form-control file', '_onChange' => 'console.log(this.files);']) . '<br />';

//    echo $form->field($tender->awards[0], 'cause')->checkboxList(
//        [
//            'Не вiдповiдає квалiфiкацiйним критерiям.' => '(Учасник не вiдповiдає квалiфiкацiйним (квалiфiкацiйному) критерiям, установленим в тендернiй документацiї)',
//            'Наявнi пiдстави, зазначенi у статтi 17.' => '(Наявнi пiдстави для вiдхилення тендерної пропозицiї, зазначенi у статтi 17 i частинi сьомiй статтi 28 Закону Про публiчнi закупiвлi)',
//            'Не вiдповiдає вимогам тендерної документацiї.' => '(Тендерна пропозицiя не вiдповiдає вимогам тендерної документацiї)',
//        ]);

    echo Html::checkboxList('Award[cause]', null,
        [
            'Не вiдповiдає квалiфiкацiйним критерiям.' => '(Учасник не вiдповiдає квалiфiкацiйним (квалiфiкацiйному) критерiям, установленим в тендернiй документацiї)',
            'Наявнi пiдстави, зазначенi у статтi 17.' => '(Наявнi пiдстави для вiдхилення тендерної пропозицiї, зазначенi у статтi 17 i частинi сьомiй статтi 28 Закону Про публiчнi закупiвлi)',
            'Не вiдповiдає вимогам тендерної документацiї.' => '(Тендерна пропозицiя не вiдповiдає вимогам тендерної документацiї)',
        ],[
            'id'=>'award-cause'
        ]);

//    echo $form->field($tender->awards[0], 'description')->textarea();
    echo Html::textarea('Award[description]',null,[
        'class'=>'form-control'
    ]);

    echo Html::submitButton(Yii::t('app', 'Unsuccessful Award'), ['class' => 'btn btn-danger btn-center', 'name' => 'drafts']);

} else if ($type === 'active') {
//    var_dump($tender->awards[0]->id);die;
//Yii::$app->VarDumper->dump($tender->awards, 10, true);die;
    echo
        '<h3>' . Yii::t('app', 'Active Award Title') . '</h3><hr />' .
        Html::label('Завантажте протокол тендерного комiтету:', 'file') .
        Html::fileInput('file', null, ['class' => 'form-control file']) . '<br />';

//    $tender->awards[0]->qualified = false;
//    $tender->awards[0]->eligible = false;
    echo $form->field($tender->awards[0], 'qualified')->checkbox();
    echo $form->field($tender->awards[0], 'eligible')->checkbox();

//    echo Html::checkbox('Award[qualified]');
//    echo Html::checkbox('Award[eligible]');


    echo Html::submitButton(Yii::t('app', 'Active Award'), ['class' => 'btn btn-success btn-center', 'name' => 'drafts']);
} else if ($type === 'cancelled') {
    echo
        '<h3>' . Yii::t('app', 'Cancelled Award Title') . '</h3><hr />' .
        //Html::label('Завантажте протокол тендерного комiтету:', 'file') .
        //Html::fileInput('file', null, ['class' => 'form-control file']) . '<br />' .
        Html::submitButton(Yii::t('app', 'Cancelled Award'), ['class' => 'btn btn-success btn-center', 'name' => 'btn_cancel']);
}


ActiveForm::end();


//"token": "b51ec7a0aac14ed69b06c0f33767f970"
//"id": "c759efe03e1847b9bc7e77b3488f4b7a",
