<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;

/**
 * @var $tendersId int
 * @var $tender object
 * @var $k int
 * @var $awardId string
 * @var $award app\models\tenderModels\Award
 * @var $bid_status string
 * @var $lot app\models\tenderModels\Lot
 * @var $bid app\models\tenderModels\Bid
 */

$isTenderOwner = \app\models\Companies::checkCompanyIsTenderOwner($tendersId, $tenders);
?>


<tr>
    <td><?= ($k + 1) ?></td>
    <? if ($bid_status === 'pending') { ?>
        <td><?= htmlspecialchars($bid->tenderers[0]->name) ?></td>
    <? } else { ?>
        <td><?= htmlspecialchars($bid->tenderers[0]->name) ?></td>
    <? } ?>
    <td>
        <?
        if (!$lot) {
            echo '<b>' . $bid->value->amount . '</b> ' . Yii::t('app', $bid->value->currency);
        } else {
            foreach ($bid->lotValues AS $key => $lotValues) {
                if ($lot->id === $lotValues->relatedLot) {
//                    echo $lot->id;
                    echo '<b>' . $lotValues->value->amount . '</b> ' . Yii::t('app', $lotValues->value->currency);
                }
            }
        }
        if (count($bid->_counted_history)) { ?>
            <br/>
            <span class="counted_amount"><?= $bid->_counted_amount ?></span>
            <div class="counted_history">
                <h2 class="text-center"><?= Yii::t('app', 'Feature window title') ?></h2>
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th class="hr-dt-table-id" width="*"><?= Yii::t('app', 'Feature title') ?></th>
                        <th class="hr-dt-table-id" width="10%"><?= Yii::t('app', 'Feature value') ?></th>
                    </tr>
                    </thead>
                    <? foreach ($bid->_counted_history AS $row) { ?>
                        <tr>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['value'] * 100) ?>%</td>
                        </tr>
                    <? } ?>
                </table>
            </div>
        <? } ?>
    </td>
    <td>
        <?
        $text = \app\models\tenderModels\Bid::getTextActiveStatusBid($tender, $bid, $tenders->tender_type);
        if (in_array($tenders->tender_method, ['limited_reporting', 'limited_negotiation', 'limited_negotiation.quick'])) {
            echo Yii::t('app', ((is_string($text)) ? $text : $text[$bid->id]) . 'AwardLimitedStatus_' . $bid_status);
        } else {
            echo Yii::t('app', ((is_string($text)) ? $text : $text[$bid->id]) . 'AwardStatus_' . $bid_status);
        }

        if ($award && ($award->status === 'unsuccessful' || $award->status === 'active') &&
        isset($award->documents) && count($award->documents)
        )
        ?>
    </td>
</tr>
<tr class="qualdocs">
    <td></td>
    <td colspan=3>
        <?
        //    Yii::$app->VarDumper->dump($award->complaints, 10, true);die;
        echo '<div class="document_block"><h4>Документи кваліфікації</h4>';
        if (isset($award->documents) && count($award->documents)){
            foreach ($award->documents as $docum) {
                echo '<div class="qualdoc"><a href="' . $docum['url'] . '" title="' . Yii::t('app', 'protocolTitle') . htmlspecialchars($docum['title']) . '">' . $docum['title'] . ' <span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span></a></div>';
            }
            echo '</div>';
        }
        ?>
    </td>
</tr>


