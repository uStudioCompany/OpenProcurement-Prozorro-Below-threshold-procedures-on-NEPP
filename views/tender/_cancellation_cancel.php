<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/**
 * @var $form yii\widgets\ActiveForm
 * @var $template string
 * @var $tender app\models\tenderModels\Tender
 * @var $tenders app\models\Tenders
 * @var $cancellation app\models\tenderModels\Cancellation
 * @var $k int
 * @var $cancellation_of
 * @var $related_lot string
 */

$cancellation_statuses = ['pending' => 'Запит оформляється', 'active' => 'Скасування активоване'];

$lot_ids = ['tender' => Yii::t('app', 'CancelTheTender')];

if (\app\models\tenderModels\Tender::isCanCancel($tenders, $tender)) {
    $select_lot_ids = ['tender' => Yii::t('app', 'CancelTheTender')];
}

foreach ($tender['lots'] AS $i => $lot) {
    if ($i === 'iClass' || $i === '__EMPTY_LOT__' || !$lot->id) continue;

    $lot_ids[$lot->id] = Yii::t('app', 'CancelLot') . $lot->title;
//    if ($lot->status == 'active') {
    if (\app\models\tenderModels\Tender::isCanCancel($tenders, $tender, $lot)) {
        $select_lot_ids[$lot->id] = Yii::t('app', 'CancelLot') . $lot->title;
    }

}
?>
<div class="cancel_item cancellation">
    <?php
    //    Yii::$app->VarDumper->dump($tenders->tender_method, 10, true);die;
    ?>
    <input type="hidden" class="cancellation_id" value="<?= $cancellation->id ?>" name="Tender[cancellations][id]">
    <input type="hidden" class="cancellation_of" value="<?= $cancellation_of ?>"
           name="Tender[cancellations][cancellationOf]">
    <input type="hidden" class="related_lot" value="<?= $related_lot ?>" name="Tender[cancellations][relatedLot]">
    <? if ($cancellation->status == 'active') { ?>
        <div class="form-group">
            <label class="col-md-3 control-label">Обьект скасування</label>
            <div class="col-md-6"><b
                    class="form_val"><?= $lot_ids[($cancellation->relatedLot ? $cancellation->relatedLot : $cancellation->cancellationOf)] ?></b>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label"><?= $cancellation->getAttributeLabel('reason') ?></label>
            <div class="col-md-6"><b class="form_val"><?= $cancellation->reason ?></b></div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label"><?= $cancellation->getAttributeLabel('date') ?></label>
            <div class="col-md-6"><b class="form_val"><?= $cancellation->date ?></b></div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label"><?= $cancellation->getAttributeLabel('status') ?></label>
            <div class="col-md-6"><b class="form_val"><?= $cancellation_statuses[$cancellation->status] ?></b></div>
        </div>
        <?
        foreach ($cancellation->documents as $d => $doc) {
            if ($d === 'iClass' || $d === '__EMPTY_DOC__') continue; ?>
            <div class="form-group">
                <label class="col-md-3 control-label"><?= $cancellation->getAttributeLabel('documents') ?></label>
                <div class="col-md-6"><b class="form_val"><a href="<?= $doc->url ?>"><?= $doc->title ?></a></b></div>
            </div>
        <? } ?>
    <? } else { ?>

        <?= $form->field($cancellation,'relatedLot')->dropDownList($select_lot_ids,
            [
                'name' => 'Tender[cancellations][relatedLot]',
                'class' => 'form-control',
                'prompt'=>'Не вибрано'
            ]); ?>
<!--        <div class="form-group">-->
<!--            <label class="col-md-3 control-label">Обьект скасування</label>-->
<!--            <div class="col-md-6">-->
<!--                --><?//= $form->field($cancellation,'relatedLot')->dropDownList($select_lot_ids, ['class' => 'form-control', 'prompt'=>'Не вибрано']); ?>
<!--                --><?//= Html::dropDownList('Tender[cancellations][relatedLot]', null, $select_lot_ids, ['class' => 'form-control', 'prompt'=>'Не вибрано']); ?>
<!--            </div>-->
<!--        </div>-->

        <?= $form->field($cancellation, 'reason', ['template' => $template])->textarea(['name' => 'Tender[cancellations][reason]']); ?>

        <?php if (strpos($tenders->tender_method, 'above') != false) { ?>
            <div class="form-group">
                <label class="col-md-3 control-label">Тип скасування</label>
                <div class="col-md-6">
                    <?= Html::dropDownList('Tender[cancellations][reasonType]', null,
                        [
                            'cancelled' => 'Торги вiдмiненi', 'unsuccessful' => 'Торги не вiдбулися'

                        ], ['class' => 'form-control']); ?>
                </div>
            </div>
        <? } ?>
        <?//= $form->field($cancellation, 'date', ['template' => $template])->textInput(['name' => 'Tender[cancellations][date]', 'class' => 'form-control picker']) ?>

        <?= Html::hiddenInput('Tender[cancellations][date]', date('d/m/Y', strtotime('now')))?>


        <input type="hidden" class="form-control real_name" name="Tender[cancellations][status]" value="pending">

        <div class="row">
            <div class="col-md-5"><h4><?=Yii::t('app','Тендерна документацiя')?></h4></div>
            <div class="col-md-4" style="text-align: right;">
                <div class="__add_file_wrapper">
<!--                    <button type="button" class="btn btn-default uploadfile">--><?//=Yii::t('app','add_file')?><!--</button>-->
                    <a role="button" class="btn btn-success uploadfile" href="javascript:void(0)"><?= Yii::t('app', 'add file') ?></a>
                </div>
            </div>
        </div>

        <div class="info-block _grey document_block">
            <?php
            $cancellation->documents['__EMPTY_DOC__'] = new \app\models\tenderModels\Document();
            if (isset($cancellation->documents[0]) && $cancellation->documents[0]['id'] == null) {
                unset($cancellation->documents[0]);
            }

            foreach ($cancellation->documents as $d => $doc) {
                if ($d === 'iClass') continue;
                if ($d === '__EMPTY_DOC__') {
                    echo '<div id="hidden_document_original" class="row margin23 panel-body" style="display:none">';
                }
                //else {echo '<div class="row margin23 panel-body">';}

                echo $this->render('_document_cancel', [
                    'form' => $form,
                    //'template' => $template,
                    'documents' => $doc,
                    'k' => $d,
                    'lot_ids' => $lot_ids,
                    'cancellations' => '[cancellations]',
                    'currentLotId' => ''
                ]);
                if ($d === '__EMPTY_DOC__') {
                    echo '</div>';
                }
            }
            ?>
        </div>
    <? } ?>
</div>
<hr/>

<?php
$this->registerJs(
    "$('.picker').datetimepicker({
        locale: $('#current_locale').val(),
        format: 'DD/MM/YYYY',
        minDate: 'now'
    });"
    , yii\web\View::POS_END);
?>


