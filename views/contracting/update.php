<?php
/**
 * @var $changesModel \app\models\contractModels\Changes
 * @var $contracts \app\models\contractModels\Contract
 * @var $contract \app\models\contractModels\Contract
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$fieldLabel = $contract->attributeLabels();
?>
    <div class="tender-preview wrap-preview">

        <?php
        echo $this->render('/site/head', [
            'title' => $this->title,
            'descr' => ''
        ]);
        ?>

        <?php
        $form = ActiveForm::begin([
            'validateOnType' => true,
            'validateOnBlur' => true,
            'validateOnChange' => true,
//        'enableClientValidation' => false,
//        'enableAjaxValidation' => true,
            'options' => [
                'class' => 'form-horizontal',
                'enctype' => 'multipart/form-data'
            ],
            'id' => 'contracts',
            'fieldConfig' => [
                'labelOptions' => ['class' => 'col-md-3 control-label'],
            ],
        ]);

        $template = "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>";
        ?>

        <hr/>
        <input type="hidden" id="current_locale" value="<?= substr(Yii::$app->language, 0, 2) ?>">
        <div class="info-block margin_b">

            <div class="row tender-id-box">
                <div class="col-md-3"><?= Yii::t('app', 'ID') ?></div>
                <div class="col-md-9"><b tid="tenderID"><?= @$contract->id ?></b></div>
            </div>

            <div class="row tender-id-box">
                <div class="col-md-3"><?= Yii::t('app', 'ContractID') ?></div>
                <div class="col-md-9"><b tid="tenderID"><?= @$contract->contractID ?></b></div>
            </div>


            <h2><?= Yii::t('app', 'РЕКВІЗИТИ КОНТРАКТУ') ?></h2>

            <div class="contact_group_wrapper">

                <div class="row one-row-style">
                    <div
                        class="col-md-3"><?= Yii::t('app', 'Сума контракту') ?></div>
                    <div class="col-md-6">
                        <b>
                            <i tid="value.amount">
                                <?= Html::encode($contract->value->amount . ' ' . $contract->value->currency) ?>
                                <?= Html::encode(\app\models\tenderModels\Value::getPDV()[(int)$contract->value->valueAddedTaxIncluded]) ?>
                            </i>
                        </b>
                    </div>
                </div>

                <div class="row one-row-style">
                    <div
                        class="col-md-3"><?= $contract->getAttributeLabel('contractNumber') ?></div>
                    <div class="col-md-6">
                        <b> <i>  <?= Html::encode($contract->contractNumber) ?></i></b>
                    </div>
                </div>

                <div class="row one-row-style">
                    <div
                        class="col-md-3"><?= Yii::t('app', 'Дата пiдписання') ?></div>
                    <div class="col-md-6">
                        <b> <i>  <?= Html::encode(Yii::$app->formatter->asDatetime($contract->dateSigned, 'dd/MM/yyyy H:m:s')) ?></i></b>
                        <?= Html::hiddenInput('Validate[dateSigned]', Yii::$app->formatter->asDatetime($contract->dateSigned, 'dd/MM/yyyy H:m:s'), [
                            'class' => 'form-control',
                            'id' => 'signed-date',
                        ]); ?>
                    </div>
                </div>

                <div class="row one-row-style">
                    <div
                        class="col-md-3"><?= Yii::t('app', 'Дата початку дiї') ?></div>
                    <div class="col-md-6">
                        <b> <i>  <?= Html::encode(Yii::$app->formatter->asDatetime($contract->period->startDate, 'dd/MM/yyyy H:m:s')) ?></i></b>
                    </div>
                </div>

                <div class="row one-row-style">
                    <div
                        class="col-md-3"><?= Yii::t('app', 'Дата завершення дiї') ?></div>
                    <div class="col-md-6">
                        <b> <i>  <?= Html::encode(Yii::$app->formatter->asDatetime($contract->period->endDate, 'dd/MM/yyyy H:m:s')) ?></i></b>
                    </div>
                </div>
                <? if (isset($contract->dateModified) && $contract->dateModified) { ?>
                    <div class="row one-row-style">
                        <div class="col-md-3"><?= Yii::t('app', 'Дата останньої зміни') ?></div>
                        <div class="col-md-6">
                            <b><i><?= Html::encode(Yii::$app->formatter->asDatetime($contract->dateModified, 'dd/MM/yyyy H:m:s')) ?></i></b>
                            <?= Html::hiddenInput('Validate[dateModified]', Yii::$app->formatter->asDatetime($contract->dateModified, 'dd/MM/yyyy H:m:s'), [
                                'class' => 'form-control',
                                'id' => 'last-update-date',
                            ]); ?>
                        </div>
                    </div>
                <? } ?>

            </div>


            <h2><?= Yii::t('app', 'ІНФОРМАЦІЯ ПРО ПОСТАЧАЛЬНИКА') ?></h2>

            <div class="contact_group_wrapper">

                <div class="row one-row-style">
                    <div
                        class="col-md-3"><?= $contract->suppliers[0]->getAttributeLabel('name') ?></div>
                    <div class="col-md-6">
                        <b> <i>  <?= Html::encode($contract->suppliers[0]->name) ?></i></b>
                    </div>
                </div>

                <div class="row one-row-style">
                    <div
                        class="col-md-3"><?= $contract->suppliers[0]->identifier->getAttributeLabel('id') ?></div>
                    <div class="col-md-6">
                        <b> <i>  <?= Html::encode($contract->suppliers[0]->identifier->id) ?></i></b>
                    </div>
                </div>

                <div class="row one-row-style">
                    <div class="col-md-3"><?= Yii::t('app', 'Юридична адреса') ?></div>
                    <div class="col-md-9"><b>
                            <?= @$contract->suppliers[0]->address->postalCode ?>
                            <?= @$contract->suppliers[0]->address->countryName ?>
                            <?= @$contract->suppliers[0]->address->streetAddress ?>
                            <?= @$contract->suppliers[0]->address->region ?>
                            <?= @$contract->suppliers[0]->address->locality ?>
                        </b>
                    </div>
                </div>

            </div>


            <h2><?= Yii::t('app', 'ЗМІНИ ДО КОНТРАКТУ') ?></h2>
            <input type="hidden" id="tender_id" value="<?= isset($contracts->id) ? $contracts->id : '' ?>">
            <div class="contact_group_wrapper">
                <?= $form->field($changesModel, 'rationaleTypes', ['template' => $template])
                    ->checkboxList(\app\models\contractModels\Contract::getContractChangesValue()) ?>

                <?= $form->field($changesModel, 'rationale', ['template' => $template])->textInput() ?>

                <?= $form->field($changesModel, 'contractNumber', ['template' => $template])->textInput() ?>

                <?= $form->field($changesModel, 'dateSigned', ['template' => $template])->textInput([
                    'class' => 'form-control picker',
                ]) ?>


                <div class="swichRationaleTypes swichRationaleTypes_volumeCuts swichRationaleTypes_itemPriceVariation swichRationaleTypes_priceReduction swichRationaleTypes_taxRate swichRationaleTypes_thirdParty">
                    <?= $form->field($contract->value, '[0]amount', ['template' => $template])->textInput([
                        'name'=>'',
                        'value'=>'',
                        'class'=>'form-control contract_change_amount'
                    ])->label(Yii::t('app','Сума додаткової угоди')) ?>

                    <?= $form->field($contract->value, '[1]amount', ['template' => $template])->textInput([
                        'disabled'=>true,
                        'class'=>'form-control result_amount'
                    ])->label(Yii::t('app','Сума контракту')) ?>
                    <?= Html::hiddenInput('Changes[value][amount]', '', [
                        'class'=>'form-control result_amount'
                    ]);?>

                </div>

                <div class="line swichRationaleTypes swichRationaleTypes_durationExtension swichRationaleTypes_fiscalYearExtension">
                    <?= $form->field($contract->period, 'endDate', ['template' => $template])
                        ->textInput([
                            'name' => 'Changes[period][endDate]',
                            'class' => 'form-control picker itemdeliverydate-enddate'
                        ])->label('Дата завершення дiї') ?>
                </div>






            </div>




            <h2><?= Yii::t('app', 'ДОКУМЕНТИ КОНТРАКТУ/ЗМІН') ?></h2>



            <div class="info-block document_block">

                <?php
                //формируем массив из последних версий файлов.
//                $realDocuments = \app\models\tenderModels\Document::getLastVersionDocuments($contract->documents);
                $realDocuments = [];
                $tmp['__EMPTY_DOC__'] = new \app\models\tenderModels\Document();
                $realDocuments = array_merge($tmp, $realDocuments);

//                Yii::$app->VarDumper->dump($realDocuments, 10, true);die;


                    foreach ($realDocuments as $d => $doc) {
                        if ($d === '__EMPTY_DOC__') {echo '<div id="hidden_document_original"class="row margin23 panel-body" style="display: none">';}
                        else {echo '<div class="row margin23 panel-body">';}

                        echo $this->render('edit/_document', [
                            'form' => $form,
                            'template' => $template,
                            'documents' => $doc,
                            'k' => $d,
                            'lot_items' => [],
                            'currentLotId' => ''
                        ]);
                        if ($d === '__EMPTY_DOC__') {echo '</div>';}
                        else {echo '</div>';}
                    }



//                unset($tender->documents['iClass']);
//                unset($tender->documents['__EMPTY_DOC__']);

                ?>

            </div>
            <a role="button" class="btn btn-success col-md-2 uploadfile" href="javascript:void(0)"><?= Yii::t('app', 'add file') ?></a>

            <div class="clearfix"></div>


<!--            <h2>--><?//= Yii::t('app', 'Специфiкацiя закупiвлi') ?><!--</h2>-->
<!--            <div class="info-block lots_block">-->
<!--                <div class="lot">-->
<!---->
<!--                    <div class="info-block items_block">-->
<!---->
<!---->
<!--                        --><?php
//                        //Yii::$app->VarDumper->dump($contract->items, 10, true);die;
//                        foreach ($contract->items as $i => $item) {
//                            if ($i === '__EMPTY_ITEM__') echo '<div id="hidden_item_original" style="display: none;">';
//
//                            echo $this->render('edit/_item', [
//                                'k' => $i,
//                                'item' => $item,
//                                'form' => $form,
//                                'template' => $template,
//                            ]);
//
//                            if ($i === '__EMPTY_ITEM__') echo '</div>';
//
//                        } ?>
<!---->
<!---->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->


            <div class="col-md-offset-3 col-md-9">
                <?php
                echo Html::submitButton(Yii::t('app', 'Зберегти та перейти до публiкацiї'), ['class' => 'btn btn-default btn_submit_form']);
                if($tenders->tender_method == 'open_belowThreshold'){
                    echo '<br/>';
                    echo Html::checkbox('needSign',false,['label'=>Yii::t('app','Я хочу пiдписати')]);
                }
                ?>
            </div>

            <?php ActiveForm::end(); ?>
            <?php

            //        Yii::$app->VarDumper->dump($contract, 10, true);die;
            ?>
        </div>
    </div>
<?php
echo $this->render('../tender/classificator_modal');
$this->registerJsFile(Url::to('@web/js/contracting.js'), ['position' => yii\web\View::POS_END, 'depends' => 'yii\web\JqueryAsset']);
?>