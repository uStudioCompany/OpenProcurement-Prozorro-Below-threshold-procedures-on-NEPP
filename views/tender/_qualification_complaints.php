<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

$this->title = \app\models\Companies::findOne(['id' => Yii::$app->user->identity->company_id])->legalName;
$template = "{label}\n<div class=\"col-md-6\">{input}</div>{error}";

if (Yii::$app->session->hasFlash('message')) { ?>
    <div class="alert alert-success fade in">
        <a href="#" class="close" data-dismiss="alert">&times;</a>
        <?= Yii::$app->session->getFlash('message'); ?>
    </div>
<?php } ?>

<div class="bs-example">
    <div class="alert alert-warning fade in"><a href="#" class="close" data-dismiss="alert">&times;</a>
        <?= Yii::t('app', 'Період подачі вимог/скарг') . ': ' . $currentAwardModel->complaintPeriod->startDate . ' - ' . $currentAwardModel->complaintPeriod->endDate ?>
    </div>
</div>


<?php
echo $this->render('small_info_block', [
    'tender' => $tender,
    'tenders' => $tenders
]);
?>


<div class="tender-questions wrap-questions">
    <input type="hidden" value="<?= $tenders->id ?>" id="tender_id"/>


    <?php
    //    echo $this->render('/site/head', [
    ////        'title' => Html::encode($this->title),
    //        'descr' => 'Скарги та на квалiфiкацiю'
    //    ]);


    $template = "{label}\n<div class=\"col-md-6\">{input}</div>{error}";


    if (\app\models\Companies::getCompanyBusinesType() == 'seller' && (strtotime('now') < strtotime(str_replace('/', '-', $currentAwardModel->complaintPeriod->endDate)))) {
        echo '<div class="complaints_create_btn">';
        $bidExist = true;
        if (in_array($tenders->tender_method, Yii::$app->params['2stage.tender']) && !\app\models\Bids::AccessMembersOfAuction($tenders)) {
            $bidExist = false;
        }
        if ($bidExist && in_array($tenders->tender_method,['open_belowThreshold'])) {
            /** Вимога для Допороги*/
            echo Html::a('Create Claim', ['/seller/tender/complaints-create/', 'tid' => $tenders->id, 'type' => 'award', 'status' => 'claim', 'target_id' => Yii::$app->request->get('qualification')], ['class' => 'btn btn-success']);
        }

        echo '</div>';
    }
    ?>


    <?= $this->render('_award_complaints', [
        'awardComplaints' => $awardComplaints,
        'tendersId' => $tenders->id,
        'awardId' => Yii::$app->request->get('qualification'),
        'companyComplaints' => $companyComplaints,
        'tenders' => $tenders
    ]);
    ?>

</div>

<?php

echo $this->render('view/_nav_block', [
    'tender' => $tender,
    'tenders' => $tenders
]);
?>
<?php $this->registerJsFile(Url::to('@web/js/complaints.js'), ['position' => yii\web\View::POS_END, 'depends' => 'yii\web\JqueryAsset']); ?>
