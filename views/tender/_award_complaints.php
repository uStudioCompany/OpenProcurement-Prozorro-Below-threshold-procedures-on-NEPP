<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use app\models\Complaints;

$template = "{label}\n<div class=\"col-md-6\">{input}</div>{error}";
?>

<?php
//                Yii::$app->VarDumper->dump($awardComplaints, 10, true);die;
//                var_dump($awardComplaints);die;
if (isset($awardComplaints) && is_array($awardComplaints)) {
    foreach ($awardComplaints as $c => $complaint) {
        //if ($complaint->status == 'draft') continue;
//        if ($complaint->status == 'stopping') continue;
        if(!Complaints::isOwnerComplaintById($complaint->id)){
            if ($complaint->status == 'draft') continue;
        }


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
                <div class="col-md-3"><?= Html::encode(Yii::t('app','Учасник'))?></div>
                <div class="col-md-6">
                    <b><?= Html::encode($complaint->author->name) ?></b>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3"><?= Html::encode(Yii::t('app','Контактна особа'))?></div>
                <div class="col-md-6">
                    <b><?= Html::encode($complaint->author->contactPoint->name) ?></b>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3"><?= Html::encode(Yii::t('app','Phone')) ?></div>
                <div class="col-md-6">
                    <b><?= Html::encode($complaint->author->contactPoint->telephone) ?></b>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3"><?= Html::encode(Yii::t('app','Суть звернення')) ?></div>
                <div class="col-md-6">
                    <b> <i><?= Html::encode($complaint->description) ?></i></b>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3"><?= Html::encode(Yii::t('app','Статус')) ?></div>
                <div class="col-md-6">
                    <b> <i><?= Html::encode(Yii::t('app', 'award_'.$complaint->status)) ?></i></b>
                </div>
            </div>
            <?php if (isset($complaint->satisfied)) { ?>
                <div class="row">
                    <div class="col-md-3"><?= Html::encode(Yii::t('app','satisfied')) ?></div>
                    <div class="col-md-6">
                        <b>
                            <i><?= Html::encode($complaint->satisfied ? Yii::t('app', 'Так') : Yii::t('app', 'Ні')) ?></i></b>
                    </div>
                </div>
            <?php } ?>
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

            <?php if (isset($complaintDocument) && count($complaintDocument)) { foreach ($complaintDocument as $d => $document) {
                if ($d === 'iClass') continue;
                if ($document->title == '') continue;

                ?>

                <div class="row">
                    <div class="col-md-3"><?= Html::encode(Yii::t('app','Доданий файл')) ?></div>
                    <div class="col-md-6">
                        <b> <a href="<?= Html::encode($document->url) ?>"><?= Html::encode($document->title) ?></a></b>
                    </div>
                </div>


            <?php } }
            echo \app\models\DocumentUploadTask::GetUploadedDoc($tenders->id, 'aw_complaint', ['tender', 'lot'], [], $tenders->tender_id .'/awards/'. $awardId .'/complaints/'. $complaint->id);
        ?>


        <?php /** ------------------------------------------------------------------------------------------- ДОВАНТАЖЕННЯ ДОКОВ */  ?>
        <?php
        if ( Complaints::isOwner($tenders,$complaint->id) && Complaints::isCanAddDocuments($tenders->tender_method,$complaint->status) ) {
            echo '<div class="add_documents"><br><h4>'. Yii::t('app','довантаження документів до скарги/вимоги').'</h4>';
            $form = ActiveForm::begin([
                'action'=>Yii::$app->urlManager->createAbsoluteUrl(['/'. \app\models\Companies::getCompanyBusinesType() .'/tender/complaints/' . $tenders->id]),
                'options' => [
                    'class' => 'form-horizontal add_documents_to_complaints',
                    'enctype' => 'multipart/form-data'
                ],
                'fieldConfig' => [
                    'labelOptions' => ['class' => 'col-md-3 control-label'],
                ],
            ]);
            echo Html::hiddenInput('tid', $tenders->id);
            echo Html::hiddenInput('cid', $complaint->id);
            echo Html::hiddenInput('type', 'award');
            echo Html::hiddenInput('target_id', $awardId);
            ?>

            <div class="contract_file_block form-group">
                <div class="col-md-3"></div>
                <div class="col-md-6">
                    <a role="button" class="btn btn-success col-md-3 uploadcontract" href="javascript:void(0)"><?= Yii::t('app', 'add_file') ?></a>
                </div>
            </div>

            <?= Html::submitButton(Yii::t('app', 'Додати документи'), [
                'class' => 'btn btn-default pull-right',
                'name' => 'add_documents_to_complaints'
            ]); ?>

            <?php ActiveForm::end(); echo '</div>'; } ?>


            <?
            if (in_array($complaint->status,['cancelled', 'stopping'])) { ?>

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
            <?php }

            //                    Yii::$app->VarDumper->dump($complaint, 10, true);
            if (isset($complaint->tendererAction) && $complaint->tendererAction) { ?>
                <div class="answer">
                    <h4>Вiдповiдь:</h4>
                    <h4><?= Html::encode($complaint->tendererAction) ?></h4>
                </div>
            <?php } ?>

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
                            <br>
                    <?php }
                    }
                    echo \app\models\DocumentUploadTask::GetUploadedDoc($tenders->id, 'complaint_an', ['tender', 'lot'], [], $tenders->tender_id .'/awards/'. $awardId .'/complaints/'. $complaint->id);
                    ?>
                </div>

            <?
            if (\app\models\Companies::getCompanyBusinesType() == 'seller') {

                foreach ($companyComplaints as $k => $companyComplaint) {
                    if ($complaint->id == $companyComplaint->complaint_id && (!in_array($tenders->status, ['complete, cancelled, unsuccessful']))) {

                        $form = ActiveForm::begin();
                        if (isset($complaint->tendererAction) && $complaint->tendererAction) {
                            if (Complaints::isOwnerComplaintById($complaint->id) && $complaint->status == 'answered' && !isset($complaint->satisfied)) {
                                // по схеме claim в этих процедурах может переходить только в answered/cancelled
                                if (!in_array($tenders->tender_method, ['open_aboveThresholdEU', 'open_aboveThresholdUA', 'open_aboveThresholdUA.defense', 'open_competitiveDialogueUA', 'open_competitiveDialogueEU', 'selective_competitiveDialogueEU.stage2', 'selective_competitiveDialogueUA.stage2'])) {
                                    echo Html::submitButton(Yii::t('app', 'Задоволений'), [
                                        'class' => 'btn btn-success',
                                        'name' => 'award_claim_resolved'
                                    ]);
                                    echo Html::submitButton(Yii::t('app', 'Перетворити на скаргу'), [
                                        'class' => 'btn btn-danger',
                                        'name' => 'award_claim_convert_to_pending'
                                    ]);

                                }
                            }
                        }

                        if (!in_array($complaint->status, ['stopping', 'resolved', 'cancelled']) && !isset($complaint->dateAnswered) && Complaints::isOwnerComplaintById($complaint->id)) {
                            echo Html::checkbox(null, false, [
                                'label' => Yii::t('app', 'cancel'),
                                'class' => 'cancel_checkbox'
                            ]);
                            echo '<div class="cancelation_block">';
                            echo $form->field($complaint, 'cancellationReason')->textarea();
                            echo $form->field($complaint, 'status')->hiddenInput()->label(false);
//                            echo Html::hiddenInput('AwardId', $awardId);
                            echo Html::submitButton(Yii::t('app', 'Скасувати'), [
                                'class' => 'btn btn-danger',
                                'name' => 'complaint_cancelled'
                            ]);
                            echo '</div>';
                        }


                        echo $form->field($complaint, 'id', ['template' => $template])->hiddenInput()->label(false);
                        echo Html::hiddenInput('token', $companyComplaint->token);
                        echo Html::hiddenInput('AwardId', $awardId);
                        ActiveForm::end();
                        break;
                    }


                }
            } elseif (
                \app\models\Companies::getCompanyBusinesType() == 'buyer' &&
                \app\models\Companies::checkCompanyIsTenderOwner($tendersId) &&
                !isset($complaint->tendererAction) &&
                in_array($complaint->status, ['claim'])
            ) {
                echo '<h4>'. Yii::t('app', 'Надати вiдповiдь') .':</h4>';

                $form = ActiveForm::begin();

                echo $form->field($complaint, '[' . $c . ']tendererAction')->textarea(
                    [
                        'name' => 'tendererAction',
                        'class' => 'form-control answer_text'
                    ]
                )->label(false);


                echo Html::hiddenInput('complaintId', $complaint->id);
                echo Html::hiddenInput('awardId', $awardId);
                echo Html::hiddenInput('complaintGurrentStatus', $complaint->status);
//                Yii::$app->VarDumper->dump($complaint->status, 10, true, true);
                ?>


                <?php if ($complaint->status == 'claim') { /* ($complaint->status == 'claim') { ----------!!No!!! Ответить можно на pending тоже !!No!!! */ ?>

                    <div class="contract_file_block form-group">
                        <!--                    <div class="col-md-3"></div>-->
                        <div class="col-md-6">
                            <a role="button" class="btn btn-success col-md-3 uploadcontract"
                               href="javascript:void(0)"><?= Yii::t('app', 'add_file') ?></a>
                        </div>
                    </div>
                    <?= $form->field($complaint, '[' . $c . ']resolutionType')
                        ->radioList([
                            'invalid' => Yii::t('app', 'Не задоволено'),
                            'declined' => Yii::t('app', 'Вiдхилено'),
                            'resolved' => Yii::t('app', 'Задоволено')
                        ],
                            [
                                'name' => 'Tender[0][resolutionType]'
                            ])->label(false);
                    ?>
                    <div class="clearfix"></div>

                <?php } ?>


                <?php echo Html::submitButton(Yii::t('app', 'Надати вiдповiдь'), [
                    'class' => 'btn btn-default btn_submit_award_answer_complaint',
                    't_id' => $tendersId,
                    'data-loading-text' => '<i class=\'fa fa-spinner fa-spin \'></i>' . Yii::t('app', 'Зачекайте'),
                    'name' => 'answer_complaint_submit'
                ]);

                ActiveForm::end();
            }
            ?>


        </div>

        <!--                        <tr>-->
        <!--                            <td style="padding: 40px">-->
        <!--                                <h4>--><?//= Html::encode($complaint->title) ?><!--</h4>-->
        <!--                                --><?//= Html::encode($complaint->description); ?>
        <!--                                <h4>Статус - --><?//= Yii::t('app', Html::encode($complaint->status)) ?><!--</h4>-->
        <!---->
        <!---->
        <!--                                --><?php
