<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

if (!isset($cancellations)) { $cancellations = '';}

?>
    <div class="row file_wrap">
        <div class="row">
            <div class="col-md-3"><label
                    class="control-label file_original_name"><?= $documents->title ?></label>
            </div>
            <div class="col-md-6 btn-group">
                <input type="text" class="form-control file_name"
                       name="Tender<?=$cancellations?>[documents][<?= $k ?>][title]"
                       value="<?= $documents->title ?>">
            </div>
        </div>

        <div class="row">
            <input type="hidden" class="form-control file_id"
                   name="Tender<?=$cancellations?>[documents][<?= $k ?>][id]"
                   value="<?= $documents->id ?>">
            <input type="hidden" class="form-control real_name" name="Tender<?=$cancellations?>[documents][<?= $k ?>][realName]"
                   value="<?= $documents->realName ?>">
            <input type="hidden" class="form-control real_name" name="Tender<?=$cancellations?>[documents][<?= $k ?>][relatedItem]"
                   value="<?= $documents->relatedItem ?>">

<!--            <div class="col-md-offset-3 col-md-3 btn-group document_type">-->
<!--                --><?//= Html::dropDownList('Tender'. $cancellations .'[documents]['. $k .'][documentType]', $documents->documentType, ArrayHelper::map(\app\models\DocumentType::getTypes(), 'id', ['title']), ['class' => 'form-control']) ?>
<!--            </div>-->

            <div class=" col-md-3  btn-group">
<!--                <div class="replace_wrap">-->
<!--                    <button type="button" class="btn btn-default uploadfile_replace">--><?//=Yii::t('app','replace')?>
<!--                    </button>-->
<!--                </div>-->
                <?php
                if (!isset($documents->id)) { ?>
                    <button type="button" del="" class="btn btn-default delete_file"><?=Yii::t('app','delete')?></button>
                <?php } ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-offset-3 col-md-6">
                <hr />
            </div>
        </div>
    </div>



