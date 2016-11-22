<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use app\models\Companies;

//use Yii;
$isTenderOwner = Companies::checkCompanyIsTenderOwner($tenders->id, $tenders);
?>

<?= Yii::$app->session->getFlash('success'); ?>

<?php if (Yii::$app->session->hasFlash('qualification_complaint_send')) { ?>
    <div class="bs-example">
        <div class="alert alert-success fade in">
            <a href="#" class="close"
               data-dismiss="alert">&times;</a><?= Yii::$app->session->getFlash('qualification_complaint_send'); ?>
        </div>
    </div>
<?php } ?>

<?php
echo $this->render('../small_info_block', [
    'tender' => $tender,
    'tenders' => $tenders
]);
?>


<?php
$data = \yii\helpers\Json::decode($tenders->response);

if (isset($data['data']['bids']) && count($data['data']['bids']) > 0) {
    foreach ($data['data']['bids'] as $k => $bid) {
        if ($bid['status'] == 'pending') {
            $notFifnish = true;
        }
    }
}


echo Html::hiddenInput(null, $tenders->id, [
    'id' => 'tender_id'
]);
?>


<?php

if (isset($data['data']['bids']) && count($data['data']['bids']) > 0) {
    if (count($tender->lots) == 0 || $tender->lots[0]->id == null) { ?>
        <div class="tender-preview m_table-wrap">
            <div class="row">
                <div class="col-md-9">
                    <div class="info-block">
                        <div class="info-block">
                            <h4><?= Yii::t('app', 'Преквалiфiкацiя учасникiв') ?></h4>
                            <div class="info-block qtable">
                                <table class="table table-striped qualification prequalification">
                                    <thead>
                                    <tr>
                                        <th class="hr-dt-table-id" width="5%">№</th>
                                        <th class="hr-dt-table-id" width="45%"><?= Yii::t('app', 'Учасник') ?></th>
                                        <th class="hr-dt-table-id" width="30%"><?= Yii::t('app', 'Документацiя') ?></th>
                                        <th class="hr-dt-table-id" width="*"><?= Yii::t('app', 'status') ?></th>

                                    </tr>
                                    </thead>
                                    <?php
                                    //            Yii::$app->VarDumper->dump($data['data']['bids'], 10, true);die;
                                    ?>
                                    <?php
                                    $count = 0;
                                    foreach ($data['data']['bids'] as $k => $bid) {
                                        if ($bid['status'] == 'invalid') continue; ?>

                                        <tr>
                                            <td colspan="4" class="preq-item" style="padding:0;border:0;">
                                                <? $form = ActiveForm::begin(); ?>

                                                <table class="table table-striped qualification prequalification">
                                                    <tr>
                                                        <td width="5%"><?= $k + 1 ?></td>
                                                        <td width="45%">
                                                        </td>
                                                        <td width="30%">
                                                            <?php
                                                            if (isset($bid['documents'])) {
                                                                foreach ($bid['documents'] as $d => $document) {
                                                                    echo '<a target="_blank" href="' . $document['url'] . '">' . $document['title'] . '</a><br/>';
                                                                }
                                                            }
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <b><?= Html::encode(Yii::t('app', 'qualification_' . $bid['status'])); ?></b>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan=4 class="qualificate makequalify">
                                                            <?php
                                                            foreach ($tender->qualifications as $q => $qualification) {
                                                                if ($qualification->bidID == $bid['id'] && in_array($qualification->status, ['active', 'unsuccessful'])) {
                                                                    if (isset($qualification['documents'])) {
                                                                        foreach ($qualification['documents'] as $d => $document) {
                                                                            echo '<a target="_blank" href="' . $document['url'] . '">' . $document['title'] . '</a><br/>';
                                                                        }
                                                                    }
                                                                }
                                                            }


                                                            foreach ($tender->qualifications as $q => $qualification) {
                                                                if ($qualification->bidID == $bid['id'] && $qualification->status != 'cancelled') {
                                                                    $currentQualificationId = $qualification->id;
                                                                    $currentQualification = $qualification;
//                                            if (isset($qualification->complaints) && count($qualification->complaints)) {
//                                                //echo '['. $currentQualificationId .']';
//                                                $complaints = $this->render('_qualification_complaints', [
//                                                    'qualification' => $qualification,
//                                                    'companyComplaintsIds'=> $companyComplaintsIds,
//                                                    'tenders'=>$tenders,
//                                                    'currentQualificationId'=>$currentQualificationId
//                                                ]);
//                                            } else {
//                                                $complaints = '';
//                                            }
                                                                    break;
                                                                }
                                                            }

                                                            if (isset($currentQualification) && $currentQualification->status != 'pending' && $tenders->status == 'active.pre-qualification.stand-still') {
                                                                echo Html::a(Yii::t('app', 'Оскарження'), //Подати скаргу
                                                                    Yii::$app->urlManager->createAbsoluteUrl([
                                                                        '/' . \app\models\Companies::getCompanyBusinesType() . '/tender/prequalification-complaints',
                                                                        'id' => $tenders->id,
                                                                        'prequalification' => $currentQualificationId,
//                                                        'companyComplaintsIds'=> $companyComplaintsIds,
//                                                        'tenders'=>$tenders
                                                                    ]), [
                                                                        'class' => 'btn btn-warning',// btn-danger
                                                                        'role' => 'button'
                                                                    ]);
                                                            }


                                                            if ($bid['status'] == 'pending' && $isTenderOwner) {
                                                                echo $this->render('_eu_qualification_form', [
                                                                    'tender' => $tender,
                                                                    'qualificationId' => $currentQualificationId,
                                                                    'k' => $count,
                                                                    'form' => $form,
                                                                    'bid' => $bid,
                                                                    'tenders' => $tenders
                                                                ]);
                                                                $count++;
                                                            } elseif (in_array($bid['status'], ['active', 'unsuccessful'])) {
                                                                foreach ($tender->qualifications as $q => $qualification) {
                                                                    if ($qualification->bidID == $bid['id'] && in_array($qualification->status, ['active', 'unsuccessful'])) {
                                                                        echo $form->field($tender->qualifications[$q], '[' . $k . ']id')->hiddenInput()->label(false);
                                                                    }
                                                                }

                                                                if ($tenders->status == 'active.pre-qualification' && Companies::getCompanyBusinesType() == 'buyer' && $isTenderOwner) {
                                                                    echo Html::submitButton(Yii::t('app', 'Вiдмiнити квалiфiкацiю'), ['class' => 'btn btn-danger btn-submitform', 'name' => 'cancel_prequalification']);

                                                                    // если есть удовлетворенные жалобы
                                                                    if (app\models\Complaints::getSatisfiedComplaint($currentQualification)) {
                                                                        echo '&copy';
                                                                    }
                                                                }

                                                            } ?>
                                                        </td>

                                                    </tr>
                                                </table>


                                                <?php ActiveForm::end(); ?>
                                                <div id="sign_block<?= $k ?>" class="e_sign_block"></div>
                                                <?php
                                                //HE выводим жалобы

                                                //                        foreach ($tender->qualifications as $q => $qualification) {
                                                //                            if ($qualification->bidID == $bid['id'] && $qualification->status != 'cancelled') {
                                                //                                if (isset($qualification->complaints) && count($qualification->complaints)) {
                                                //                                    echo $this->render('_qualification_complaints', [
                                                //                                        'qualification' => $qualification,
                                                //                                        'companyComplaintsIds'=> $companyComplaintsIds,
                                                //                                        'tenders'=>$tenders,
                                                //                                        'currentQualificationId'=>$currentQualificationId
                                                //                                    ]);
                                                //                                }
                                                //                            }
                                                //                        }
                                                ?>
                                            </td>
                                        </tr>

                                    <?php } ?>
                                </table>
                                <?
                                if (!isset($notFifnish) && $tenders->status == 'active.pre-qualification' && $isTenderOwner) {
                                    ActiveForm::begin();
                                    echo Html::hiddenInput('tid', $tenders->tender_id);
                                    echo Html::submitButton(Yii::t('app', 'Сформувати протокол прекваліфікації'), ['class' => 'btn btn-success btn-submitform', 'name' => 'prequalification_next_status']);
                                    ActiveForm::end();
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <?php } else { // если мультилот

        $lotArray = [];

        foreach ($data['data']['bids'] as $k => $bid) {
            foreach ($data['data']['qualifications'] as $qualification) {
                if ($qualification['bidID'] == $bid['id'] && $qualification['status'] != 'cancelled') {
                    foreach ($data['data']['lots'] as $lot) {
                        if ($lot['id'] == $qualification['lotID'] && $lot['status'] == 'active') {
                            $lotArray[$lot['id'] . '_' . $lot['title']][] = $qualification;
                        }
                    }

                }
            }
        }

        ?>


        <?php foreach ($lotArray as $key => $qualifications) { ?>

            <div class="tender-preview m_table-wrap">
            <div class="row">
            <div class="col-md-9">
            <div class="info-block">
            <div class="info-block">
            <h4><?= Yii::t('app', 'Преквалiфiкацiя учасникiв') ?></h4>
            <div class="info-block qtable">
            <table class="table table-striped qualification prequalification">
            <thead>
            <tr>
                <th class="hr-dt-table-id" width="3%">№</th>
                <th class="hr-dt-table-id" width="43%"><?= Yii::t('app', 'Назва учасника') ?></th>
                <th class="hr-dt-table-id" width="*"><?= Yii::t('app', 'status') ?></th>
                <th class="hr-dt-table-id" width="15%"></th>
            </tr>
            </thead>
            <h2 style="text-align: center"><?= explode('_', $key)[1] ?></h2>


            <?php foreach ($qualifications as $k => $qualification) { ?>

                <tr>
                    <td colspan="4">
                        <? $form = ActiveForm::begin(); ?>

                        <table width="100%">
                            <tr>
                                <td width="3%"><?= $k + 1 ?></td>
                                <td width="43%">
                                    <!--                                        --><?php //Yii::$app->VarDumper->dump($tender, 10, true);die; ?>
                                    <?php
                                    $bid = \app\models\tenderModels\Bid::getTenderBid($qualification['bidID'], $tender);
                                    if ($bid['status'] != 'invalid' && in_array($tenders->tender_method , ['open_aboveThresholdEU', 'selective_competitiveDialogueEU.stage2', 'open_competitiveDialogueEU', 'open_competitiveDialogueUA']) && $tenders->status != 'active.pre-qualification') { ?>
                                        <div class="row">
                                            <div class="col-md-3"><?= Yii::t('app', 'Организация') ?></div>
                                            <div class="col-md-6">
                                                <b><?= Html::encode($bid['tenderers'][0]['name']) ?></b>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-3"><?= Yii::t('app', 'Контактна особа') ?></div>
                                            <div class="col-md-6">
                                                <b><?= Html::encode($bid['tenderers'][0]['contactPoint']['name']) ?></b>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-3"><?= Yii::t('app', 'Phone') ?></div>
                                            <div class="col-md-6">
                                                <b><?= Html::encode($bid['tenderers'][0]['contactPoint']['telephone']) ?></b>
                                            </div>
                                        </div>
                                    <?php } else {
                                        echo Yii::t('app', 'undefined');
                                    }
                                    if (isset($bid['documents'])) {
                                        foreach ($bid['documents'] as $d => $document) {
                                            echo '<a target="_blank" href="' . $document['url'] . '">' . $document['title'] . '</a><br/>';
                                        }
                                    }
                                    ?>
                                </td>
                                <td width="10%">


                                    <?= Html::encode(Yii::t('app', $qualification['status'])); ?>
                                </td>

                                <td colspan="2" width="*">

                                    <?php

                                    if ($qualification['status'] != 'pending' && $tenders->status == 'active.pre-qualification.stand-still') {
                                        echo Html::a(Yii::t('app', 'Оскарження'), //Подати скаргу
                                            Yii::$app->urlManager->createAbsoluteUrl([
                                                '/' . \app\models\Companies::getCompanyBusinesType() . '/tender/prequalification-complaints',
                                                'id' => $tenders->id,
                                                'prequalification' => $qualification['id'],
                                            ]), [
                                                'class' => 'btn btn-warning',
                                                'role' => 'button'
                                            ]);
                                    }
//

                                    if ($qualification['status'] == 'pending') {

                                        echo $this->render('_eu_qualification_form', [
                                            'tender' => $tender,
                                            'k' => $k,
                                            'form' => $form,
                                            'qualificationId' => $qualification['id'],
                                            'tenders' => $tenders
                                        ]);

                                    } elseif (in_array($qualification['status'], ['active','unsuccessful'])) {
                                        echo Html::hiddenInput('Qualifications[0][id]', $qualification['id']);
                                        if ($tenders->status == 'active.pre-qualification' && Companies::getCompanyBusinesType() == 'buyer' && $isTenderOwner) {
                                            echo Html::submitButton(Yii::t('app', 'Вiдмiнити квалiфiкацiю'), ['class' => 'btn btn-danger btn-submitform', 'name' => 'cancel_prequalification']);

                                            // если есть удовлетворенные жалобы
                                            if(app\models\Complaints::getSatisfiedComplaint($qualification)){
                                                echo '&copy';
                                            }                                        }

                                    } ?>
                                </td>
                            </tr>
                        </table>


                        <?php ActiveForm::end(); ?>
                        <div id="sign_block<?= $key . '_' . $k ?>" class="e_sign_block"></div>
                        <?php
                        // не выводим жалобы
//                        foreach ($tender->qualifications as $q => $qualification) {
//                            if ($qualification->bidID == $bid['id'] && $qualification->status != 'cancelled') {
//                                if (isset($qualification->complaints) && count($qualification->complaints)) {
//                                    echo $this->render('_qualification_complaints', [
//                                        'qualification' => $qualification
//                                    ]);
//                                }
//                            }
//                        }
                        ?>
                    </td>
                </tr>
                <?php
            }
        }
        ?>
        </table>
        </div>
        </div>
        </div>
        </div>
        </div>
        </div>
        <?php
        if (!isset($notFifnish) && $tenders->status == 'active.pre-qualification' && $isTenderOwner) {
            ActiveForm::begin();
            echo Html::hiddenInput('tid', $tenders->tender_id);
            echo Html::submitButton(Yii::t('app', 'Сформувати протокол прекваліфікації'), ['class' => 'btn btn-success btn-submitform', 'name' => 'prequalification_next_status']);
            ActiveForm::end();
        }

    }
}

?>
    <div class="col-md-3">
        <?= $this->render('../view/_nav_block', [
            'tender' => $tender,
            'tenders' => $tenders
        ]); ?>
    </div>

<?php $this->registerJsFile(Url::to('@web/js/prequalification.js'), ['position' => yii\web\View::POS_END, 'depends' => 'yii\web\JqueryAsset']); ?>