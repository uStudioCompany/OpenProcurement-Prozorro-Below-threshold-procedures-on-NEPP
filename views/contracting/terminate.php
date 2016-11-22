<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;


//Yii::$app->VarDumper->dump($contract, 10, true);die;
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
                <div class="col-md-3"><?= Yii::t('app', 'ContractID') ?></div>
                <div class="col-md-9"><b tid="tenderID"><?= @$contract->id ?></b></div>
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
                        <b> <i>  <?= Html::encode(Yii::$app->formatter->asDatetime($contract->dateSigned)) ?></i></b>
                    </div>
                </div>

                <div class="row one-row-style">
                    <div
                        class="col-md-3"><?= Yii::t('app', 'Дата початку дiї') ?></div>
                    <div class="col-md-6">
                        <b> <i>  <?= Html::encode(Yii::$app->formatter->asDatetime($contract->period->startDate)) ?></i></b>
                    </div>
                </div>

                <div class="row one-row-style">
                    <div
                        class="col-md-3"><?= Yii::t('app', 'Дата завершення дiї') ?></div>
                    <div class="col-md-6">
                        <b> <i>  <?= Html::encode(Yii::$app->formatter->asDatetime($contract->period->endDate)) ?></i></b>
                    </div>
                </div>

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
            <?php $contract->changes[0] = new \app\models\contractModels\Changes(); ?>
            <input type="hidden" id="tender_id" value="<?= isset($contracts->id) ? $contracts->id : '' ?>">
            <div class="contact_group_wrapper">
                <?= $form->field($contract, 'terminateType', ['template' => $template])
                    ->radioList([0 => Yii::t('app', 'contract_done'), 1 => Yii::t('app', 'contract_break')],[
                        'name' => 'Terminate[terminateType]',
                    ]) ->label('')?>

                <?= $form->field($contract->amountPaid, 'amount', ['template' => $template])->textInput([
                    'name' => 'Terminate[amountPaid][amount]',
                ])->label(Yii::t('app', 'Сума оплати за договором')) ?>

                <div class="terminationDetails">
                    <?= $form->field($contract, 'terminationDetails', ['template' => $template])->textarea([
                        'name' => 'Terminate[terminationDetails]',
                    ]) ?>
                </div>

            </div>


            <h2><?= Yii::t('app', 'ДОКУМЕНТИ КОНТРАКТУ/ЗМІН') ?></h2>


            <div class="info-block document_block">

                <?php
                $tmp['__EMPTY_DOC__'] = new \app\models\tenderModels\Document();

                foreach ($tmp as $d => $doc) {
                    if ($d === '__EMPTY_DOC__') {
                        echo '<div id="hidden_document_original"class="row margin23 panel-body" style="display: none">';

                        echo $this->render('edit/_document', [
                            'form' => $form,
                            'template' => $template,
                            'documents' => $doc,
                            'k' => $d,
                            'lot_items' => [],
                            'currentLotId' => ''
                        ]);

                        echo '</div>';
                    }

                }
                ?>

            </div>
            <a role="button" class="btn btn-success col-md-2 uploadfile"
               href="javascript:void(0)"><?= Yii::t('app', 'add file') ?></a>




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