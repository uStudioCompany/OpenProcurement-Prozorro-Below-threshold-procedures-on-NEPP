<?php
use app\models\Tenders;
use app\components\SimpleTenderConvertIn;
use app\models\Companies;
$isTenderOwner = \app\models\Companies::checkCompanyIsTenderOwner($tenders->id, $tenders);
?>
<div class="nav_block">
    <div class="b-sticky-right__trigger"></div>
    <div class="b-sticky-right2__trigger">
        <div class="centered row">
            <h2 tid="status"><?= Yii::t('app', 'tender_'.$tenders->status); ?></h2>
        </div>


        <div class="form-group clearfix">
            <?php
            $count = 0;
            foreach ($tender->questions as $q => $question) {
                if ($q === 'iClass') continue;
//                if ($question['title'] != NULL && $question['answer'] === NULL) {
                    $count++;
//                }
            }

            echo \yii\helpers\Html::a(Yii::t('app', 'questions') . ' (' . $count . ')', Yii::$app->urlManager->createAbsoluteUrl(['/' . \app\models\Companies::getCompanyBusinesType() . '/tender/questions', 'id' => $tenders->id]), [
                'role' => 'button',
                'class' => 'btn btn-success col-md-12'
            ]);
            ?>
        </div>


        <div class="form-group clearfix">
            <?php
            $count = 0;
            foreach ($tender->complaints as $c => $complaint) {
                if ($c === 'iClass') continue;
                if ($complaint['status'] === 'cancelled') continue;
//                if ($complaint['description'] != NULL && $complaint['resolution'] == NULL) {
                    $count++;
//                }
            }
            echo \yii\helpers\Html::a(Yii::t('app', 'complaints') . ' (' . $count . ')', Yii::$app->urlManager->createAbsoluteUrl(['/' . \app\models\Companies::getCompanyBusinesType() . '/tender/complaints', 'id' => $tenders->id]), [
                'role' => 'button',
                'class' => 'btn btn-success col-md-12'
            ]);
            ?>
        </div>
        <hr>

        <div class="form-group">
            <a href="<?= Yii::$app->urlManager->createAbsoluteUrl(['/' . \app\models\Companies::getCompanyBusinesType() .'/tender/view', 'id' => $tenders->id]) ?>"
               class="btn btn-success col-md-12" role="button"><?= Yii::t('app', 'view') ?></a>
        </div>

        <?php if (Yii::$app->user->identity) { ?>

            <?php if (\app\models\Companies::getCompanyBusinesType() == 'buyer') { ?>

                <?php if (Tenders::CheckAllowedStatus($tenders->id, 'update', $tenders) && $isTenderOwner) { ?>
                    <div class="form-group">
                        <a href="<?= Yii::$app->urlManager->createAbsoluteUrl(['/' . \app\models\Companies::getCompanyBusinesType() . '/tender/update', 'id' => $tenders->id]) ?>"
                           class="btn btn-success col-md-12" role="button"><?= Yii::t('app', 'edit') ?></a>
                    </div>
                <?php } ?>

                <?php if ($isTenderOwner && Tenders::CheckAllowedStatus($tenders->id, 'cancelation', $tenders)) { ?>
                    <div class="form-group clearfix">
                        <a href="<?= Yii::$app->urlManager->createAbsoluteUrl(['/' . \app\models\Companies::getCompanyBusinesType() . '/tender/cancel', 'id' => $tenders->id]) ?>"
                           class="btn btn-success col-md-12" role="button"><?= Yii::t('app', 'tender_cancel') ?></a>
                    </div>
                <?php } ?>

                <?php if ($tender->procurementMethod == 'limited' && app\models\Companies::getCompanyBusinesType() == 'buyer' && $tender->status != 'complete' && $isTenderOwner)  { ?>
                    <div class="form-group">
                        <?php
                        $showAwardsFrom = SimpleTenderConvertIn::getLimitedAward($tenders);
                        if (($showAwardsFrom || is_array($showAwardsFrom)) && $tenders->status != 'cancelled') { ?>
                            <a href="<?= Yii::$app->urlManager->createAbsoluteUrl(['/' . \app\models\Companies::getCompanyBusinesType() . '/tender/limitedavards', 'id' => $tenders->id]) ?>"
                               class="btn btn-warning col-md-12"
                               role="button"><?= Yii::t('app', 'Пропозиції') /*Визначити переможця*/ ?></a>
                        <?php } ?>
                    </div>
                <?php } ?>



                <div class="form-group clearfix">
                    <a href="<?= Yii::$app->urlManager->createAbsoluteUrl(['/' . \app\models\Companies::getCompanyBusinesType() . '/tenders/index']) ?>"
                       class="btn btn-success col-md-12" role="button"><?= Yii::t('app', 'Перелiк моїх тендерiв') ?></a>
                </div>

            <?php } ?>


        <?php } ?>


        <?php if (Tenders::CheckAllowedStatus($tenders->id, 'euprocedure', $tenders)) {
            if ($tenders->status == 'active.pre-qualification' || $tenders->status == 'active.pre-qualification.stand-still') {
                ?>
                <a href="<?= Yii::$app->urlManager->createAbsoluteUrl(['/' . \app\models\Companies::getCompanyBusinesType() . '/tender/euprequalification', 'id' => $tenders->id]) ?>"
                   class="btn btn-warning col-md-12" role="button"><?= Yii::t('app', 'Предквалiфiкацiя') ?></a>

                <?php
            }
        }
        ?>


        <?php if (Tenders::CheckAllowedStatus($tenders->id, 'awards', $tenders)) { ?>
            <div class="form-group">
                <a href="<?= Yii::$app->urlManager->createAbsoluteUrl(['/' . \app\models\Companies::getCompanyBusinesType() . '/tender/award', 'id' => $tenders->id]) ?>"
                   class="btn btn-success col-md-12" role="button"><?= Yii::t('app', 'awards') ?></a>
            </div>
        <?php } ?>

        <?php if ($tenders->status == 'complete') { ?>
            <div class="form-group">
                <a href="<?= Yii::$app->urlManager->createAbsoluteUrl(['/tender/protokol', 'id' => $tenders->id]) ?>"
                   class="btn btn-success col-md-12" role="button"><?= Yii::t('app', 'Протокол розкриття пропозицiй') ?></a>
            </div>
        <?php } ?>

        <hr>
        <div class="form-group clearfix">
            <a href="<?= Yii::$app->urlManager->createAbsoluteUrl('tenders/index') ?>"
               class="btn btn-success col-md-12" role="button"><?= Yii::t('app', 'Перелiк тендерiв') ?></a>
        </div>

    </div>
</div>
