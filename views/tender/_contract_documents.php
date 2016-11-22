<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use app\models\Companies;


?>
<tr class="lotcontract">
    <td colspan="4">
        <div style="text-align: center">
            <button class="btn btn-primary tender_contract_btn"><?= Yii::t('app', 'Контрактна документацiя') ?></button>
            <?php
            if (strtotime(str_replace('/', '-', $awardCompaintsPeriod->endDate)) < strtotime('now')) { ?>
                <p><?= Yii::t('app', 'Перiод подачи скарг вже скiнчiвся.'); ?></p>
            <?php } else { ?>
                <p><?= Yii::t('app', 'Перiод подачи скарг закiнчується: ') . $awardCompaintsPeriod->endDate; ?></p>
            <?php } ?>
        </div>

        <div class="tender_contract_block">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>

            <?php
            //Yii::$app->VarDumper->dump($tender->contracts, 10, true);die;

            if (Companies::getCompanyBusinesType() == 'buyer' && Companies::checkCompanyIsTenderOwner($tendersId)){ //если это покупатель и хозяин тендера

            foreach ($tender->contracts as $c => $contract) {
            //            var_dump($contract);die;
            if (($contract->status == 'pending') && $contract->awardID == $awardId) {


            //проверяем была ли нажата активация
            $needActivation = true;
            if ($userAction = \app\models\Tenders::findOne($tendersId)->user_action) {
                $userAction = \yii\helpers\Json::decode($userAction);
                if (isset($userAction['Contracts'])) {
                    foreach ($userAction['Contracts'] as $i => $item) {
                        if ($i == $contract->id) {
                            $needActivation = false;
                            break;
                        }
                    }
                }

            };


            //                    Pjax::begin([
            //                        'id' => 'pjax_tender_contract' . $c,
            //                    ]);

            $form = ActiveForm::begin([
                'id' => 'contract_form' . $c,
                'options' => [
                    'data-pjax' => true,
                    'class' => 'contract_form',
                ]
            ]);


            echo Html::input('hidden', 't_id', $tendersId, [
                'id' => 'tender_id'
            ]);
            echo Html::input('hidden', 'contract_id', $contract->id);

            echo '<div class="c_doc_example">';
            echo Html::dropDownList('documents[__CONTRACT_DOC__][documentType]', null, \app\models\DocumentType::getType(null, 'contract'),
                [
                    'class' => 'form-control contract_document_type_select',
                ]);
            ?>
        </div>

        <h2 class="text-center"><?= Yii::t('app', 'Контрактна документацiя') ?></h2>

        <div class="info-block modal_document_block">
            <?php
            if (isset($contract->documents)) {
                //определяем необходимость в отображении кнопок активации документов контракта
                if (count($contract->documents) > 0 || $tenders->tender_method == 'limited_reporting') {
                    $addHidden = '';
                } else {
                    $addHidden = 'hidden';
                }

                //формируем массив из последних версий файлов.
                $realContract = \app\models\tenderModels\Document::getLastVersionDocuments($contract->documents);

                foreach ($realContract as $d => $document) {
                    if ($document->is_old == 1) { ?>

                        <div class="margin_b_20 row contract_file_block panel-body">
                            <div class="col-md-4">
                                <s><?= $document->title ?></s>
                            </div>
                            <div class="col-md-4"></div>
                            <div class="col-md-2"></div>
                            <?php echo '<div class="col-md-2 text-right download_link"><a href="' . $document->url . '" title="' . Yii::t('app', 'contractDocument') . htmlspecialchars($document->title) . '">' . Yii::t('app', 'download') . ' <span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span></a></div>'; ?>
                        </div>


                    <?php } else { ?>

                        <div class="margin_b_20 row contract_file_block panel-body">
                            <div class="col-md-4">
                                <?= Html::textInput('documents[' . $d . '][title]', $document->title, [
                                    'class' => 'form-control file_original_name'
                                ]) ?>
                            </div>
                            <?= Html::hiddenInput('documents[' . $d . '][original_name]', $document->title, [
                                'class' => 'form-control file_original_name'
                            ]) ?>
                            <div class="col-md-4">
                                <?= Html::dropDownList('documents[' . $d . '][documentType]', $document->documentType, \app\models\DocumentType::getType(null, 'contract'),
                                    [
                                        'class' => 'form-control contract_document_type_select_isset',
                                    ]);
                                ?>

                            </div>
                            <a role="button" class="btn btn-warning col-md-2 replace_file"
                               href="javascript:void(0)"><?= Yii::t('app', 'replace') ?></a>
                            <!--                                <div class="replace_file_wrap col-md-1">-->
                            <!--                                    <button type="button" class="btn btn-default replace_file">Замiнити</button>-->
                            <!--                                </div>-->
                            <?php echo '<div class="col-md-2 text-right download_link"><a href="' . $document->url . '" title="' . Yii::t('app', 'contractDocument') . htmlspecialchars($document->title) . '">' . Yii::t('app', 'download') . ' <span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span></a></div>'; ?>
                            <?= Html::hiddenInput('documents[' . $d . '][realName]', null, [
                                'class' => 'real_name'
                            ]) ?>
                            <?= Html::hiddenInput('documents[' . $d . '][id]', $document->id, [
                                'class' => 'document_id'
                            ]) ?>
                        </div>
                        <?php
                    }

                }
            }
            echo \app\models\DocumentUploadTask::GetUploadedDoc($tenders->id, 'aw_contract', 'tender', null,  $tenders->tender_id . '/contracts/' . $contract->id);
            ?>

        </div>

        <?php if (strtotime(str_replace('/', '-', $awardCompaintsPeriod->endDate)) < strtotime('now')) { ?>
        <a role="button" class="btn btn-success col-md-3 uploadcontract"
           href="javascript:void(0)"><?= Yii::t('app', 'add_file') ?></a>

        <div class="clearfix"></div>


        <div class="row contract_buttons_block <?= $addHidden ?>" align="center">
            <?= Html::submitButton(Yii::t('app', 'Завантажити'), [
                'class' => 'btn btn-default btn_submit_award_contract btn-success',
                't_id' => $tendersId,
                'data-loading-text' => '<i class=\'fa fa-spinner fa-spin \'></i>' . Yii::t('app', 'Зачекайте')
            ]); ?>
        </div>

        <div class="row <?= $addHidden ?>" align="center">
            <?// if (strtotime(str_replace('/', '-', $awardCompaintsPeriod->endDate)) < strtotime('now')) { ?>
            <div class="margin_b_20 row panel-body contract_info">
                <div class="col-md-3">
                    <?php
                    //                        Yii::$app->VarDumper->dump($tender->id, 10, true);die;
                    ?>
                    <?= $form->field($contract, '[' . $c . ']contractNumber')->textInput(); ?>


                </div>
                <div class="col-md-3">
                    <?= $form->field($contract, '[' . $c . ']dateSigned')->textInput(
                        [
                            'class' => 'picker_date_signed form-control'
                        ]); ?>

                </div>
                <div class="col-md-3">
                    <?= $form->field($contract->period, '[' . $c . ']startDate')->textInput([
                        'class' => 'picker form-control'
                    ]); ?>

                </div>
                <div class="col-md-3">
                    <?= $form->field($contract->period, '[' . $c . ']endDate')->textInput([
                        'class' => 'picker form-control'
                    ]); ?>

                </div>



                <?= Html::hiddenInput(null, $awardCompaintsPeriod->endDate, ['class' => 'complaintPeriodEnd']) ?>
            </div>


            <?php
            //                var_dump(\app\models\Complaints::getNotClosedComplaintsInAllAwards($tenders, $awardId));
            if ($needActivation) {
                if ($tenders->tender_method == 'open_belowThreshold') {
//                    if (\app\models\Complaints::getNotClosedComplaintsInAllAwards($tenders, $awardId)) {
                        echo Html::submitButton(Yii::t('app', 'Активувати'), [
                            'class' => 'btn btn_submit_award_contract_activate btn-danger',
                            't_id' => $tendersId,
                            'activate' => 'activate',
                            'data-loading-text' => '<i class=\'fa fa-spinner fa-spin \'></i>' . Yii::t('app', ' Зачекайте')
                        ]);
//                    } else {
//                        echo Yii::t('app', 'Активация контракта невозможна, так как у Вас есть не закрытые вопросы');
//                    }
                }
            } else {
                echo Html::button(Yii::t('app', 'Накласти ЕЦП'), ['class' => 'sign_btn_contract btn btn-warning', 'contractId' => $contract->id, 'tenderId' => $tendersId, 'tid' => $tender->id, 'action' => 'activate', 'data-loading-text' => '<i class=\'fa fa-spinner fa-spin \'></i>' . Yii::t('app', ' Зачекайте')]);
            }


            }
            ?>


        </div>


        <?php
        ActiveForm::end();
        echo '<div class="e_sign_block" id="sign_contract_' . $contract->id . '"></div>';
        //                Pjax::end();
        break;
        } elseif (($contract->status == 'active') && $contract->awardID == $awardId) { ?>


            <h2 class="text-center"><?= Yii::t('app', 'Контрактна документацiя') ?></h2>
            <div class="info-block modal_document_block">
                <?php
                if (isset($contract->documents)) {
                    //формируем массив из последних версий файлов.
                    $fileId = '';
                    $realContract = [];
                    foreach (array_reverse($contract->documents) as $d => $document) {
                        if ($fileId == $document->id) continue;

                        $realContract[] = $document;
                        $fileId = $document->id;
                    }

                    foreach (array_reverse($realContract) as $d => $document) {
                        ?>

                        <div class="margin_b_20 row contract_file_block panel-body">
                            <div class="col-md-4">
                                <?= $document->title ?>
                            </div>
                            <div class="col-md-4">
                                <?= \app\models\DocumentType::getType($document->documentType) ?>
                            </div>

                            <?php echo '<div class="col-md-2 text-right"><a href="' . $document->url . '" title="' . Yii::t('app', 'contractDocument') . htmlspecialchars($document->title) . '">' . Yii::t('app', 'download') . ' <span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span></a></div>'; ?>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>

            <?php

            foreach ($tender->contracts as $c => $contract) {
                if ($contract->awardID == $awardId) {

                    /*-------------------------------------------------------------------------------------------------- CONTRACTING*/
                    $contracting = \app\models\Contracting::find()->where(['contract_id' => $contract->id])->one();
                    if ($contracting && $contracting->token) {
                        echo Html::a(Yii::t('app', 'Змiни/виконання договору'), '/' . \app\models\Companies::getCompanyBusinesType() . '/contracting/view/' . $contracting->id, ['class' => 'btn btn-primary pull-right']);
                    }
                    /*----------------------------------*/

                    echo Yii::t('app', 'contractNumber') . ' - ' . $contract->contractNumber . '<br/>';
                    echo Yii::t('app', 'contractdateSigned') . ' - ' . Yii::$app->formatter->asDatetime($contract->dateSigned) . '<br/>';
                    echo Yii::t('app', 'contractPeriodStartDate') . ' - ' . $contract->period->startDate . '<br/>';
                    echo Yii::t('app', 'contractPeriodEndDate') . ' - ' . $contract->period->endDate . '<br/>';
                }
            }
            ?>


            <?php
            break;
        }
        }
        } else {
            foreach ($tender->contracts as $c => $contract) {

                if ($contract->awardID == $awardId) { ?>

                    <h2 class="text-center"><?= Yii::t('app', 'Контрактна документацiя') ?></h2>
                    <div class="info-block modal_document_block">
                        <?php
                        if (isset($contract->documents)) {
                            //формируем массив из последних версий файлов.
                            $fileId = '';
                            $realContract = [];
                            foreach (array_reverse($contract->documents) as $d => $document) {
                                if ($fileId == $document->id) continue;

                                $realContract[] = $document;
                                $fileId = $document->id;
                            }

                            foreach (array_reverse($realContract) as $d => $document) {
                                ?>

                                <div class="margin_b_20 row contract_file_block panel-body">
                                    <div class="col-md-4">
                                        <?= $document->title ?>
                                    </div>
                                    <div class="col-md-4">
                                        <?= \app\models\DocumentType::getType($document->documentType) ?>
                                    </div>

                                    <?php echo '<div class="col-md-2 text-right"><a href="' . $document->url . '" title="' . Yii::t('app', 'contractDocument') . htmlspecialchars($document->title) . '">' . Yii::t('app', 'download') . ' <span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span></a></div>'; ?>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>

                    <?php

                    foreach ($tender->contracts as $c => $contract) {
                        if ($contract->awardID == $awardId) {

                            /*-------------------------------------------------------------------------------------------------- CONTRACTING*/
                            $contracting = \app\models\Contracting::find()->where(['contract_id' => $contract->id])->one();
                            if ($contracting) {
                                echo Html::a(Yii::t('app', 'Змiни/виконання договору'), ['/' . \app\models\Companies::getCompanyBusinesType() . '/contracting/view/' . $contracting->id], ['class' => 'btn btn-primary pull-right']);
                            }
                            /*----------------------------------*/

                            echo Yii::t('app', 'contractNumber') . ' - ' . $contract->contractNumber . '<br/>';
                            echo Yii::t('app', 'contractdateSigned') . ' - ' . Yii::$app->formatter->asDatetime($contract->dateSigned) . '<br/>';
                            echo Yii::t('app', 'contractPeriodStartDate') . ' - ' . $contract->period->startDate . '<br/>';
                            echo Yii::t('app', 'contractPeriodEndDate') . ' - ' . $contract->period->endDate . '<br/>';
                        }
                    }
                    ?>

                    <?php
                    break;
                }
            }
        }
        ?>

        <?php
        //        if($tenders->id == '27037'){
        //            echo Html::a(Yii::t('app', 'Контрактинг'),
        //                Yii::$app->urlManager->createAbsoluteUrl([
        //                    \app\models\Companies::getCompanyBusinesType().'/contracting',
        //                    'id' => $tenders->id,
        //                ]), [
        //                    'class' => 'btn btn-success',
        //                    'role' => 'button',
        ////                        'target'=>'_blank'
        //                ]);
        //        }
        ?>
        </div>
    </td>
</tr>