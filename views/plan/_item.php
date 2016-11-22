<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

/**
 * @var $item app\models\planModels\Item
 * @var $form yii\widgets\ActiveForm
 * @var $k int
 */

$template = ''.'{label}<div class="col-md-6">{input}</div><div class="col-md-3">{error}</div>';

/*
$enableClientValidation = true;
if ($k === '__EMPTY__') {
    $enableClientValidation = false; } 'enableClientValidation'=>$enableClientValidation
*/
?>
<div class="item">

    <div class="row">
        <div class="col-md-9"><strong> <?=Yii::t('app', 'plan.item')?> № <span class="number_item"><?=$k?></span></strong></div>
        <div class="col-md-3">
            <?= Html::button(Yii::t('app', 'Delete'). ' '. Yii::t('app', 'plan.item'), ['class' => 'btn btn-default delete_item']) ?>
        </div>
    </div>

    <?= $form->field($item, 'id')
        ->hiddenInput(['name' => 'Plan[items]['.$k.'][id]',
            'value' => ( $item->id ? $item->id : md5(Yii::$app->security->generateRandomString(32)) ),
            'rel' => 'hiddenid',
            'class' => 'item_id'])->label(false) ?>

    <?= $form->field($item, '['.$k.']description', ['template' => $template])
        ->textarea([
            'name' => 'Plan[items]['.$k.'][description]',
            'class' => 'form-control item-description']) ?>

    <?php
    $tmp_tpl = ''.'
        {label}
        <div class="col-md-3">{input}</div>
        <div class="col-md-3">'. Html::dropDownList('Plan[items]['.$k.'][unit][code]', $item->unit->code, ArrayHelper::map(\app\models\Unit::find()->all(), 'id', ['name']), ['class' => 'form-control']) .'</div>
        <div class="col-md-3">{error}</div>';
    echo $form->field($item, '['.$k.']quantity', ['template' => $tmp_tpl])
        ->textInput(['name' => 'Plan[items]['.$k.'][quantity]']); ?>

<!--    <div class="row">-->
<!--        <div class="col-md-9"><strong>--><?//= $item->getAttributeLabel('classification'); ?><!--</strong></div>-->
<!--    </div>-->

    <?= $this->render('__classification', [
        'k'=>"[$k]", 'type'=>'cpv', 'form'=>$form,
        'parentId'=>'#classification-cpv-id',
        'classification'=>$item->classification,
        'name'=>"items][$k][classification", ]) ?>


<!--    <div class="row">-->
<!--        <div class="col-md-9"><strong>--><?php //echo $item->getAttributeLabel('additionalClassifications') ?><!--</strong></div>-->
<!--    </div>-->

    <div class="additionalClassifications_block">
        <div class="additionalClassifications_input">
            <?= $this->render('__dk_classification', [
                'k' => "[$k]",
                'type' => $typedk,
                'form' => $form,
                'parentId' => $typedk ? '#classification-'.$typedk.'-id' : '#classification-id',
                'classification' => $item->additionalClassifications[0],
                'name' => "items][$k][additionalClassifications][0",
            ]) ?>
        </div>
    </div>

<!--    <div class="row">-->
<!--        <div class="col-md-9"><strong>Доставка</strong></div>-->
<!--    </div>-->


<!--    --><?//= $form->field($item->deliveryDate, '['.$k.']endDate', ['template' => $template])
//        ->textInput([
//            'name' => 'Plan[items]['.$k.'][deliveryDate][endDate]',
//            'class' => 'form-control picker_date itemdeliverydate-enddate'])
//    ?>

</div>