//                                if (isset($complaint->documents)) {
//                                    foreach ($complaint->documents as $d => $document) {
//                                        if ($document->title != '') {
//                                            ?>
        <!--                                            <br/>-->
        <!--                                            <a href="--><?//= $document->url ?><!--">--><?//= htmlspecialchars($document->title) ?><!--</a>-->
        <!--                                            <a href="--><?//= $document->url ?><!--"><span-->
        <!--                                                    class="glyphicon glyphicon-download-alt"-->
        <!--                                                    aria-hidden="true"></span></a>-->
        <!--                                            <br/>-->
        <!--                                            --><?php
//                                        }
//                                    }
//                                } ?>
        <!---->
        <!---->
        <!--                                --><?php //if (($complaint->status == 'claim' || $complaint->status == 'pending')) {
//
////                                    Yii::$app->VarDumper->dump($complaint, 10, true);
//                                    if (isset($complaint->tendererAction) && $complaint->tendererAction) {
//                                        echo '<h4>Вiдповiдь - ' . Yii::t('app', Html::encode($complaint->tendererAction)) . '</h4>';
//                                    } elseif(\app\models\Companies::getCompanyBusinesType() == 'buyer') {
//                                        $form = ActiveForm::begin();
//
//                                        echo $form->field($complaint, '[' . $c . ']tendererAction')->textarea(
//                                            [
//                                                'name' => 'tendererAction',
//                                                'class' => 'form-control answer_text'
//                                            ]
//                                        )->label(false);
//
//
//                                        echo Html::hiddenInput('complaintId', $complaint->id);
//                                        echo Html::hiddenInput('awardId', $awardId);
//                                        echo Html::hiddenInput('type', 'cancelled');
//
//                                        echo Html::submitButton(Yii::t('app', 'Надати вiдповiдь'), [
//                                            'class' => 'btn btn-default btn_submit_award_answer_complaint',
//                                            't_id' => $tendersId,
//                                            'data-loading-text' => '<i class=\'fa fa-spinner fa-spin \'></i>' . Yii::t('app', 'Зачекайте')
//                                        ]);
//
//                                        ActiveForm::end();
//                                    }
//
//
//                                }
//
//
//                                //                                if(in_array($complaint->id, \app\models\Complaints::getCompanyAwardComplains($tenders->id))){
//                                //
//                                //                                }
//                                ?>
        <!--                            </td>-->
        <!--                        </tr>-->
        <?php
    }
}

$this->registerJs(
    "$(document).ready(function(){
        $('[data-toggle=\"popover\"]').popover();
    });"
    , yii\web\View::POS_END);
?>

