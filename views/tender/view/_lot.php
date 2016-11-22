<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

/**
 * @var $tender app\models\tenderModels\Tender
 * @var $tenderId int
 * @var $lot app\models\tenderModels\Lot
 * @var $tenders app\models\Tenders
 * @var $cancellations app\models\tenderModels\Cancellation[]
 * @var $published bool
 * @var $k int
 * @var $draft bool
 * @var $tenderType int
 */
?>

<?php
if ($lot) {
    $fieldLabel = $lot->attributeLabels();
}
?>
<div class="lot">

    <? if ($lot && $tenderType == 2) { ?>

        <div class="lots_marker" style="display: block">

            <?php
            if (count($cancellations)) {
                foreach ($cancellations AS $key => $cancellation) {
                    if (isset($cancellation->cancellationOf) && $cancellation->cancellationOf == 'lot' && isset($cancellation->relatedLot) && $cancellation->relatedLot == $lot->id && $cancellation->status == 'active') {
                        $lot_cancellation = true; ?>
                        <div class="alert alert-danger">
                            <h3> <?php echo Yii::t('app', 'Лот закупiвлi скасованно:') . ' ' . \app\models\tenderModels\Lot::getLotById($tender, $cancellation->relatedLot)->title ?></h3>
                            <?= Yii::t('app', 'Причина скасування: ') ?>
                            <b><i><?= Html::encode($cancellation->reason) ?></i></b>
                        </div>
                    <?php }
                }
            } ?>

            <?php if ($lot->status == 'unsuccessful') { ?>
                <div class="alert alert-danger">
                    <?= '<h3>' . Yii::t('app', 'Торги за лотом: {title} не вiдбулися', ['title' => $lot->title]) . '</h3>' ?>
                </div>
            <?php } ?>


            <input type="hidden" value="<?= $lot->id ?>"/>


            <div class="row one-row-style">
                <div class="col-md-3"><?= $fieldLabel['title'] ?></div>
                <div class="col-md-6">
                    <b>
                        <i>
                            <?= Html::encode($lot->title) ?>
                        </i>
                    </b>
                    <br/>
                    <b>
                        <i>
                            <?= Html::encode($lot->title_en) ?>
                        </i>
                    </b>
                </div>
            </div>


            <div class="row one-row-style">
                <div class="col-md-3"><?= $fieldLabel['description'] ?></div>
                <div class="col-md-6">
                    <b>
                        <i>
                            <?= Html::encode($lot->description) ?>
                        </i>
                    </b>
                    <br/>
                    <b>
                        <i>
                            <?= Html::encode($lot->description_en) ?>
                        </i>
                    </b>
                </div>
            </div>

            <?php if ($lot->guarantee->amount != null) { ?>
                <div class="row one-row-style">
                    <div class="col-md-3"><?= Yii::t('app', 'Банкiвськi гарантii') ?></div>
                    <div class="col-md-6">
                        <b>
                            <i>
                                <?= Html::encode($lot->guarantee->amount . ' ' . $lot->guarantee->currency); ?>
                            </i>
                        </b>
                    </div>
                </div>
            <?php } ?>

            <div class="row one-row-style">
                <div class="col-md-3"> <?= $fieldLabel['value'] ?></div>
                <div class="col-md-6">
                    <b>
                        <i>
                            <?= Html::encode($lot->value->amount . ' ' . $tender->value->currency) ?>
                            <?= Html::encode(\app\models\tenderModels\Value::getPDV()[(int)$tender->value->valueAddedTaxIncluded]) ?>
                        </i>
                    </b>
                </div>
            </div>

            <?php if ($lot->minimalStep->amount != null) { ?>
            <div class="row one-row-style">
                <div class="col-md-3">  <? echo Yii::t('app', 'lot_step') ?></div>
                <div class="col-md-6">
                    <b>
                        <i>
                            <?= Html::encode($lot->minimalStep->amount . ' ' . $tender->value->currency) ?>
                            <?= Html::encode(\app\models\tenderModels\Value::getPDV()[(int)$tender->value->valueAddedTaxIncluded]) ?>
                        </i>
                    </b>
                </div>
            </div>
            <?php } ?>

            <h2><?= Yii::t('app', 'Документацiя лота') ?></h2>

            <div class="info-block grey document_block">

                <?php if ($tenderType == 2) {

                    $lotItemArrId = [];
                    foreach ($items as $rr => $item) {
                        if ($rr === 'iClass') continue;
                        if ($rr === '__EMPTY_ITEM__') continue;
                        if ($lot->id == $item->relatedLot) {
                            $lotItemArrId[] = $item->id;
                        }
                    }
                    $checkDoc = false;
                    foreach ($documents as $d => $doc) {
                        if ($d === 'iClass') continue;
                        if ($d === '__EMPTY_DOC__') continue;
//                        Yii::$app->VarDumper->dump($doc->relatedItem , 10, true);
//                        Yii::$app->VarDumper->dump($lot->id , 10, true);
                        if ($lot->id == $doc->relatedItem || in_array($doc->relatedItem, $lotItemArrId)) {
                            $checkDoc = true;
                            echo $this->render('_document', [
                                'documents' => $doc,
                                'k' => $d,
                                'tender' => $tender
//                            'lot_items' => $lotItemArrId,
//                            'currentLotId' => $lot->id
                            ]);
                        }
                    }
                    $lotItemArrId[] = $lot->id;
//                echo \app\models\DocumentUploadTask::GetUploadedDoc($tenderId, 'lot', $lotItemArrId);
                }
                if ($published) {
                    $uploadedDoc = \app\models\DocumentUploadTask::GetUploadedDoc($tenderId, 'tender', ['lot'], $lot->id);
                    echo $uploadedDoc;
                    $uploadedDoc = $uploadedDoc == '' ? false : true;
                    if (!$draft && !$checkDoc && !$uploadedDoc) {
                        echo '<h4>' . Yii::t('app', 'Дані не було додано') . '</h4>';
                    }
                }
                ?>
            </div>


            <div class="info-block  features_block">
                <h2><?= Yii::t('app', 'Нецiновi показники лоту') ?></h2>
                <?php if ($tenderType == 2) {

                    foreach ($features as $f => $feature) {

                        if ($f === 'iClass') continue;
                        if ($f === '__EMPTY_FEATURE__') continue;

                        if ($lot->id == $feature->relatedItem || in_array($feature->relatedItem, $lotItemArrId)) {
                            $checkFeature = true;
                            echo $this->render('_feature', [
                                'feature' => $feature,
                                'k' => $f,
                                'tender' => $tender
                            ]);
                        }
                    }
                } ?>
            </div>
            <?php
            if (!$draft && !$checkFeature) {
                echo '<h4>' . Yii::t('app', 'Дані не було додано') . '</h4>';
            } ?>
        </div>
    <? } ?>

    <div class="info-block items_block">
        <h2><?= Yii::t('app', 'Специфiкацiя закупiвлi') ?></h2>
        <?php
        foreach ($items as $i => $item) {

            $lot_id = null;
            if ($lot) {
                $lot_id = $lot->id;
            }

            if ($lot_id == $item->relatedLot) {
                echo $this->render('_item', [
                    'k' => $i,
                    'item' => $item,
                    'currentLotId' => $lot_id
                ]);
            }
        } ?>
    </div>

    <?php if (count($tender->contracts)) { ?>
        <div class="info-block contract_block">
            <h2><?= Yii::t('app', 'Contracts BLOCK') ?></h2>
            <?
            foreach ($tender->contracts AS $c => $contract) {
                if ($contract->status === 'cancelled') continue;
                if (count($contract->documents)) {
                    if ($lot) {
                        if ($lot->id !== \app\components\ApiHelper::findLotByAwardId($tender, $contract->awardID)) {
                            continue;
                        }
                    }

                    foreach ($contract->documents AS $cd => $document) {
                        echo '<div class="row"><div class="col-md-3">' . \app\models\DocumentType::getType($document->documentType) . '</div><div class="col-md-9"><b><a href="' . $document->url . '">' . $document->title . '</a></b></div></div>';
                    }
                }
            }
            ?>
        </div>
    <?php } ?>

</div>