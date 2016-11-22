<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use app\models\Complaints;

/**
 * @var int $qualifications_id: tender->qualifications->id
 * @var \app\models\tenderModels\Complaint $complaint
 */

$this->title = \app\models\Companies::findOne(['id' => Yii::$app->user->identity->company_id])->legalName;
echo $this->render('small_info_block', [
    'tender' => $tender,
    'tenders' => $tenders
]);

$showSendForm = (strtotime(str_replace('/', '.', $tender->qualificationPeriod->endDate)) > strtotime('now'));
//$showAnswerForm = \app\models\Complaints::IsShowComplaintAnswerForm($tenders, $tender);
$showAnswerForm = true; // (strtotime(str_replace('/', '.', $tender->qualificationPeriod->endDate)) > strtotime('now'));
if (Yii::$app->session->hasFlash('message')) { ?>
    <div class="alert alert-success fade in">
        <a href="#" class="close" data-dismiss="alert">&times;</a>
        <?= Yii::$app->session->getFlash('message'); ?>
    </div>
<?php } ?>

<div class="bs-example">
    <div class="alert alert-warning fade in"><a href="#" class="close" data-dismiss="alert">&times;</a>
        <?= Yii::t('app', 'Період подачі вимог/скарг') . ': ' . $tender->qualificationPeriod->startDate . ' - ' . $tender->qualificationPeriod->endDate ?>
    </div>
</div>


