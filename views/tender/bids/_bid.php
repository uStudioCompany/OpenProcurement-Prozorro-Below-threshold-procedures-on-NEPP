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
                if ($key === 'iClass') continue;
                if ($lot->id === $lotValues->relatedLot) {
                    echo '<b>' . $lotValues->value->amount . '</b> ' . Yii::t('app', $lotValues->value->currency);
                }
            }
        }
        if (count($bid->_counted_history)) { ?>
            <span class="counted_amount"><?= $bid->_counted_amount ?></span>
            <div class="counted_history">
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

            echo Yii::t('app', 'AwardLimitedStatus_' . $bid_status);



        if ($award && ($award->status === 'unsuccessful' || $award->status === 'active') &&
            isset($award->documents) && count($award->documents)
        ) {
            foreach ($award->documents as $docum) {
                echo '<br /><a href="' . $docum['url'] . '" title="' . Yii::t('app', 'protocolTitle') . htmlspecialchars($docum['title']) . '">' . $docum['title'] . ' <span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span></a>';
            }
        }
        ?>
    </td>
</tr>

<? if ($bid_status === 'pending') { ?>
    <tr>
	<td></td>
        <td colspan="3">
            <div class="document_block">

                <?
                if (count($bid->documents) > 0) {
                    if ($bid->documents[0]->id) { ?>
                        <h4><?= Yii::t('app', 'Документи') ?></h4>
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

                <?php
                if (\app\models\Companies::getCompanyBusinesType() == 'buyer' && \app\models\Companies::checkCompanyIsTenderOwner($tendersId)) {



                        echo $this->render('_award_qualification_form', [
                            'tender' => $tender,
                            'tenders' => $tenders,
                            'tendersId' => $tendersId,
                            'k' => $k,
                            'awardId' => $awardId,
                            'currentAward'=>$currentAward
                        ]);





                    $this->registerJsFile(Url::to('@web/js/prequalification.js'), ['position' => yii\web\View::POS_END, 'depends' => 'yii\web\JqueryAsset']);

                }

                ?>
            </div>
        </td>
    </tr>
<? } else if ($bid_status === 'active' && $tender->status != 'complete') { ?>
    <tr class="">
        <td>&nbsp;</td>
        <td colspan="3">
            <?php if (\app\models\Companies::getCompanyBusinesType() == 'buyer' && \app\models\Companies::checkCompanyIsTenderOwner($tenders->id, $tenders)) { ?>

                <?php if(\app\models\tenderModels\Award::checkAllowedCancelAward($award) && \app\models\tenderModels\Lot::getLotById($tender, $lot->id)->status != 'complete'){
                    if(!in_array($tenders->tender_method, ['limited_reporting', 'limited_negotiation','limited_negotiation.quick'])){
                        ?>
                    <button class="btn btn-danger btn-award" data-tender_id="<?= $tendersId ?>"
                            data-award_id="<?= $awardId ?>"
                            data-type="cancelled"><?= Yii::t('app', 'award_cancel') ?></button>
                    <?php }
                }
            }
                echo Html::a(Yii::t('app', 'Скарги'),
                    Yii::$app->urlManager->createAbsoluteUrl([
                        \app\models\Companies::getCompanyBusinesType().'/tender/qualification-complaints',
                        'id' => $tenders->id,
                        'qualification' => $award->id,
//                                                        'companyComplaintsIds'=> $companyComplaintsIds,
//                                                        'tenders'=>$tenders
                    ]), [
                        'class' => 'btn btn-danger',
                        'role' => 'button',
                    ]);

            ?>
        </td>
    </tr>
<? } elseif($bid_status == 'unsuccessful'){ ?>
<tr class="">
        <td>&nbsp;</td>
        <td colspan="3">

    <?= Html::a(Yii::t('app', 'Скарги'),
        Yii::$app->urlManager->createAbsoluteUrl([
            \app\models\Companies::getCompanyBusinesType().'/tender/qualification-complaints',
            'id' => $tenders->id,
            'qualification' => $award->id,
        ]), [
            'class' => 'btn btn-danger',
            'role' => 'button',
//                        'target'=>'_blank'
        ]);
    }




//if (isset($awardComplaints[0]) && $awardComplaints[0]->id != '' && $awardComplaints[0]->status != 'draft') {
//
//    if (strtotime('now') < strtotime(str_replace('/', '-', $awardCompaintsPeriod['endDate']))) {
//    if (true) {
//    echo $this->render('_award_complaints', [
//        'awardComplaints' => $awardComplaints,
//        'tendersId' => $tendersId,
//        'awardId' => $awardId,
//    ]);
//    }
//}
//?>


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
