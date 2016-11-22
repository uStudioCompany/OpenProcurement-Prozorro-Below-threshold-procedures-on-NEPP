<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/**
 * User: Nik
 * Date: 29.01.2016
 * Time: 13:10
 *
 * @var $form yii\widgets\ActiveForm
 * @var $tender app\models\tenderModels\Tender
 * @var $tendersId int
 * @var $tenders app\models\Tenders
 * @var $company app\models\Companies
 *
 */

$this->title = \app\models\Companies::findOne(['id'=>Yii::$app->user->identity->company_id])->legalName;
$fieldLabel = $tender->attributeLabels();
?>
    <div class="tender-preview  m_tender-cancel">

        <input type="hidden" id="current_locale" value="<?= substr(Yii::$app->language, 0, 2) ?>">
        <?php

        echo $this->render('/site/head', [
            'title' => $this->title,
            'descr' => 'Скасування закупiвлi'
        ]);

        $form = ActiveForm::begin([
            'validateOnType' => true,
            'validateOnBlur' => true,
            'validateOnChange' => true,
            'options' => [
                'class' => 'form-horizontal',
                'enctype' => 'multipart/form-data'
            ],
            'id' => 'tender_cancellation',
            'fieldConfig' => [
                'labelOptions' => ['class' => 'col-md-3 control-label'],
            ],
        ]);

        $template = "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>";

        ?>
        <?php if (Yii::$app->session->hasFlash('message')) { ?>
            <div class="bs-example">
                <div class="alert alert-success fade in"><a href="#" class="close"
                                                            data-dismiss="alert">&times;</a><?= Yii::$app->session->getFlash('message'); ?>
                </div>
            </div>
        <?php } ?>

        <?php if (Yii::$app->session->hasFlash('message_error')) { ?>
            <div class="bs-example">
                <div class="alert alert-danger fade in"><a href="#" class="close"
                                                           data-dismiss="alert">&times;</a><?= Yii::$app->session->getFlash('message_error'); ?>
                </div>
            </div>
        <?php } ?>

        <div class="info-block">
            <h4><?=Yii::t('app','Закупiвля')?></h4>

            <div class="row tender-id-box">
                <div class="col-md-3">TenderID</div>
                <div class="col-md-9"><b><?= @$tender->tenderID ?></b></div>
            </div>
            <div class="row tender-id-box">
                <div class="col-md-3">ID</div>
                <div class="col-md-9"><b><?= @$tender->id ?></b></div>
            </div>

            <hr/>

            <input type="hidden" id="tenders_id" value="<?= $tendersId ?>" name="Tenders[id]">

            <h4><?=Yii::t('app','Загальна iнформацiя про закупiвлю')?></h4>

            <div class="row">
                <div class="col-md-3"><?=Yii::t('app','Загальна назва закупiвлi')?></div>
                <div class="col-md-9"><b><?= @$tender->title ?></b></div>
            </div>
            <div class="row">
                <div class="col-md-3"><?=Yii::t('app','Описова частина')?></div>
                <div class="col-md-9"><b><?= @$tender->description ?></b></div>
            </div>
            <div class="row">
                <div class="col-md-3"><?=Yii::t('app','Загальний бюджет закупiвлi')?></div>
                <div class="col-md-9">
                    <b><?= @$tender->value->amount . ' ' . @$tender->value->currency . ' ' . (@$tender->value->valueAddedTaxIncluded ? 'З ПДВ' : 'Без ПДВ') ?></b>
                </div>
            </div>

            <hr/>

            <div class="info-block">
                <h4><?=Yii::t('app','Скасування закупiвлi')?></h4>
                <div class="info-block  cancellations_block">
                    <?
                    unset($tender->cancellations['iClass']);
                    $show_empty_form = true;
                    if ($tenders->tender_type === 1 && count($tender->cancellations) > 1) {
                        $show_empty_form = false;
                    } else if (count($tender->cancellations) > 1) {
                        foreach ($tender->cancellations as $c => $cancellation) {
                            if ($c === '__EMPTY_CANCEL__') continue;
                            if ($cancellation->status === 'pending' || $cancellation->cancellationOf === 'tender') {
                                $show_empty_form = false;
                            }
                        }
                    }

                    if (!$show_empty_form) {
                        unset($tender->cancellations['__EMPTY_CANCEL__']);
                    }

                    $tender->cancellations = array_reverse($tender->cancellations);

                    foreach ($tender->cancellations as $c => $cancellation) {
                        if ($c === '__EMPTY_CANCEL__' && $show_empty_form) $c = 0;

                        echo $this->render('_cancellation_cancel', [
                            'form' => $form,
                            'tender' => $tender,
                            'tenders' => $tenders,
                            'template' => $template,
                            'cancellation' => $cancellation,
                            'cancellation_of' => 'lot',
                            'related_lot' => '',
                            'k' => $c
                        ]);
                    }
                    unset($tender->cancellations['__EMPTY_CANCEL__']);
                    ?>
                </div>

                <hr/>

                <? if (isset($tenders->status) && $tenders->status != 'cancelled') { ?>
                    <div class="row">
                        <div class="col-md-3"></div>
                        <div class="col-md-9">
                            <?= Html::submitButton(Yii::t('app', 'Скасувати закупiвлю'), ['class' => 'btn btn-default btn_submit_form']) ?>
                        </div>
                    </div>
                <? } ?>
            </div>

        </div>
        <?php ActiveForm::end(); ?>
    </div>
    <div class="col-md-3">
        <?= $this->render('view/_nav_block', [
            'tender' => $tender,
            'tenders' => $tenders
        ]);?>
    </div>
<?

$this->registerJsFile(Url::to('@web/js/cancel.js'), ['position' => yii\web\View::POS_END, 'depends' => 'yii\web\JqueryAsset']);
//$this->registerJsFile(Url::to('@web/js/ajaxupload_old.js'), ['position' => yii\web\View::POS_END, 'depends' => 'yii\web\JqueryAsset']);

$this->registerJs(
    'var FilesCount = 0;' //. /*count($tender->cancellations->documents)*/ .';'
    , yii\web\View::POS_END);