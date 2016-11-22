<?php
use yii\helpers\Html;
$tender = (object)$tender;

if (isset($tender->cancellations) && count($tender->cancellations)) {

    foreach ($tender->cancellations as $c => $cancellation) {

        if(isset($cancellation->relatedLot)){
            echo '<h3>' . Yii::t('app', 'Скасування лоту') .' '. \app\models\tenderModels\Lot::getLotById($tender, $cancellation->relatedLot)->title.'</h3>';
        }else{
            echo '<h3>' . Yii::t('app', 'Тендер').' ' . $tender->title.' скасовано</h3>';
        }
        ?>



        <?php if (isset($cancellation['reason'])) { ?>
            <div class="row margin_b_20">
                <div class="col-md-3"><?= Yii::t('app', 'Причина скасування') ?></div>
                <div class="col-md-6">
                    <b>
                        <i>   <?= $cancellation['reason'] ?></i></b>
                </div>
            </div>
        <?php } ?>

        <?php
        if (isset($cancellation->documents)) {
            foreach ($cancellation->documents as $d => $docum) {
                echo '<br /><a href="' . $docum['url'] . '" title="' . Yii::t('app', 'protocolTitle') . htmlspecialchars($docum['title']) . '">' . $docum['title'] . ' <span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span></a>';
            }
        }
    }
}
?>