<tr class="biddocs">
    <td></td>
    <td colspan="4">
        <div class="bid_document_block">
            <? if (count($bid->documents) > 0) {
                if ($bid->documents[0]->id) { ?>
                    <h4><?= Yii::t('app', 'Документи пропозиції') ?></h4>
                    <table class="table table-striped">
                        <?
                        foreach ($bid->documents AS $key => $doc) {
                            echo $this->render('_bid_documents', [
                                'k' => $key,
                                'doc' => $doc]);
                        }
                        ?>
                    </table>
                <? }
            } ?>

            <? if (count($bid->eligibilityDocuments) > 0) {
                if ($bid->eligibilityDocuments[0]->id) { ?>
                    <h4><?= Yii::t('app', 'Документи для критерiїв прийнятностi') ?></h4>
                    <table class="table table-striped">
                        <? foreach ($bid->eligibilityDocuments AS $key => $doc) {
                            echo $this->render('_bid_documents', [
                                'k' => $key,
                                'doc' => $doc]);
                        }
                        ?>
                    </table>
                <? }
            } ?>

            <? if (count($bid->financialDocuments) > 0) {
                if ($bid->financialDocuments[0]->id) { ?>
                    <h4><?= Yii::t('app', 'Фiнансовi та квалiфiкацiйнi документи') ?></h4>
                    <table class="table table-striped">
                        <? foreach ($bid->financialDocuments AS $key => $doc) {
                            echo $this->render('_bid_documents', [
                                'k' => $key,
                                'doc' => $doc]);
                        }
                        ?>
                    </table>
                <? }
            } ?>

            <? if (count($bid->qualificationDocuments) > 0) {
                if ($bid->qualificationDocuments[0]->id) { ?>
                    <h4><?= Yii::t('app', 'Квалiфiкацiйнi документи') ?></h4>
                    <table class="table table-striped">
                        <? foreach ($bid->qualificationDocuments AS $key => $doc) {
                            echo $this->render('_bid_documents', [
                                'k' => $key,
                                'doc' => $doc]);
                        }
                        ?>
                    </table>
                <? }
            } ?>



        </div>
    </td>
</tr>
<?

