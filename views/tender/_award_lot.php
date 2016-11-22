<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

/**
 * @var $k int
 * @var $tender app\models\tenderModels\Tender
 * @var $tendersId int
 * @var $lot_status string
 * @var $lot app\models\tenderModels\Lot
 * @var $title string
 * @var $description string
 */
?>

<div class="info-block qtable">
    <input type="hidden" id="tender_id" value="<?=$tendersId?>">
<!--    <h3>--><?//= Html::encode($title)?><!--</h3>-->
<!--    <p>--><?//= Html::encode($description)?><!--</p>-->
<!--    <p>Status: --><?//= Yii::t('app','TenderStatus_'.$lot_status)?><!--</p>-->

    <?
    $award_bid_ids = [];
//    foreach ($tender->awards as $key=>$award) {
////        Yii::$app->VarDumper->dump($award, 10, true);
////        Yii::$app->VarDumper->dump($lot, 10, true);die;
//        if(isset($award->lotID) && $award->lotID == $lot->id) {
//            $award_bid_ids[$award->bid_id][] = $award; //здесь аварды победители
//        }else{
//            $award_bid_ids[$award->bid_id][] = $award; //для limited
//        }
//    }

    foreach ($tender->awards as $key=>$award) {
        if($award->lotID == $lot->id) {
            $award_bid_ids[$award->bid_id][] = $award; //здесь аварды победители
        }
    }
    $sorted_bids = \app\components\ApiHelper::sortBids($tender,$lot, $tenders);
    if ( count($sorted_bids) ) {
    ?>

    <table class="table table-striped qualification">
        <thead>
        <tr>
            <th class="hr-dt-table-id" width="5%"><?=Yii::t('app','Bid #')?></th>
            <th class="hr-dt-table-id" width="45%"><?=Yii::t('app','Bid Compamy name')?></th>
            <th class="hr-dt-table-id" width="30%"><?=Yii::t('app','Bid amount')?></th>
            <th class="hr-dt-table-id" width="*"><?=Yii::t('app','Bid status')?></th>
        </tr>
        </thead>

        <?
        echo '<h3>'.$lot->title.'</h3>';
        foreach ($sorted_bids as $key => $bid) {
            if($bid->status == 'unsuccessful') continue;//здесь биды после активации контракта.

            if ($lot) {
                $skip = true;
                foreach ($bid->lotValues AS $key_lot=>$lotValue) {

                    if ($lot->id === $lotValue->relatedLot) {
                        $skip = false;
                    }
                }
                if ($skip) continue;
            }
            $last_award = null;
            $awardComplaints = '';
            if (isset($award_bid_ids[$bid->id])) {
                $last_award = $award_bid_ids[$bid->id][count($award_bid_ids[$bid->id]) - 1];

                $awardComplaints = $award_bid_ids[$bid->id][count($award_bid_ids[$bid->id]) - 1]->complaints;
                $awardComplaintsPeriod = $award_bid_ids[$bid->id][count($award_bid_ids[$bid->id]) - 1]->complaintPeriod;
            }

            echo $this->render('_bid', [
                'k' => $key,
                'bid' => $bid,
                'lot' => $lot,
                'bid_status' => $last_award ? $last_award->status : '',
                'awardId' => $last_award ? $last_award->id : '',
                'award' => $last_award,
                'tendersId' => $tendersId,
                'awardComplaints'=>$awardComplaints,
                'awardCompaintsPeriod'=>isset($awardComplaintsPeriod) ? $awardComplaintsPeriod : '',
                'tender'=>$tender,
                'tenders'=>$tenders,
                'currentAward'=>$last_award
                ]);
        }

        ?>
    </table>
    <? } else { ?>
        <h3><?= $lot->title ?></h3>
        <div class="bs-example"><div class="alert alert-warning fade in"><?= Yii::t('app','NO Bids!'); ?></div></div>
    <? } ?>
</div>
