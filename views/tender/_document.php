<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
//Yii::$app->VarDumper->dump($documents->id, 10, true);
if($documents->is_old){ ?>
    <?=Yii::t('app','old_document')?> - <s><i><a href="<?= Html::encode($documents->url) ?>"><?= Html::encode($documents->title) ?></a></i></s>
<?php }else { ?>




    <div class="row file_wrap">
        <div class="row">
            <div class="col-md-3"><label
                    class="control-label file_original_name"><?= $documents->title ?></label>
            </div>
            <div class="col-md-3 btn-group">

                <input type="text" class="form-control file_name"
                       name="Tender[documents][<?= $k ?>][title]"
                       value="<?= $documents->title ?>">
            </div>
            <div class="col-md-3 btn-group document_type">
                <?= Html::dropDownList('Tender[documents][' . $k . '][documentType]', $documents->documentType, \app\models\DocumentType::getType(null,'tender'), ['class' => 'form-control']) ?>
            </div>

        </div>
        <div class="row document_link">
            <div class="col-md-3"><label class="control-label"></label></div>
            <div class="col-md-3 btn-group">

            </div>
            <div class="col-md-3 btn-group">
                <input type="hidden" class="form-control related_id"
                       name="Tender[documents][<?= $k ?>][relatedItem]"
                       value="<?= $documents->relatedItem ?>">

                <select class="form-control" name="Tender[documents][<?= $k ?>][relatedItem]">
                    <?php
                    $res = \app\models\tenderModels\Feature::getFeatureTypes();
                    foreach ($res as $num => $row) {
//                        echo '<optgroup label="' . $row . '">' . $row . '</optgroup>';
                        echo '<optgroup rel="'.$num.'" label="' . $row . '">' . $row . '</optgroup>';
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="row">
            <input type="hidden" class="form-control file_id"
                   name="Tender[documents][<?= $k ?>][id]"
                   value="<?= $documents->id ?>">
            <input type="hidden" class="form-control real_name" name="Tender[documents][<?= $k ?>][realName]"
                   value="<?= $documents->realName ?>">
            <div class="col-md-offset-3 col-md-6  btn-group">
<!--                <div class="replace_wrap">-->
                    <a role="button" class="btn btn-warning uploadfile_replace" href="javascript:void(0)"><?= Yii::t('app', 'replace') ?></a>
<!--                    <button type="button" class="btn btn-default uploadfile_replace">Заменить</button>-->
<!--                </div>-->
                <?php
                //Yii::$app->VarDumper->dump($documents, 10, true);
                if ($documents->id == '') { ?>
                    <a role="button" class="btn btn-danger delete_file" del="<?= $documents->realName ?>" href="javascript:void(0)"><?= Yii::t('app', 'Delete') ?></a>
<!--                    <button type="button" del="" class="btn btn-default delete_file">Удалить</button>-->
                <?php } ?>
            </div>
        </div>
    </div>
<?php } ?>


