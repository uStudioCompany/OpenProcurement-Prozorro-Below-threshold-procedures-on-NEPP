<?php

use yii\helpers\Html;

?>
<div class="tender-preview wrap-preview">
    <div class="info-block margin_b">

        <div class="row tender-id-box">
            <div class="col-md-3"><?= Yii::t('app', 'title') ?></div>
            <div class="col-md-9"><b tid="tenderID"><?= Html::encode(@$tenders->title) ?></b></div>
        </div>
        <div class="row tender-id-box">
            <div class="col-md-3"><?= Yii::t('app', 'tender description') ?></div>
            <div class="col-md-9"><b><?= Html::encode(@$tenders->description) ?></b></div>
        </div>
        <div class="row tender-id-box">
            <div class="col-md-3"><?= Yii::t('app', 'budget') ?></div>
            <div class="col-md-9"><b>
                    <?= Html::encode($tender->value->amount . ' ' . $tender->value->currency) ?>
                    <?= Html::encode(\app\models\tenderModels\Value::getPDV()[(int)$tender->value->valueAddedTaxIncluded]) ?>
                </b></div>
        </div>

    </div>
</div>