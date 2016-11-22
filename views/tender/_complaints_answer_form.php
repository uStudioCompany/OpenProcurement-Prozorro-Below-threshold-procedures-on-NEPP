<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var int $tid
 * @var string $status claim(Вимога)|pending(Жалоба)
 * @var string $type tender|prequalification|award
 * @var string $target_id - [prequalification|award] ID
 * @var \app\models\tenderModels\Complaint $complaint
 */

//echo '<pre>'; print_r($complaint); DIE();

$template = "{label}\n<div class=\"col-md-6\">{input}</div>{error}";


$form = ActiveForm::begin([
    'action'=>Yii::$app->urlManager->createAbsoluteUrl(['/buyer/tender/complaints/' . $tenders->id]),
    'options' => [
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data'
    ],
    'fieldConfig' => [
        'labelOptions' => ['class' => 'col-md-3 control-label'],
    ],
]);

echo Html::hiddenInput('complaint_type',$type.'_complaint_submit');

echo Html::hiddenInput('target_id', $target_id);

echo $form->field($complaint, '[' . $c . ']resolution', ['template' => $template])->textarea(
    [
        'name' => 'Tender[0][resolution]'
    ])->label(false);

echo $form->field($complaint, '[' . $c . ']id', ['template' => $template])->hiddenInput(
    [
        'name' => 'Tender[0][complaint_id]'
    ])->label(false);

echo $form->field($complaint, '[' . $c . ']resolutionType')
    ->radioList([
        'invalid' => 'Не задоволено',
        'declined' => 'Вiдхилено',
        'resolved' => 'Задоволено'
    ],
        [
            'name' => 'Tender[0][resolutionType]'
        ])->label(false);
?>


    <div class="contract_file_block form-group">
        <!--                            <div class="col-md-3"></div>-->
        <div class="col-md-6">
            <a role="button" class="btn btn-success col-md-3 uploadcontract"
               href="javascript:void(0)"><?= Yii::t('app', 'add_file') ?></a>
        </div>
    </div>

<?= Html::submitButton(Yii::t('app', 'Надати вiдповiдь'), [
    'class' => 'btn btn-default btn_submit_complaint',
    'name' => 'answer_complaint_submit'
]);

ActiveForm::end();

