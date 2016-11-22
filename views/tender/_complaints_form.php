<?php

use \yii\bootstrap\Html;

/**
 * @var int $tid
 * @var string $status claim(Вимога)|pending(Жалоба)
 * @var string $type tender|prequalification|award
 * @var string $target_id - [prequalification|award] ID
 */

if (!isset($status)) $status = 'claim';
if (!isset($type)) $type     = 'tender';

$this->title = Yii::$app->user->identity ? \app\models\Companies::findOne(['id' => Yii::$app->user->identity->company_id])->legalName : '';

$model = new \app\models\tenderModels\Complaint();

$template = "{label}\n<div class=\"col-md-6\">{input}</div>{error}";

?>
<div class="tender-questions wrap-questions m_upload_block_fix">
    <input type="hidden" id="tender_id" value="<?= $tenders->id ?>">
<?

$form = \yii\widgets\ActiveForm::begin([
    'action' => Yii::$app->urlManager->createAbsoluteUrl(['/seller/tender/complaints/' . $tenders->id]),
    'options' => [
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data'
    ],
    'fieldConfig' => [
        'labelOptions' => ['class' => 'col-md-3 control-label'],
    ],
]);

echo Html::hiddenInput($model->formName().'[status]',$status);
echo Html::hiddenInput('complaint_type',$type.'_complaint_submit');
echo Html::hiddenInput('target_id', $target_id);
echo Html::hiddenInput('tid', $tid);

echo $form->field($model, 'title', ['template' => $template])->textInput();
echo $form->field($model, 'description', ['template' => $template])->textarea();
if ($type == 'tender') {
    echo $form->field($model, 'relatedLot', ['template' => $template])->dropDownList(\app\models\tenderModels\Complaint::getSellerRelatedLot($tender), ['class' => 'form-control']);
}
?>

<div class="contract_file_block form-group">
    <div class="col-md-3"></div>
    <div class="col-md-6">
        <a role="button" class="btn btn-success col-md-3 uploadcontract" href="javascript:void(0)"><?= Yii::t('app', 'add_file') ?></a>
    </div>
</div>


<div class="col-md-offset-3 col-md-6">

    <?= \yii\helpers\Html::submitButton(Yii::t('app', 'Подати '.$status), [
        'class' => 'btn btn-default btn_submit_question',
        'name' => 'complaint_submit'
    ]);?>



</div>

<? \yii\widgets\ActiveForm::end(); ?>

<div class="clearfix margin_b"></div>

</div>

<?php $this->registerJsFile(\yii\helpers\Url::to('@web/js/complaints.js'), ['position' => yii\web\View::POS_END, 'depends' => 'yii\web\JqueryAsset']); ?>