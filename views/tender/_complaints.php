<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use app\models\Complaints;

$this->title = Yii::$app->user->identity ? \app\models\Companies::findOne(['id' => Yii::$app->user->identity->company_id])->legalName : '';
echo $this->render('small_info_block', [
    'tender' => $tender,
    'tenders' => $tenders
]);
$showSendForm = true;
$cancelled = false;
if (in_array($tender->status,['cancelled', 'unsuccessful'])){
    $cancelled = true;
}
$isTenderOwner = \app\models\Companies::checkCompanyIsTenderOwner($tenders->id, $tenders);
?>


<div class="tender-questions wrap-questions m_upload_block_fix">

    <?php
    echo $this->render('/site/head', [
//        'title' => Html::encode($this->title),
        'descr' => 'Скарги на умови'
    ]);
    $template = "{label}\n<div class=\"col-md-6\">{input}</div>{error}";
    ?>

    <?php if (Yii::$app->session->hasFlash('message')) { ?>
        <div class="bs-example">
            <div class="alert alert-success fade in">
                <a href="#" class="close"
                   data-dismiss="alert">&times;</a><?= Yii::$app->session->getFlash('message'); ?>
            </div>
        </div>
    <?php } ?>

    <input type="hidden" id="tender_id" value="<?= $tenders->id ?>">

    <?php
    if (!$showSendForm = \app\models\Complaints::IsShowSendComplaintPendingForm($tenders, $tender)) {
        if (!$isTenderOwner) { ?>

        <div class="bs-example">
            <div class="alert alert-warning fade in">
                <a href="#" class="close"
                   data-dismiss="alert">&times;</a><?= Yii::t('app', 'Прийняття вимог/скарг заблоковано.') ?>
            </div>
        </div>

    <?php } } ?>

    <?php
    if (!$showAnswerForm = \app\models\Complaints::IsShowComplaintAnswerForm($tenders, $tender)) {
        if ($isTenderOwner) { ?>

        <div class="bs-example">
            <div class="alert alert-warning fade in">
                <a href="#" class="close"
                   data-dismiss="alert">&times;</a><?= Yii::t('app', 'Прийняття вiдповiдей заблоковано.') ?>
            </div>
        </div>

    <?php } } ?>


    <?php if ($showSendForm) {
        $bidIn1Stage = true;
        if (in_array($tenders->tender_method, Yii::$app->params['2stage.tender']) && !\app\models\Bids::checkFirstStageOnBidByCompany($tenders)) {
            $bidIn1Stage = false;
            if (!$isTenderOwner) {
                echo '<h2>' . Yii::t('app', 'Ваша компанія не приймала участь у першому етапі') . '</h2>';
            }
        }
        if ($bidIn1Stage && in_array($tenders->tender_method,['open_belowThreshold'])) {
            echo Html::a(Yii::t('app','Create Claim'),['/' . \app\models\Companies::getCompanyBusinesType() . '/tender/complaints-create/','tid'=>$tenders->id,'type'=>'tender','status'=>'claim','target_id'=>''],['class'=>'btn btn-success']);
        }

    } ?>



    <?php foreach (array_reverse($tender->complaints) as $c => $complaint) {

        if(!Complaints::isOwnerComplaintById($complaint->id)){
            if ($complaint->status == 'draft') continue;
        }

        if ($complaint->title == '') continue;

        ?>
        <div class="questions margin_b panel panel-default">


            <h4><?= Html::encode($complaint->title) ?></h4>
            <?php if ($complaint->status == 'pending') {
                echo '<b style="color: red">' . Yii::t('app', 'скарга') . '</b>';
            } elseif ($complaint->status == 'claim') {
                echo '<b style="color: red">' . Yii::t('app', 'вимога') . '</b>';
            } elseif ($complaint->status == 'cancelled') {
                echo '<b style="color: red">' . Yii::t('app', 'Відкликано скаржником') . '</b>';
            }
            ?>

            <div class="row">
                <div class="col-md-3"><?= Yii::t('app', 'Create At') ?></div>
                <div class="col-md-6">
                    <b> <i><?= Html::encode(Yii::t('app', $complaint->date)) ?></i></b>
                </div>
            </div>

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
            <?php }
            } ?>
            <?php echo \app\models\DocumentUploadTask::GetUploadedDoc($tenders->id, 'complaint', 'tender', [], $tenders->tender_id . '/complaints/' . $complaint->id); ?>


            <?php /** ------------------------------------------------------------------------------------------- */  ?>
            <?php
            if ( Complaints::isOwner($tenders,$complaint->id) && Complaints::isCanAddDocuments($tenders->tender_method,$complaint->status) && !$cancelled) {
                echo '<div class="add_documents"><br><h4>'. Yii::t('app','довантаження документів до скарги/вимоги').'</h4>';
                $form = ActiveForm::begin([
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
                echo Html::hiddenInput('type', 'tender');
                echo Html::hiddenInput('target_id', '');
                ?>

                <div class="contract_file_block form-group">
                    <div class="col-md-3"></div>
                    <div class="col-md-6">
                        <a role="button" class="btn btn-success col-md-3 uploadcontract" href="javascript:void(0)"><?= Yii::t('app', 'add_file') ?></a>
                    </div>
                </div>

                <?= Html::submitButton(Yii::t('app', 'Вiдправити'), [
                    'class' => 'btn btn-default pull-right',
                    'name' => 'add_documents_to_complaints'
                ]); ?>

            <?php ActiveForm::end(); echo '</div>'; } ?>


            <?php if ($complaint->status == 'cancelled') { ?>

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



            <?php if ($complaint->resolution == '' && $showAnswerForm && !in_array($complaint->status, ['pending', 'cancelled', 'draft'])) {
                if (\app\models\Companies::getCompanyBusinesType() == 'buyer') {

//                    echo $this->render('_complaints_answer_form.php', [
//                        'tender' => $tender,
//                        'tenders' => $tenders,
//                        'type'=>'tender',
//                        'target_id'=>null,
//                        'complaint'=>$complaint
//                    ]);

                    $form = ActiveForm::begin([
                        'options' => [
                            'class' => 'form-horizontal',
                            'enctype' => 'multipart/form-data'
                        ],
                        'fieldConfig' => [
                            'labelOptions' => ['class' => 'col-md-3 control-label'],
                        ],
                    ]);

                    echo $form->field($complaint, '[' . $c . ']resolution', ['template' => $template])->textarea(
                        [
                            'name' => 'Tender[0][resolution]'
                        ])->label(false);

                    echo $form->field($complaint, '[' . $c . ']id', ['template' => $template])->hiddenInput(
                        [
                            'name' => 'Tender[0][id]'
                        ])->label(false);

                    echo $form->field($complaint, '[' . $c . ']resolutionType')
                        ->radioList([
                            'invalid' => Yii::t('app', 'Не задоволено'),
                            'declined' => Yii::t('app', 'Вiдхилено'),
                            'resolved' => Yii::t('app', 'Задоволено')
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
                }
            } elseif (isset($complaint->resolution) && $complaint->resolution) { ?>

                <div class="answer">
                    <h4>Вiдповiдь:</h4>
                    <h4><?= Html::encode($complaint->resolution) ?></h4>
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
                    <?php } }
                echo \app\models\DocumentUploadTask::GetUploadedDoc($tenders->id, 'complaint_an', ['tender', 'lot'], [], $tenders->tender_id . '/complaints/' . $complaint->id);
                ?>
            </div>


            <? if (\app\models\Companies::getCompanyBusinesType() == 'seller') {
                foreach ($companyComplaints as $k => $companyComplaint) {

                    if ($complaint->id == $companyComplaint->complaint_id) {


                        $form = ActiveForm::begin();
                        if ($complaint->status == 'answered') {
                            if (!in_array($tenders->tender_method, ['open_aboveThresholdEU', 'open_aboveThresholdUA', 'open_aboveThresholdUA.defense'])) {

                                echo Html::submitButton(Yii::t('app', 'Задоволений'), [
                                    'class' => 'btn btn-success',
                                    'name' => 'complaint_resolved'
                                ]);


                                echo Html::submitButton(Yii::t('app', 'Перетворити на скаргу'), [
                                    'class' => 'btn btn-danger',
                                    'name' => 'complaint_convert_to_claim'
                                ]);

                            }

                        }
//                        Yii::$app->VarDumper->dump($complaint->status, 10, true);
                        if (!$cancelled && $complaint->status != 'cancelled' && $complaint->status != 'resolved' && !isset($complaint->resolution)) {
                            echo Html::checkbox(null, false, [
                                'label' => Yii::t('app', 'cancel'),
                                'class' => 'cancel_checkbox'
                            ]);
                            echo '<div class="cancelation_block">';
                            echo $form->field($complaint, 'cancellationReason')->textarea();
                            echo $form->field($complaint, 'status')->hiddenInput()->label(false);
                            echo Html::submitButton(Yii::t('app', 'Скасувати'), [
                                'class' => 'btn btn-danger',
                                'name' => 'complaint_cancelled'
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
    }
    ?>

</div>

<?php

echo $this->render('view/_nav_block', [
    'tender' => $tender,
    'tenders' => $tenders
]);
?>
<?php $this->registerJsFile(Url::to('@web/js/complaints.js'), ['position' => yii\web\View::POS_END, 'depends' => 'yii\web\JqueryAsset']); ?>
