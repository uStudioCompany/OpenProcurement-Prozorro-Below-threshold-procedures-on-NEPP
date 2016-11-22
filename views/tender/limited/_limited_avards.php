<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

?>

<?php
echo $this->render('../small_info_block', [
    'tender' => $tender,
    'tenders' => $tenders
]);
?>

    <div class="tender-preview m_create-wrap">
        <?= Yii::$app->session->getFlash('success'); ?>

        <?php $form = ActiveForm::begin([
            'validateOnType' => true,
            'enableAjaxValidation' => false,
            'id' => 'add_award_form',

            'options' => ['class' => 'form-horizontal'],
            'fieldConfig' => [
                'labelOptions' => ['class' => 'col-md-3 control-label'],
            ],
        ]);
        ?>


        <h2><?= Yii::t('app', 'ABOUT PARTICIPANT') ?></h2>
        <? if ($tenders->tender_type == 2) { ?>
        <div class="info-block">
            <div class="form-group">
                <div class="col-md-4">
                    <h6><?= Yii::t('app', 'Lot Description') ?></h6>
                </div>
                <div class="col-md-6">
                    <?= Html::dropDownList(null, null, \app\models\tenderModels\Award::checkLotAwardsForDropdown($tender), [
                        'id' => 'lot_id_select',
                        'class' => 'form-control',
                        'onchange' => 'ChangeDataInAwardField(), changeSubmitValue(this)'
                    ]); ?>
                </div>
            </div>
        </div>
    <? } ?>
        <br>
        <?php foreach ($awards as $lotId => $award) {
            ?>
            <div class="limited_award_form" id="<?= $lotId ?>">
                <div class="info-block">
                    <?php
                    if ($tenders->tender_type == 2) {
                        echo  $form->field($award, 'lotID')->hiddenInput([
                            'value' => $lotId,
                            'name' => 'Award[lotID]',
                        ])->label(false);
                    }
                    ?>
                    <?= $form->field($award->suppliers[0]->address, '[' . $lotId . ']' . 'countryName', [
                        'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>"
                    ])->dropDownList(ArrayHelper::map(\app\models\Countries::find()->all(), 'id', 'name'), [
                        'name' => 'Award[suppliers][0][address][countryName]',

                        'onchange' => '$.post( "' . Yii::$app->urlManager->createUrl(["register/getcountrysheme"]) . '",{id:$(this).val()},function(data){
                var companiesCountryname = $("#identifier-scheme").val();
                $("#identifier-scheme").html(data);
                $("#identifier-scheme").val(companiesCountryname);
                $("#identifier-scheme").trigger("change");
                }),
                $.post( "' . Yii::$app->urlManager->createUrl(["register/getcountryregion"]) . '",{id:$(this).val()},function(data){
                var companiesRegion = $("#address-region").val();
                $("#address-region").html(data);
                $("#address-region").val(companiesRegion);
                })'
                    ]) ?>


                    <?= $form->field($award->suppliers[0]->identifier, '[' . $lotId . ']' . 'scheme', [
                        'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>"
                    ])->dropDownList(ArrayHelper::map((new \app\models\CountrySheme)->find()->where(['country_id' => 1])->all(), 'id', 'name'), [
                        'name' => 'Award[suppliers][0][identifier][scheme]',
                        'onchange' => '$.post( "' . Yii::$app->urlManager->createUrl(["register/getshemetype"]) . '",{ids:$("option:selected",this).attr("type_ids")},function(data){
                var companiesLegaltype = $("#companies-legaltype").val();
                $("#companies-legaltype").html(data);
                $("#companies-legaltype").val(companiesLegaltype);
                })'
                    ]) ?>

                    <?= $form->field($award->suppliers[0]->identifier, '[' . $lotId . ']' . 'id', [
                        'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>",
                    ])->textInput([
                        'name' => 'Award[suppliers][0][identifier][id]',
                    ]) ?>


                    <?= $form->field($award->suppliers[0], '[' . $lotId . ']' . 'name', [
                        'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>",
                    ])->textInput([
                        'name' => 'Award[suppliers][0][name]',
                    ]) ?>

                    <?= $form->field($award->suppliers[0]->address, '[' . $lotId . ']' . 'region', [
                        'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>",
                    ])->textInput([
                        'name' => 'Award[suppliers][0][address][region]',
                    ]) ?>

                    <?= $form->field($award->suppliers[0]->address, '[' . $lotId . ']' . 'postalCode', [
                        'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>",
                    ])->textInput([
                        'name' => 'Award[suppliers][0][address][postalCode]',
                    ]) ?>

                    <?= $form->field($award->suppliers[0]->address, '[' . $lotId . ']' . 'locality', [
                        'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>",
                    ])->textInput([
                        'name' => 'Award[suppliers][0][address][locality]',
                    ]) ?>

                    <?= $form->field($award->suppliers[0]->address, '[' . $lotId . ']' . 'streetAddress', [
                        'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>",
                    ])->textInput([
                        'name' => 'Award[suppliers][0][address][streetAddress]',
                    ]) ?>

                </div>


                <div class="info-block">

                    <?= $form->field($award->suppliers[0]->contactPoint, '[' . $lotId . ']' . 'name', [
                        'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>",
                    ])->textInput([
                        'name' => 'Award[suppliers][0][contactPoint][name]',
                    ]) ?>

                    <?= $form->field($award->suppliers[0]->contactPoint, '[' . $lotId . ']' . 'telephone', [
                        'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>",
                    ])->textInput([
                        'name' => 'Award[suppliers][0][contactPoint][telephone]',
                    ]) ?>
                    <?= $form->field($award->suppliers[0]->contactPoint, '[' . $lotId . ']' . 'email', [
                        'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>",
                    ])->textInput([
                        'name' => 'Award[suppliers][0][contactPoint][email]',
                    ]) ?>
                </div>
                <div class="info-block">

                    <?= $form->field($award->value, '[' . $lotId . ']' . 'amount', [
                        'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>",
                    ])->textInput([
                        'name' => 'Award[value][amount]',
                    ])->label(Yii::t('app', 'Цiна пропозицiї'));

                    ?>

                    <?= $form->field($award, '[' . $lotId . ']' . 'subcontractingDetails', [
                        'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>",
                    ])->textarea([
                        'name' => 'Award[subcontractingDetails]',
                    ]) ?>
                    <?= $form->field($award, 'id')->hiddenInput([
                        'name' => 'Award[id]',
                    ])->label(false); ?>
                </div>
            </div>
            <?
        }
        echo Html::hiddenInput('Award[value][currency]', 'UAH');
        echo Html::hiddenInput('t_id', $tenders->id, [
            'id' => 'tender_id'
        ]);

        echo Html::hiddenInput(null, $tenderAmount, [
            'id' => 'tender_amount'
        ]);

        ?>


        <div class="form-group">
            <div class="col-md-offset-5 col-md-6">
                <?= Html::submitButton(Yii::t('app', 'save'), ['class' => 'btn btn-danger btn-submitform', 'name' => 'add_limited_avards', 'rel'=>Yii::t('app', 'save')]) ?>
            </div>


            <!--            <div class="tender_contract_block">-->
            <!--                <button type="button" class="close" data-dismiss="modal" aria-label="Close">-->
            <!--                    <span aria-hidden="true">&times;</span>-->
            <!--                </button>-->
            <!---->
            <!--                <h2 class="text-center">--><? //=Yii::t('app','Контрактна документацiя')?><!--</h2>-->
            <!--                <div class="info-block modal_document_block">-->
            <!---->
            <!--                    <div class="c_doc_example">-->
            <!--                        --><? //= Html::dropDownList('documents[__CONTRACT_DOC__][documentType]', null, \app\models\DocumentType::getType(null,'award'),
            //                        [
            //                        'class' => 'form-control contract_document_type_select',
            //                        ]);
            //                        ?>
            <!--                    </div>-->
            <!--                    --><?php
            ////                    Yii::$app->VarDumper->dump($award->documents, 10, true);die;
            //                    if (isset($award->documents)) {
            //                        //формируем массив из последних версий файлов.
            //                        $fileId = '';
            //                        $realContract = [];
            //                        foreach (array_reverse($award->documents) as $d => $document) {
            //                            if ($fileId == $document->id) continue;
            //
            //                            $realContract[] = $document;
            //                            $fileId = $document->id;
            //                        }
            //
            //                        foreach (array_reverse($realContract) as $d => $document) {
            //                            ?>
            <!---->
            <!--                            <div class="margin_b_20 row contract_file_block panel-body">-->
            <!--                                <div class="col-md-4">-->
            <!--                                    --><? //= Html::textInput('documents[' . $d . '][title]', $document->title, [
            //                                        'class' => 'form-control file_original_name'
            //                                    ]) ?>
            <!--                                </div>-->
            <!--                                --><? //= Html::hiddenInput('documents[' . $d . '][original_name]', $document->title, [
            //                                    'class' => 'form-control file_original_name'
            //                                ]) ?>
            <!--                                <div class="col-md-4">-->
            <!--                                    --><? //= Html::dropDownList('documents[' . $d . '][documentType]', $document->documentType, \app\models\DocumentType::getType(null, 'award'),
            //                                        [
            //                                            'class' => 'form-control contract_document_type_select_isset',
            //                                        ]);
            //                                    ?>
            <!---->
            <!--                                </div>-->
            <!--                                <a role="button" class="btn btn-warning col-md-2 replace_file"-->
            <!--                                   href="javascript:void(0)">-->
            <? //= Yii::t('app', 'replace') ?><!--</a>-->
            <!--                                --><?php //echo '<div class="col-md-2 text-right download_link"><a href="' . $document->url . '" title="' . Yii::t('app', 'contractDocument') . htmlspecialchars($document->title) . '">' . Yii::t('app', 'download') . ' <span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span></a></div>'; ?>
            <!--                                --><? //= Html::hiddenInput('documents[' . $d . '][realName]', null, [
            //                                    'class' => 'real_name'
            //                                ]) ?>
            <!--                                --><? //= Html::hiddenInput('documents[' . $d . '][id]', $document->id, [
            //                                    'class' => 'document_id'
            //                                ]) ?>
            <!--                            </div>-->
            <!--                            --><?php
            //
            //                        }
            //                    }
            //                    ?>
            <!---->
            <!--                </div>-->
            <!--                <div class="row margin_l_20">-->
            <!--                    <a role="button" class="btn btn-success col-md-2 uploadcontract"-->
            <!--                        href="javascript:void(0)">--><? //= Yii::t('app', 'add_file') ?><!--</a>-->
            <!--                    <a role="button" class="btn btn-info col-md-2" href="javascript:void(0)" class="close" data-dismiss="modal" aria-label="Close">-->
            <? //= Yii::t('app', 'ok') ?><!--</a>-->
            <!--                </div>-->

            <!--                <div class="row" align="center">-->
            <!--                    --><? //= Html::submitButton(Yii::t('app', 'Завантажити'), [
            //                        'class' => 'btn btn-default btn_submit_award_contract btn-success',
            //                        't_id' => $tenders->id,
            //                        'data-loading-text' => '<i class=\'fa fa-spinner fa-spin \'></i>' . Yii::t('app', 'Зачекайте')
            //                    ]); ?>
            <!---->
            <!--                    --><?php
            //                    //Yii::$app->VarDumper->dump($awardCompaintsPeriod->endDate, 10, true);
            //                    ?>
            <!---->
            <!--                    --><? //
            ////                    if (strtotime(str_replace('/','-',$awardCompaintsPeriod->endDate)) < strtotime('now')) {
            ////
            ////                        echo  Html::submitButton(Yii::t('app', 'Активувати'), [
            ////                            'class' => 'btn btn-default btn_submit_award_contract_activate btn-danger',
            ////                            't_id' => $tendersId,
            ////                            'activate' => 'activate',
            ////                            'data-loading-text' => '<i class=\'fa fa-spinner fa-spin \'></i>' . Yii::t('app', ' Зачекайте')
            ////                        ]);
            //////
            ////                    }
            //                    ?>
            <!---->
            <!---->
            <!--                </div>-->
            <!--            </div>-->


        </div>
        <?php ActiveForm::end(); ?>
    </div>

    <div class="modal fade" id="contract-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-sw">
            <div class="modal-content">
                <!--                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>-->
                <div class="modal-body">
                    ...
                </div>
            </div>
        </div>
    </div>

<?php
echo $this->render('../view/_nav_block', [
    'tender' => $tender,
    'tenders' => $tenders
]);
$this->registerJsFile(Url::to('@web/js/award.js'), ['position' => yii\web\View::POS_END, 'depends' => 'yii\web\JqueryAsset']);
?>