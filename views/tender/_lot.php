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
 */

$lot_cancellation = false;

?>
<div class="lot">

    <div class="lots_marker">

        <?php
        if (count($cancellations)) {
            foreach ($cancellations AS $key => $cancellation) {
                if (isset($cancellation->cancellationOf) && $cancellation->cancellationOf == 'lot' && isset($cancellation->relatedLot) && $cancellation->relatedLot == $lot->id && $cancellation->status=='active') {
                    $lot_cancellation = true; ?>
                    <div class="alert alert-danger">
                        <h3> <?php echo Yii::t('app', 'Лот закупiвлi скасованно:') .' '. \app\models\tenderModels\Lot::getLotById($tender, $cancellation->relatedLot)->title ?></h3>
                        <?= Yii::t('app', 'Причина скасування: ') ?>
                        <b><i><?= Html::encode($cancellation->reason) ?></i></b>
                    </div>
                <?php }
            }
        } ?>

        <?php if (true) /* (!$published) */ { ?>
            <div class="info-block">
                <div class="row">
                    <div class="col-md-9"></div>
                    <div class="col-md-3">
                        <?= Html::button(Yii::t('app', 'Видалити лот'), ['class' => 'btn btn-default delete_lot']) ?>
                    </div>
                </div>
            </div>
        <?php } else { /* Лот можно отменить */ ?>
            <div class="info-block">
                <div class="row">
                    <div class="col-md-12 cancellations_block">
                        <?
                        if (count($cancellations)) {
                            foreach ($cancellations as $c => $cancellation) {
                                if ($cancellation->cancellationOf && $cancellation->cancellationOf == 'tender') continue;
                                if ($cancellation->cancellationOf == 'lot' && isset($cancellation->relatedLot) && $cancellation->relatedLot == $lot->id) {
                                    echo $this->render('_cancellation', [
                                        'form' => $form,
                                        'template' => $template,
                                        'cancellation' => $cancellation,
                                        'cancellation_of' => 'lot',
                                        'related_lot' => $lot->id,
                                        'k' => $c
                                    ]);
                                }
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        <?php } ?>

    </div>


    <div class="lots_marker">

        <?php
        echo $form->field($lot, '[' . $k . ']id', ['template' => $template])->hiddenInput([
            'name' => 'Tender[lots][' . $k . '][id]',
            'value' => $lot->id ? $lot->id : md5(Yii::$app->security->generateRandomString(32)),
            'class' => 'form-control lot_id'
        ])->label(false);

        echo $form->field($lot, '[' . $k . ']title', ['template' => $template])->textInput([
            'name' => 'Tender[lots][' . $k . '][title]',
            'class' => 'form-control lot_title'
        ]);
        echo $form->field($lot, '[' . $k . ']description', ['template' => $template])->textarea(['name' => 'Tender[lots][' . $k . '][description]']);
        ?>


        <div class="eu_procedure">
            <?php
            echo $form->field($lot, '[' . $k . ']title_en', ['template' => $template])->textInput([
                'name' => 'Tender[lots][' . $k . '][title_en]',
                'class' => 'form-control lot_title'
            ]);
            echo $form->field($lot, '[' . $k . ']description_en', ['template' => $template])->textarea([
                'name' => 'Tender[lots][' . $k . '][description_en]'
            ]);
            ?>
        </div>
        <?php

        //$sel = '<div class="col-md-" style="display: none">'
        //    . Html::dropDownList('Tender[lots]['.$k.'][value][valueAddedTaxIncluded]', (int)$lot->value->valueAddedTaxIncluded, ['0' => 'Без урахування ПДВ','1' => 'З урахуванням ПДВ'], ['class' => 'form-control', 'id' => "tender_type"]) . '
        //            </div>';
        $sel = '';

        //echo $form->field($lot->value, '['.$k.']currency')
        //    ->hiddenInput([
        //        'name' => 'Tender[lots]['.$k.'][value][currency]',
        //        'value' => 'UAH',
        //    ])->label(false);

        ?>
        <div class="guarantee_block_lot">
            <div class="form-group">
                <label
                    class="col-md-3 control-label"><?= Yii::t('app', 'Вид забезпечення тендерних пропозицiй') ?></label>
                <div class="col-md-6">
                    <?= Html::dropDownList(
                        null,
                        ($lot->guarantee->amount) != null ? 1 : 0,
                        ['0' => Yii::t('app', 'Вiдсутнє'), '1' => Yii::t('app', 'Електронна банкiвська гарантiя')],
                        [
                            'class' => 'form-control guarantee_select_lot',
                        ]); ?>
                </div>
            </div>

            <div class="guarantee_amount_lot">
                <?= $form->field($lot->guarantee, 'amount', ['template' => $template])
                    ->textInput(['name' => 'Tender[lots][' . $k . '][guarantee][amount]', 'class' => 'form-control'])
                ?>
            </div>
        </div>

        <div class="lot_amount_block">
            <?= $form->field($lot->value, '[' . $k . ']amount', [
                'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n" . $sel . "\n<div class=\"col-md-3\">{error}</div>"])
                ->textInput(['placeholder' => $lot->value->currency, 'name' => 'Tender[lots][' . $k . '][value][amount]', 'class' => 'form-control lot_amount'])
                ->label(Yii::t('app', 'сумма лота'));

            $tenderLabels = $lot->attributeLabels();
            ?>
            <div class="wrapper_minimalStep">
                <?
                echo $form->field($lot->minimalStep, '[minimalStep[' . $k . ']]amount', ['template' => $template])
                    ->textInput([
                        'placeholder' => $lot->value->currency,
                        'name' => 'Tender[lots][' . $k . '][minimalStep][amount]',
                        'class' => 'form-control lot_step_amount'])
                    ->label(Yii::t('app', 'шаг лота'));

                echo $form->field($lot->minimalStep, '[minimalStep[' . $k . ']]amountProcent', ['template' => $template])
                    ->textInput([
                        'placeholder' => '%',
                        'name' => '',
                        'class' => 'form-control lot_step_amount_procent'])
                    ->label(Yii::t('app', 'шаг лота у вiдсотках'));
                ?>
            </div>
        </div>


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


                //формируем массив из последних версий файлов.
                $realDocuments = \app\models\tenderModels\Document::getLastVersionDocuments($documents);
                $tmp['__EMPTY_DOC__'] = new \app\models\tenderModels\Document();
                $realDocuments = array_merge($tmp, $realDocuments);

                foreach ($realDocuments as $d => $doc) {
                    if ($d === 'iClass') continue;
                    if ($d === '__EMPTY_DOC__') continue;
//                        if ($lot->id == $doc->relatedItem || in_array($doc->relatedItem, $lotItemArrId)) {
                    if (($lot->id != '' && $doc->relatedItem != '') && ($lot->id == $doc->relatedItem || in_array($doc->relatedItem, $lotItemArrId))) {
                        echo '<div class="row margin23 panel-body">';
                        echo $this->render('_document', [
                            'form' => $form,
                            'template' => $template,
                            'documents' => $doc,
                            'k' => $d,
//                            'lot_items' => $lotItemArrId,
//                            'currentLotId' => $lot->id
                        ]);
                        echo '</div>';
                    }

                }
                echo \app\models\DocumentUploadTask::GetUploadedDoc($tenderId, 'lot', 'tender');
            } ?>
        </div>
        <a role="button" class="btn btn-success col-md-2 uploadfile"
           href="javascript:void(0)"><?= Yii::t('app', 'add file') ?></a>
        <div class="clearfix margin_b"></div>


    </div>

    <div class="info-block items_block">
        <?php
        foreach ($items as $i => $item) {
            if ($i === 'iClass') continue;
            if ($k !== '__EMPTY_LOT__' && $i === '__EMPTY_ITEM__') continue;
            if ($k === '__EMPTY_LOT__' && $i !== '__EMPTY_ITEM__') continue;
            if ($i === '__EMPTY_ITEM__') echo '<div id="hidden_item_original" style="display: none;">';


            if ($i === '__EMPTY_ITEM__' || $lot->id == $item->relatedLot) {
                echo $this->render('_item', [
                    'k' => $i,
                    'item' => $item,
                    'form' => $form,
                    'template' => $template,
                    'currentLotId' => $lot->id,
                    'published' => $published
                ]);
            }
            if ($i === '__EMPTY_ITEM__') echo '</div>';

        } ?>
    </div>
    <button type="button" class="btn btn-default add_item"><?= Yii::t('app', 'Додати товар') ?></button>


    <div class="lots_marker">
        <h2><?= Yii::t('app', 'Нецiновi показники лоту') ?></h2>
        <div class="info-block  features_block">

            <?php if ($tenderType == 2) {

                foreach ($features as $f => $feature) {

                    if ($f === 'iClass') continue;
                    if ($f === '__EMPTY_FEATURE__') continue;

                    if (($lot->id != '' && $feature->relatedItem != '') && ($lot->id == $feature->relatedItem || in_array($feature->relatedItem, $lotItemArrId))) {
                        echo $this->render('_feature', [
                            'form' => $form,
                            'template' => $template,
                            'feature' => $feature,
                            'k' => $f
                        ]);
                    }
                }
            } ?>


            <button type="button"
                    class="btn btn-default add_feature margin_b"><?= Yii::t('app', 'Додати показник') ?></button>
        </div>

    </div>


</div>