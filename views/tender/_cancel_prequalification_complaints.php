<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

$this->title = \app\models\Companies::findOne(['id' => Yii::$app->user->identity->company_id])->legalName;
?>
<div class="tender-questions">
    <input type="hidden" value="<?= $tenders->id ?>" id="tender_id">
    <?php
    echo $this->render('/site/head', [
        'title' => Html::encode($this->title),
        'descr' => 'Скарги та на квалiфiкацiю'
    ]);
    $template = "{label}\n<div class=\"col-md-6\">{input}</div>{error}";


    $form = ActiveForm::begin([

        'action'=>Yii::$app->urlManager->createAbsoluteUrl(['/seller/tender/complaints/'.$tenders->id]),
        'options' => [
            'class' => 'form-horizontal',
            'enctype' => 'multipart/form-data'
        ],
        'fieldConfig' => [
            'labelOptions' => ['class' => 'col-md-3 control-label'],
        ],
    ]);

    echo $form->field($complaint, 'cancellationReason', ['template' => $template])->textarea();
    echo Html::hiddenInput('cid', Yii::$app->request->get('cid'));
    echo Html::hiddenInput('qid', Yii::$app->request->get('prequalification'));
    echo Html::hiddenInput('status', Yii::$app->request->get('status'));
//    echo $form->field($complaint, 'relatedLot', ['template' => $template])->dropDownList(\app\models\tenderModels\Complaint::getSellerRelatedLot($tender),
//        ['class' => 'form-control']);
    ?>




    <div class="col-md-offset-5 col-md-6">

        <?= Html::submitButton(Yii::t('app', 'cancel'), [
            'class' => 'btn btn-default btn_submit_question',
            'name' => 'cancel_prequalification_complaint_submit'
        ]);

        ActiveForm::end(); ?>

    </div>

    <div class="clearfix margin_b"></div>

</div>

<?php

echo $this->render('view/_nav_block', [
    'tender' => $tender,
    'tenders' => $tenders
]);
?>
<?php $this->registerJsFile(Url::to('@web/js/complaints.js'), ['position' => yii\web\View::POS_END, 'depends' => 'yii\web\JqueryAsset']); ?>