<div class="tender-questions wrap-questions">
    <input type="hidden" value="<?= $tenders->id ?>" id="tender_id">
    <?php
    echo $this->render('/site/head', [
        'title' => Html::encode($this->title),
        'descr' => 'Скарги та на квалiфiкацiю'
    ]);
    $template = "{label}\n<div class=\"col-md-6\">{input}</div>{error}";
    ?>


    <?php if ($showSendForm && !Complaints::isOwner($tenders,$complaint->id)) {
        $bidExist = true;
        if (in_array($tenders->tender_method, array_merge(Yii::$app->params['2stage.tender'], ['open_competitiveDialogueUA','open_competitiveDialogueEU'])) && !\app\models\Bids::AccessMembersOfAuction($tenders)) {
            $bidExist = false;
        }
        if ($bidExist) {
            ?>
            <?= Html::a('Create Claim', ['/seller/tender/complaints-create/', 'tid' => $tenders->id, 'type' => 'prequalification', 'status' => 'claim', 'target_id' => $qualifications_id], ['class' => 'btn btn-success']) ?>
            <?= Html::a('Create Pending', ['/seller/tender/complaints-create/', 'tid' => $tenders->id, 'type' => 'prequalification', 'status' => 'pending', 'target_id' => $qualifications_id], ['class' => 'btn btn-success']) ?>
        <?php }
    }?>


    <?php
    foreach ($tender->qualifications AS $qualifications) {
        if ($qualifications->id !== $qualifications_id) { continue; }

    foreach (array_reverse($qualifications->complaints) as $c => $complaint) {

        if(!Complaints::isOwnerComplaintById($complaint->id)){
            if ($complaint->status == 'draft') continue;
        }

        if ($complaint->title == '') continue;

        ?>
        <div class="questions margin_b panel panel-default">


            <h4><?= Html::encode($complaint->title) ?></h4>
            <?php if ($complaint->status == 'pending') {
                echo '<b style="color: red">' . Yii::t('app', 'скарга') . '</b>';
            } elseif (in_array($complaint->status, ['cancelled', 'stopping'])) {
                echo '<b style="color: red">' . Yii::t('app', 'Відкликано скаржником') . '</b>';
            }
            ?>

            <div class="row">
                <div class="col-md-3"><?= Yii::t('app', 'Организация') ?></div>
                <div class="col-md-6">
                    <b><?= Html::encode($complaint->author->name) ?></b>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3"><?= Yii::t('app', 'Контактна особа') ?></div>
                <div class="col-md-6">
                    <b><?= Html::encode($complaint->author->contactPoint->name) ?></b>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3"><?= Yii::t('app', 'Phone') ?></div>
                <div class="col-md-6">
                    <b><?= Html::encode($complaint->author->contactPoint->telephone) ?></b>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3"><?= Yii::t('app', 'Суть звернення') ?></div>
                <div class="col-md-6">
                    <b> <i><?= Html::encode($complaint->description) ?></i></b>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3"><?= Yii::t('app', 'status') ?></div>
                <div class="col-md-6">
                    <b> <i><?= Html::encode(Yii::t('app', $complaint->status)) ?></i></b>
                </div>
            </div>
            <?php if (isset($complaint->satisfied)) { ?>
                <div class="row">
                    <div class="col-md-3"><?= Yii::t('app', 'satisfied') ?></div>
                    <div class="col-md-6">
                        <b>
                            <i><?= Html::encode($complaint->satisfied ? Yii::t('app', 'Так') : Yii::t('app', 'Ні')) ?></i></b>
                    </div>
                </div>
            <?php } ?>
            <div class="row">
                <div class="col-md-3"><?= Yii::t('app', 'resolutionType') ?></div>
                <div class="col-md-6">
                    <b> <i><?= Html::encode(Yii::t('app', $complaint->resolutionType)) ?></i></b>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3"><?= Yii::t('app', 'Замечание к') ?></div>
                <div class="col-md-6">
                    <b>
                        <i>
                            <?php
                            if (isset($complaint->relatedLot) && $complaint->relatedLot != NULL) {
                                foreach ($tender['lots'] as $l => $lot) {
                                    if ($l === 'iClass') continue;
                                    if ($l === '__EMPTY_LOT__') continue;
                                    if ($complaint->relatedLot == $lot['id']) {
                                        echo ' ' . Yii::t('app', 'Лоту') . ' ' . $lot['title'];
                                    }
                                }
                            } else {
                                echo ' ' . Yii::t('app', 'Тендеру') . ' ';
                            }
                            ?>
                        </i>
                    </b>
                </div>
            </div>

            <?php //echo \app\models\DocumentUploadTask::GetUploadedDoc($tenders->id, 'complaint', 'tender'); ?>
            <?php
            $tenderDocuments = [];
            $complaintDocument = [];
            foreach ($complaint->documents as $d => $document) {
                if ($document->author == 'tender_owner') {
                    $tenderDocuments[$d] = $document;
                }
                if ($document->author == 'complaint_owner') {
                    $complaintDocument[$d] = $document;
                }
            }
            ?>
            <?php
            if (isset($complaintDocument) && count($complaintDocument)) {
                foreach ($complaintDocument as $d => $document) {
                    if ($d === 'iClass') continue;
                    if ($document->title == '') continue;
                    ?>

                    <div class="row">
                        <div class="col-md-3"><?= Yii::t('app', 'Доданий файл') ?></div>
                        <div class="col-md-6">
                            <b> <a href="<?= Html::encode($document->url) ?>"><?= Html::encode($document->title) ?></a></b>
                            <b></b>
                        </div>
                    </div>
                <?php } } ?>
            <?php echo \app\models\DocumentUploadTask::GetUploadedDoc($tenders->id, 'complaint', ['tender', 'lot'], [], $tenders->tender_id . '/complaints/' . $complaint->id); ?>

            <? //echo '<pre>'; print_r($complaint); DIE(); ?>




            <?php if (in_array($complaint->status, ['cancelled', 'stopping'])) { ?>

                <div class="row">
                    <div class="col-md-3"><?= Yii::t('app', 'Дата скасування') ?></div>
                    <div class="col-md-6">
                        <b><?= Html::encode(Yii::$app->formatter->asDatetime($complaint->dateCanceled)) ?></b>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-3"><?= Yii::t('app', 'Причина скасування') ?></div>
                    <div class="col-md-6">
                        <b><?= Html::encode($complaint->cancellationReason) ?></b>
                    </div>
                </div>
            <?php } ?>



            <?php if ($complaint->resolution == '' && $showAnswerForm && !in_array($complaint->status, ['pending', 'cancelled', 'draft', 'stopping'])) {
                if (\app\models\Companies::getCompanyBusinesType() == 'buyer') {

                    echo $this->render('_complaints_answer_form.php', [
                        'tender' => $tender,
                        'tenders' => $tenders,
                        'type'=>'prequalification',
                        'target_id'=>$qualifications_id,
                        'complaint'=>$complaint
                    ]);

//                    $form = ActiveForm::begin([
//                        'action'=>Yii::$app->urlManager->createAbsoluteUrl(['/buyer/tender/complaints/' . $tenders->id]),
//                        'options' => [
//                            'class' => 'form-horizontal',
//                            'enctype' => 'multipart/form-data'
//                        ],
//                        'fieldConfig' => [
//                            'labelOptions' => ['class' => 'col-md-3 control-label'],
//                        ],
//                    ]);
//
//                    $type = 'prequalification';
//                    $target_id = $qualifications_id; // $complaint->id;
//
//                    echo Html::hiddenInput('complaint_type',$type.'_complaint_submit');
//                    echo Html::hiddenInput('target_id', $target_id);
//
//                    echo $form->field($complaint, '[' . $c . ']resolution', ['template' => $template])->textarea(
//                        [
//                            'name' => 'Tender[0][resolution]'
//                        ])->label(false);
//
//                    echo $form->field($complaint, '[' . $c . ']id', ['template' => $template])->hiddenInput(
//                        [
//                            'name' => 'Tender[0][complaint_id]'
//                        ])->label(false);
//
//                    echo $form->field($complaint, '[' . $c . ']resolutionType')
//                        ->radioList([
//                            'invalid' => 'Не задоволено',
//                            'declined' => 'Вiдхилено',
//                            'resolved' => 'Задоволено'
//                        ],
//                            [
//                                'name' => 'Tender[0][resolutionType]'
//                            ])->label(false);
//                    ?>
<!---->
<!---->
<!--                    <div class="contract_file_block form-group">-->
<!--                        <!--                            <div class="col-md-3"></div>-->
<!--                        <div class="col-md-6">-->
<!--                            <a role="button" class="btn btn-success col-md-3 uploadcontract"-->
<!--                               href="javascript:void(0)">--><?//= Yii::t('app', 'add_file') ?><!--</a>-->
<!--                        </div>-->
<!--                    </div>-->
<!---->
<!--                    --><?//= Html::submitButton(Yii::t('app', 'Надати вiдповiдь'), [
//                        'class' => 'btn btn-default btn_submit_complaint',
//                        'name' => 'answer_complaint_submit'
//                    ]);
//
//                    ActiveForm::end();
                }
            } elseif (isset($complaint->resolution) && $complaint->resolution) { ?>

                <div class="answer">
                    <h4><?= Html::encode(Yii::t('app', 'Відповідь')) ?>:</h4>
                    <h4><?= Html::encode($complaint->resolution) ?></h4>
                </div>

            <?php } else { /*echo '------------ cannot answer -----------'. $complaint->status .'-'; var_dump($showAnswerForm);*/ } ?>

            <div class="answer_docs">
                <?php
                if (isset($tenderDocuments) && count($tenderDocuments)) {
                    ?><h4><?= Yii::t('app', 'Документи вiдповiдi:') ?></h4><?
                    foreach ($tenderDocuments as $d => $document) {
                        if ($d === 'iClass') continue;
                        if ($document->title == '') continue;
                        ?>

                        <div class="row">
                            <div class="col-md-3"><?= Yii::t('app', 'Доданий файл') ?></div>
                            <div class="col-md-6">
                                <b>
                                    <a href="<?= Html::encode($document->url) ?>"><?= Html::encode($document->title) ?></a></b>
                                <b></b>
                            </div>
                        </div>
                    <?php } }
                echo \app\models\DocumentUploadTask::GetUploadedDoc($tenders->id, 'complaint_an', ['tender', 'lot'], [], $tenders->tender_id . '/complaints/' . $complaint->id);
                ?>
            </div>


            <? if (\app\models\Companies::getCompanyBusinesType() == 'seller') {
                foreach ($companyComplaints as $k => $companyComplaint) {

                    if ($complaint->id == $companyComplaint->complaint_id) {

                        $form = ActiveForm::begin();
                        if ($complaint->status == 'answered' && !isset($complaint->satisfied)) {
                            echo Html::hiddenInput('qualification_id', $qualifications_id);
//                            if (in_array($tenders->tender_method, ['open_aboveThresholdEU'])) {
//                                echo Html::submitButton(Yii::t('app', 'Задоволений'), [
//                                    'class' => 'btn btn-success',
//                                    'name' => 'prequalification_satisfied_true'
//                                ]);
//                                echo Html::submitButton(Yii::t('app', 'Не згоден'), [
//                                    'class' => 'btn btn-danger',
//                                    'name' => 'prequalification_satisfied_false',
//                                    'data-toggle' => 'popover',
//                                    'data-trigger' => 'hover',
//                                    'data-content' => Yii::t('app', 'Ви маєте можливість створити іншу скаргу/вимогу.'),
//                                ]);
//                                //а тут все другие процедуры или зачем они нужны если преквалификация только в европейской бывает
//                            }

//                            echo Html::submitButton(Yii::t('app', 'Задоволений'), [
//                                'class' => 'btn btn-success',
//                                'name' => 'prequalification_resolved',
//                            ]);
//
//                            if (in_array($tenders->tender_method, ['open_aboveThresholdEU', 'open_aboveThresholdUA','open_aboveThresholdUA.defense'])) {
//                                echo Html::submitButton(Yii::t('app', 'Не згоден'), [
//                                    'class' => 'btn btn-danger',
//                                    'name' => 'prequalification_resolved'
//                                ]);
//                            } else {
//                                echo Html::submitButton(Yii::t('app', 'Перетворити на скаргу'), [
//                                    'class' => 'btn btn-danger',
//                                    'name' => 'complaint_convert_to_claim'
//                                ]);
//                            }

                        }
//                        Yii::$app->VarDumper->dump($complaint->status, 10, true);
                        if (!in_array($complaint->status,['cancelled', 'resolved', 'stopping']) && !isset($complaint->resolution) && Complaints::isOwnerComplaintById($complaint->id)) {
                            echo Html::checkbox(null, false, [
                                'label' => Yii::t('app', 'cancel'),
                                'class' => 'cancel_checkbox'
                            ]);
                            echo '<div class="cancelation_block">';
                            echo Html::hiddenInput('qualification_id', $qualifications_id);
                            echo $form->field($complaint, 'cancellationReason')->textarea();
                            echo $form->field($complaint, 'status')->hiddenInput()->label(false);
                            echo Html::submitButton(Yii::t('app', 'Скасувати'), [
                                'class' => 'btn btn-danger',
                                'name' => 'prequalification_complaint_cancelled'
                            ]);
                            echo '</div>';
                        }


                        echo $form->field($complaint, 'id', ['template' => $template])->hiddenInput()->label(false);
                        echo Html::hiddenInput('token', $companyComplaint->token);

                        ActiveForm::end();
                        break;
                    }
                }
            }
            ?>

        </div>

        <?php
    } }
    ?>



</div>

<?php

echo $this->render('view/_nav_block', [
    'tender' => $tender,
    'tenders' => $tenders
]);
?>
<?php
$this->registerJsFile(Url::to('@web/js/complaints.js'), ['position' => yii\web\View::POS_END, 'depends' => 'yii\web\JqueryAsset']);
$this->registerJs(
    "$(document).ready(function(){
        $('[data-toggle=\"popover\"]').popover();
    });"
    , yii\web\View::POS_END);
?>
