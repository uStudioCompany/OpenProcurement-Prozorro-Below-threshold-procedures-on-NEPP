<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/**
 * @var $form yii\widgets\ActiveForm
 * @var $template string
 * @var $tenders app\models\Tenders
 * @var $cancellation app\models\tenderModels\Cancellation
 * @var $k int
 * @var $cancellation_of
 * @var $related_lot string
 */

$cancellation_statuses = ['pending' => 'Запит оформляється','active' => 'Скасування активоване']

?>
<div class="cancel_item cancellation" style="border: solid 1px #AAA;">
        <div class="form-group">
            <label class="col-md-3 control-label"><?=$cancellation->getAttributeLabel('reason')?></label>
            <div class="col-md-6"><?=$cancellation->reason?></div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label"><?=$cancellation->getAttributeLabel('date')?></label>
            <div class="col-md-6"><?=$cancellation->date?></div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label"><?=$cancellation->getAttributeLabel('status')?></label>
            <div class="col-md-6"><?=$cancellation_statuses[$cancellation->status]?></div>
        </div>
</div>