if ($bid_status === 'pending') { ?>
    <tr class="biddocs">
        <td></td>
        <td colspan="3">
            <div class="document_block">

<!--                --><?// if (count($bid->documents) > 0) {
//                    if ($bid->documents[0]->id) { ?>
<!--                        <h4>--><?//= Yii::t('app', 'Документи пропозиції') ?><!--</h4>-->
<!--                        <table class="table table-striped">-->
<!--                            --><?//
//                            foreach ($bid->documents AS $key => $doc) {
//                                echo $this->render('_bid_documents', [
//                                    'k' => $key,
//                                    'doc' => $doc]);
//                            }
//                            ?>
<!--                        </table>-->
<!--                    --><?// }
//                } ?>
<!---->
<!--                --><?// if (count($bid->eligibilityDocuments) > 0) {
//                    if ($bid->eligibilityDocuments[0]->id) { ?>
<!--                        <h4>--><?//= Yii::t('app', 'Документи для критерiїв прийнятностi') ?><!--</h4>-->
<!--                        <table class="table table-striped">-->
<!--                            --><?// foreach ($bid->eligibilityDocuments AS $key => $doc) {
//                                echo $this->render('_bid_documents', [
//                                    'k' => $key,
//                                    'doc' => $doc]);
//                            }
//                            ?>
<!--                        </table>-->
<!--                    --><?// }
//                } ?>
<!---->
<!--                --><?// if (count($bid->financialDocuments) > 0) {
//                    if ($bid->financialDocuments[0]->id) { ?>
<!--                        <h4>--><?//= Yii::t('app', 'Фiнансовi та квалiфiкацiйнi документи') ?><!--</h4>-->
<!--                        <table class="table table-striped">-->
<!--                            --><?// foreach ($bid->financialDocuments AS $key => $doc) {
//                                echo $this->render('_bid_documents', [
//                                    'k' => $key,
//                                    'doc' => $doc]);
//                            }
//                            ?>
<!--                        </table>-->
<!--                    --><?// }
//                } ?>
<!---->
<!--                --><?// if (count($bid->qualificationDocuments) > 0) {
//                    if ($bid->qualificationDocuments[0]->id) { ?>
<!--                        <h4>--><?//= Yii::t('app', 'Квалiфiкацiйнi документи') ?><!--</h4>-->
<!--                        <table class="table table-striped">-->
<!--                            --><?// foreach ($bid->qualificationDocuments AS $key => $doc) {
//                                echo $this->render('_bid_documents', [
//                                    'k' => $key,
//                                    'doc' => $doc]);
//                            }
//                            ?>
<!--                        </table>-->
<!--                    --><?// }
//                } ?>

                <?php
                if (\app\models\Companies::getCompanyBusinesType() == 'buyer' && $isTenderOwner) {



                        echo '<div class="makequalify">';
//                        Yii::$app->VarDumper->dump($k, 10, true);
                        echo $this->render('_award_qualification_form', [
                            'tender' => $tender,
                            'tenders' => $tenders,
                            'tendersId' => $tendersId,
                            'k' => $k,
                            'awardId' => $awardId,
                            'currentAward' => $currentAward
                        ]);
                        echo '</div>';




                    $this->registerJsFile(Url::to('@web/js/prequalification.js'), ['position' => yii\web\View::POS_END, 'depends' => 'yii\web\JqueryAsset']);

                }

                ?>
            </div>
        </td>
    </tr>
<? } else if ($bid_status === 'active' && $tender->status != 'complete') { ?>

    <tr class="bidclaims">
        <td colspan="4">
            <?php if (\app\models\Companies::getCompanyBusinesType() == 'buyer' && $isTenderOwner) { ?>

                <?php
                if (
                        //\app\models\tenderModels\Award::checkAllowedCancelAward($award) &&
                        \app\models\tenderModels\Lot::getLotById($tender, $lot->id)->status != 'complete'
                ) {
                    if ( //\app\models\tenderModels\Award::isCancelableAward($tenders, $award) && // Во всех процедурах у заказчика должны быть возможность отменить свое решение о признании победителем до момента активации контракта
                        true
                    ) {
                        ?>
                        <button class="btn btn-danger btn-award" data-tender_id="<?= $tendersId ?>"
                                data-award_id="<?= $awardId ?>"
                                data-type="cancelled"><?= Yii::t('app', 'award_cancel') ?></button>
                    <?php }
                }
            }



            if($tenders->tender_method != 'limited_reporting') {
                echo Html::a(Yii::t('app', 'Оскарження результатів кваліфікації'),
                    Yii::$app->urlManager->createAbsoluteUrl([
                        \app\models\Companies::getCompanyBusinesType() . '/tender/qualification-complaints',
                        'id' => $tenders->id,
                        'qualification' => $award->id,
//                                                        'companyComplaintsIds'=> $companyComplaintsIds,
//                                                        'tenders'=>$tenders
                    ]), [
                        'class' => 'btn btn-danger',
                        'role' => 'button',
                    ]);
            }

            ?>
        </td>
    </tr>



<? } elseif ($bid_status == 'unsuccessful'){ ?>
<tr class="bidclaims">
    <td colspan="4">

        <?php
        //Yii::$app->VarDumper->dump($tender, 10, true);die;
        if (\app\models\tenderModels\Award::isCancelableAward($tenders, $award) ) { /* ---------------- */ ?>

            <button class="btn btn-danger btn-award" data-tender_id="<?= $tendersId ?>"
                    data-award_id="<?= $awardId ?>"
                    data-type="cancelled"><?= Yii::t('app', 'award_cancel') ?></button>

        <?php } ?>

        <?php if (!in_array($tenders->tender_method, ['limited_negotiation', 'limited_negotiation.quick'])) {
            echo Html::a(Yii::t('app', 'Оскарження результатів кваліфікації'),
                Yii::$app->urlManager->createAbsoluteUrl([
                    \app\models\Companies::getCompanyBusinesType() . '/tender/qualification-complaints',
                    'id' => $tenders->id,
                    'qualification' => $award->id,
                ]), [
                    'class' => 'btn btn-danger',
                    'role' => 'button',
//                        'target'=>'_blank'
                ]);
        }
        }


        //Yii::$app->VarDumper->dump($awardComplaints, 10, true);die;

        //if (isset($awardComplaints[0]) && $awardComplaints[0]->id != '') {


        //    echo $this->render('_qualification_complaints', [
        //        'qualification' => $award
        //    ]);

        //            echo $this->render('_award_complaints', [
        //                'awardComplaints' => $awardComplaints,
        //                'tendersId' => $tendersId,
        //                'awardId' => $awardId,
        //            ]);
        //}
        ?>


        <?php //if (($bid_status == 'active') && (strtotime('now') > strtotime(str_replace('/', '-', $awardCompaintsPeriod['endDate'])))) { ?>
        <?php if (($bid_status == 'active')) {

            echo $this->render('_contract_documents', [
                'tendersId' => $tendersId,
                'awardId' => $awardId,
                'tender' => $tender,
                'tenders' => $tenders,
                'awardCompaintsPeriod' => $awardCompaintsPeriod
            ]);
        } ?>
