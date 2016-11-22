<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

//Yii::$app->VarDumper->dump($documents, 10, true);
?>
<div class="row bid_file_wrap panel panel-default margin_t_20">
    <div class="panel-body">
        <div class="row document_type">
            <label><?= Yii::t('app', 'Name of document') ?></label>
            <input type="text" class="form-control file_name"
                   name="documents[<?= $k ?>][title]"
                   value="<?= $documents->title ?>">


            <?php
            //Тип документа. Послезагрузки тип менять нельзя.
            if (in_array($tenders->tender_method, ['open_aboveThresholdEU', 'open_aboveThresholdUA', 'open_aboveThresholdUA.defense']) && !$documents->id) { ?>

                <label><?= Yii::t('app', 'Type of document') ?></label>
                <div class="document_type">

                    <?= Html::dropDownList('documents[' . $k . '][documentType]', $documents->documentType, \app\models\DocumentType::getType(null, 'bid'), ['class' => 'form-control select_document_type', 'prompt' => Yii::t('app', 'Select document type')]) ?>
                </div>

            <?php } elseif (in_array($tenders->tender_method, ['open_competitiveDialogueUA', 'open_competitiveDialogueEU']) && !$documents->id) { ?>

                <label><?= Yii::t('app', 'Type of document') ?></label>
                <div class="document_type">

                    <?= Html::dropDownList('documents[' . $k . '][documentType]', $documents->documentType, \app\models\DocumentType::getType(null, 'competitive_bid'), ['class' => 'form-control select_document_type', 'prompt' => Yii::t('app', 'Select document type')]) ?>
                </div>

            <?php } else { ?>

                <input type="hidden" class="form-control related_id" name="documents[<?= $k ?>][documentType]" value="<?= $documents->documentType ?>">

            <?php } ?>

        </div>
        <div class="row document_link">


            <input type="hidden" class="form-control related_id"
                   name="documents[<?= $k ?>][relatedItem]"
                   value="<?= $documents->relatedItem ?>">


            <?php if ($tenders->tender_type == 2) { ?>
                <label><?= Yii::t('app', 'Level of document') ?></label>
                <select class="form-control select_document_level" name="documents[<?= $k ?>][relatedItem]">

                    <?php echo \app\modules\seller\helpers\HBid::getTenderLots($tender, $documents); ?>

                </select>
            <?php } else { ?>
                <input type="hidden" class="form-control related_id"
                       name="documents[<?= $k ?>][relatedItem]"
                       value="tender">
            <?php } ?>


            <input type="hidden" class="form-control file_id" name="documents[<?= $k ?>][id]" value="<?= $documents->id ?>">
            <input type="hidden" class="form-control real_name" name="documents[<?= $k ?>][realName]" value="<?= $documents->realName ?>">


        </div>

        <?php if (in_array($tenders->tender_method, ['open_aboveThresholdEU'])) { ?>
            <div class="row">

                <div class="form-group">

                    <label><input class="confidentiality" <? echo $checked = $documents->confidentiality == 'buyerOnly' ? 'checked' : ''; ?>
                            type="checkbox" value="buyerOnly" name="documents[<?= $k ?>][confidentiality]"
                            onclick="ShowConfedential(this)"><?= Yii::t('app', 'confiedintial document') ?></label>


                    <div class="confidentialityRationale">
                        <?= $form->field($documents, '[' . $k . ']confidentialityRationale')
                            ->textarea([
                                'name' => 'documents['.$k.'][confidentialityRationale]',
                            ]);
                        ?>
                    </div>
                </div>
            </div>
        <?php } elseif (in_array($tenders->tender_method, ['open_competitiveDialogueUA', 'open_competitiveDialogueEU'])) {
//            Yii::$app->VarDumper->dump($documents->confidentiality, 10, true);
            ?>
            <div class="row">
                <div class="form-group">
                    <label><input class="confidentiality" <? echo $checked = $documents->confidentiality == 'buyerOnly' ? 'checked' : ''; ?>
                                  type="checkbox" value="buyerOnly" name="documents[<?= $k ?>][confidentiality]"
                                  onclick="ShowConfedential(this)"><?= Yii::t('app', 'Опис рішення про закупівлю') ?></label>
                </div>
            </div>
        <?php } ?>



        <div class="row">
            <div class="btn-group">

                <a role="button" class="btn btn-warning uploadfile_replace"
                   href="javascript:void(0)"><?= Yii::t('app', 'replace') ?></a>

                <?php

                if ($documents->id == '') { ?>
                    <a role="button" class="btn btn-danger delete_file" del="<?= $documents->realName ?>"
                       href="javascript:void(0)"><?= Yii::t('app', 'Delete') ?></a>
                    <!--                    <button type="button" del="" class="btn btn-default delete_file">Удалить</button>-->
                <?php }else{ ?>
<!--                    <a role="button" class="btn btn-danger download_file" href="--><?//= $documents->url ?><!--">--><?//= Yii::t('app', 'Download') ?><!--</a>-->
                <?php } ?>
            </div>
        </div>
    </div>
</div>


