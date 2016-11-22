<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\models\tenderModels\Cancellation;

/**
 * @var $tender app\models\tenderModels\Tender
 * @var $tenderId int
 * @var $tenders app\models\Tenders
 * @var $published bool
 */

//$this->title = \app\models\Companies::findOne(['id' => Yii::$app->user->identity->company_id])->legalName;
$fieldLabel = $tender->attributeLabels();
if ($tenders->status == 'draft'){
    $draft = true;
} else {
    $draft = false;
}
?>
<div class="tender-preview wrap-preview">

    <?php
    echo $this->render('/site/head', [
        'title' => $this->title,
        'descr' => ''
    ]);
    ?>

    <?php
    if($m = Yii::$app->request->get('messageid')) {
        if (Yii::$app->session->hasFlash('bid_send'.$m)) { ?>
            <div class="alert alert-success fade in">
                <a href="#" class="close" data-dismiss="alert">&times;</a>
                <?= Yii::$app->session->getFlash('bid_send'.$m); ?>
            </div>
        <?php }
    }
    ?>

    <?php
    if (Yii::$app->session->hasFlash('tender_added')) { ?>
        <div class="alert alert-success fade in">
            <a href="#" class="close" data-dismiss="alert">&times;</a>
            <?= Yii::$app->session->getFlash('tender_added'); ?>
        </div>
    <?php } ?>

    <?php
    if (Yii::$app->session->hasFlash('draft_success')) { ?>
        <div class="alert alert-success fade in">
            <a href="#" class="close" data-dismiss="alert">&times;</a>
            <?= Yii::$app->session->getFlash('draft_success'); ?>
        </div>
    <?php } ?>

    <?php
    if (Yii::$app->session->hasFlash('update_success')) { ?>
        <div class="alert alert-success fade in">
            <a href="#" class="close" data-dismiss="alert">&times;</a>
            <?= Yii::$app->session->getFlash('update_success'); ?>
        </div>
    <?php } ?>

    <?php if (Yii::$app->session->hasFlash('message')) { ?>
        <div class="bs-example">
            <div class="alert alert-success fade in">
                <a href="#" class="close" data-dismiss="alert">&times;</a><?= Yii::$app->session->getFlash('message'); ?>
            </div>
        </div>
    <?php } ?>

    <?php if (Yii::$app->session->hasFlash('message_error')) { ?>
        <div class="bs-example">
            <div class="alert alert-danger fade in"><a href="#" class="close" data-dismiss="alert">&times;</a><?= Yii::$app->session->getFlash('message_error'); ?>
            </div>
        </div>
    <?php } ?>

<!--    --><?php //if (Yii::$app->session->hasFlash('bid_send')) { ?>
<!--        <div class="bs-example">-->
<!--            <div class="alert alert-success fade in">-->
<!--                <a href="#" class="close" data-dismiss="alert">&times;</a>--><?//= Yii::$app->session->getFlash('bid_send'); ?>
<!--            </div>-->
<!--        </div>-->
<!--    --><?php //} ?>

    <?php if (Yii::$app->session->hasFlash('bid_confirm')) { ?>
        <div class="bs-example">
            <div class="alert alert-success fade in">
                <a href="#" class="close" data-dismiss="alert">&times;</a><?= Yii::$app->session->getFlash('bid_confirm'); ?>
            </div>
        </div>
    <?php } ?>

    <?php if (Yii::$app->session->hasFlash('qualification_complaint_send')) { ?>
        <div class="bs-example">
            <div class="alert alert-success fade in">
                <a href="#" class="close" data-dismiss="alert">&times;</a><?= Yii::$app->session->getFlash('qualification_complaint_send'); ?>
            </div>
        </div>
    <?php } ?>

    <?php if (Yii::$app->session->hasFlash('cancel_qualification_complaint_send')) { ?>
        <div class="bs-example">
            <div class="alert alert-success fade in">
                <a href="#" class="close" data-dismiss="alert">&times;</a><?= Yii::$app->session->getFlash('cancel_qualification_complaint_send'); ?>
            </div>
        </div>
    <?php } ?>


    <?php
    if (isset($tender->cancellations) && count($tender->cancellations)) {
        if ($tender->status == 'cancelled' || $tender->status == 'unsuccessful') { ?>
            <div class="alert alert-danger">
                <h3><?= Cancellation::getReasonType()[$tender->status] ?></h3>
                <?php if ($tender->cancellations[0]->cancellationOf == 'tender') {
                    if ($tender->status == 'cancelled') { ?>
                        <div class="row margin_b_20">
                            <div class="col-md-3"><?= Yii::t('app', 'Причина скасування') ?></div>
                            <div class="col-md-6"><b><?= $tender->cancellations[0]->reason ?></b></div>
                        </div>
                    <?php }
                } elseif ($tender->cancellations[0]->cancellationOf != 'tender') {

                    foreach ($tender->cancellations as $c => $cancellation) {
                        if ($cancellation->status != 'active') continue;
                        if (isset($cancellation->documents)) {
                            foreach ($cancellation->documents as $d => $docum) {
                                echo '<br /><a href="' . $docum['url'] . '" title="' . Yii::t('app', 'protocolTitle') . htmlspecialchars($docum['title']) . '">' . $docum['title'] . ' <span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span></a>';
                            }
                        }
                    }
                } ?>
            </div>
            <?php
        }
    }
    ?>


    <input type="hidden" id="tender_id" value="<?=$tenders->id?>">

    <?php
    if ($tenders->ecp == 1) { ?>
        <div class="bs-example">
            <div class="alert alert-warning fade in">
                <a href="#" class="close"
                   data-dismiss="alert">&times;</a><?= Yii::t('app', 'Скріплено ЕЦП') ?>
            </div>
        </div>
    <?php } else { ?>
        <div class="bs-example">
            <div class="alert alert-warning fade in">
                <a href="#" class="close"
                   data-dismiss="alert">&times;</a><?= Yii::t('app', 'Електронний цифровий підпис відсутній') ?>
            </div>
        </div>

    <?php } ?>

<hr/>
    <input type="hidden" id="current_locale" value="<?= substr(Yii::$app->language, 0, 2) ?>">

    <div class="info-block margin_b">

        <div class="row tender-id-box">
            <div class="col-md-3"><?=Yii::t('app','TenderID') ?></div>
            <div class="col-md-9"><b tid="tenderID"><?= @$tender->tenderID ?></b></div>
        </div>
        <div class="row tender-id-box">
            <div class="col-md-3">ID</div>
            <div class="col-md-9"><b><?= @$tender->id ?></b></div>
        </div>

        <h2><?= Yii::t('app', 'Замовник') ?></h2>

        <div class="row one-row-style">
            <div class="col-md-3">Назва</div>
            <div class="col-md-9"><b tid="procuringEntity.name"><?= @$tender->procuringEntity->name ?></b></div>
        </div>


        <h2><?= Yii::t('app', 'Параметри закупiвлi') ?></h2>

        <div class="row one-row-style">
            <div class="col-md-3"><?= $fieldLabel['title'] ?></div>
            <div class="col-md-6">
                <b><i tid="title"><?= Html::encode($tender->title) ?></i></b>

            </div>
        </div>

        <div class="row one-row-style">
            <div class="col-md-3"><?= $fieldLabel['description'] ?></div>
            <div class="col-md-6">
                <b> <i tid="description"> <?= Html::encode($tender->description) ?></i></b>
            </div>
        </div>

        <div class="row one-row-style">
            <div class="col-md-3"><?= Yii::t('app', 'Процедура закупiвлi') ?></div>
            <div class="col-md-6">
                <b>
                    <i>
                        <input type="hidden" class="view_t_type" value="<?= $tenders->tender_method ?>">
                        <?= \app\models\Tenders::getAllTenderMethod()[$tenders->tender_method]; ?>
                    </i>
                </b>
            </div>
        </div>

<!--        <div class="row one-row-style">-->
<!--            <div class="col-md-3">--><?//= Yii::t('app', 'Тип оголошення') ?><!--</div>-->
<!--            <div class="col-md-6">-->
<!--                <b>-->
<!--                    <i>-->
<!--                        <input type="hidden" class="view_t_type" value="--><?//= $tenders->tender_type ?><!--">-->
<!--                        --><?//= \app\models\Tenders::getTenderType()[$tenders->tender_type]; ?>
<!--                    </i>-->
<!--                </b>-->
<!--            </div>-->
<!--        </div>-->

        <?php if ($tender->guarantee->amount != null) { ?>
            <div class="row one-row-style">
                <div class="col-md-3"><?= Yii::t('app', 'Банкiвськi гарантii') ?></div>
                <div class="col-md-6">
                    <b>
                        <i>
                            <?= Html::encode($tender->guarantee->amount . ' ' . $tender->guarantee->currency); ?>
                        </i>
                    </b>
                </div>
            </div>
        <?php } ?>


        <div class="simple_only">

            <div class="row one-row-style">
                <div class="col-md-3">
                    <?php
                    echo Html::encode($fieldLabel['value']);
                    ?>
                </div>
                <div class="col-md-6">
                    <b> <i tid="value.amount">
                            <?= Html::encode($tender->value->amount . ' ' . $tender->value->currency) ?>
                            <?= Html::encode(\app\models\tenderModels\Value::getPDV()[(int)$tender->value->valueAddedTaxIncluded]) ?>
                        </i>
                    </b>
                </div>
            </div>


            <? if ($tenders->tender_method === 'limited_reporting' || $tenders->tender_method === 'limited_negotiation' || $tenders->tender_method === 'limited_negotiation.quick') { ?>

            <? } else { ?>
                <div class="row one-row-style">
                    <div class="col-md-3">
                        <?= $fieldLabel['minimalStep'] ?>
                    </div>
                    <div class="col-md-6">
                        <b><i tid="minimalStep.amount"><?= Html::encode($tender->minimalStep->amount . ' ' . $tender->minimalStep->currency) ?></i></b>
                    </div>
                </div>
            <? } ?>

        </div>
    </div>

    <div class="info-block">


        <? if ($tenders->tender_method === 'limited_reporting' || $tenders->tender_method === 'limited_negotiation' || $tenders->tender_method === 'limited_negotiation.quick') { ?>

        <? } else { ?>
            <h2><?= Yii::t('app', 'Нецiновi показники тендеру') ?></h2>
            <div class="info-block  features_block">

                <?php
                if (/*!$draft &&*/ empty($tender->features)) {
                    echo '<h4>' . Yii::t('app', 'Дані не було додано') . '</h4>';
                } else {
                    if ($tenders->tender_type == 2) {

                        foreach ($tender->features as $f => $feature) {
                            if ($f === 'iClass') continue;
                            if ($f === '__EMPTY_FEATURE__') continue;
                            if ($f === '__EMPTY_FEATURE__' || $feature->relatedItem == '' || $feature->featureOf == 'tenderer' || $feature->featureOf == 'tender') {
                                echo $this->render('view/_feature', [
                                    'feature' => $feature,
                                    'k' => $f,
                                    'fieldLabel' => $fieldLabel,
                                    'tender' => $tender
                                ]);
                            }
                        }
                    } else {
                        foreach ($tender->features as $f => $feature) {
                            if ($f === 'iClass') continue;
                            if ($f === '__EMPTY_FEATURE__') continue;
                            echo $this->render('view/_feature', [
                                'feature' => $feature,
                                'k' => $f,
                                'fieldLabel' => $fieldLabel,
                                'tender' => $tender
                            ]);
                        }
                    }
                }
                ?>

            </div>
        <? } ?>


        <h2><?= Yii::t('app', 'Тендерна документацiя') ?></h2>

        <div class="info-block document_block">

            <?php

            //формируем массив из последних версий файлов.
            $realDocuments = \app\models\tenderModels\Document::getLastVersionDocuments($tender->documents);
            if ($draft){
                $realDocuments = $tender->documents;
            }

            if ($tenders->tender_type == 1) {
                foreach ($realDocuments as $d => $doc) {
                    if ($d === 'iClass') continue;
                    if ($d === '__EMPTY_DOC__') {
                        echo '<div id="hidden_document_original" class="row margin23 panel-body" style="display: none">';
                    } else {
                        echo '<div class="row margin23 panel-body">';
                    }
                    echo $this->render('view/_document', [
                        'documents' => $doc,
                        'k' => $d,
                        'lot_items' => [],
                        'currentLotId' => '',
                        'tender' => $tender
                    ]);
                    if ($d === '__EMPTY_DOC__') {
                        echo '</div>';
                    } else {
                        echo '</div>';
                    }
                }
            } else if ($tenders->tender_type == 2) {
                $checkDoc = false;
                foreach ($realDocuments as $d => $doc) {
                    if ($d === 'iClass') continue;
                    if ($d === '__EMPTY_DOC__') {
                        echo '<div id="hidden_document_original"class="row margin23 panel-body" style="display: none">';
                    } elseif ($doc->relatedItem == 'tender') {
                        echo '<div class="row margin23 panel-body">';
                    }
                    if ($d === '__EMPTY_DOC__' || $doc->documentOf == 'tender') {
                        $checkDoc = true;
                        echo $this->render('view/_document', [
                            'documents' => $doc,
                            'k' => $d,
                            'lot_items' => [],
                            'currentLotId' => '',
                            'tender' => $tender
                        ]);
                    }
                    if ($d === '__EMPTY_DOC__') {
                        echo '</div>';
                    } elseif ($doc->relatedItem == 'tender') {
                        echo '</div>';
                    }
                }
            }
            if (/*$published*/true) {
                $uploadedDoc = \app\models\DocumentUploadTask::GetUploadedDoc($tenderId, 'tender', ['tender', 'item']);
                echo $uploadedDoc;
                $uploadedDoc = $uploadedDoc == '' ? false : true;
                $checkDoc = isset($checkDoc) ? $checkDoc : false;
                if (/*!$draft && */empty($realDocuments) && !$uploadedDoc && !$checkDoc) {
                    echo '<h4>' . Yii::t('app', 'Дані не було додано') . '</h4>';
                }
            }

            ?>

        </div>


        <div class="info-block">

            <? if (count($tender->lots)) { ?>
                <h2><?= Yii::t('app', 'Специфiкацiя закупiвлi') ?></h2>
            <? } ?>
            <div class="info-block">

                <?php
                if (!count($tender->lots)) {
                    $tender->lots = [null];
                }

                foreach ($tender->lots as $k => $lot) {

                    echo $this->render('view/_lot', [
                        'k' => $k,
                        'lot' => $lot,
                        'items' => $tender->items,
                        'features' => $tender->features,
                        'documents' => $tender->documents,
                        'tenderType' => $tenders->tender_type,
                        'published' => $published,
                        'tender' => $tender,
                        'tenderId' => $tenderId,
                        'cancellations' => $tender->cancellations,
                        'draft' => $draft,
                    ]);
                } ?>
            </div>

        </div>


        <? if ($tenders->tender_method === 'limited_reporting' || $tenders->tender_method === 'limited_negotiation' || $tenders->tender_method === 'limited_negotiation.quick') { ?>

        <? } else { ?>
            <div class="info-block">
                <h2><?= Yii::t('app', 'Дати та термiни') ?></h2>

                <div class="row one-row-style">
                    <div class="col-md-3"><?= $tender->enquiryPeriod->getAttributeLabel('startDate') ?></div>
                    <div class="col-md-6">
                        <b>
                            <i tid="enquiryPeriod.startDate">  <?= $tender->enquiryPeriod->startDate ? Html::encode($tender->enquiryPeriod->startDate) : '' ?></i></b>

                    </div>
                </div>

                <div class="row one-row-style">
                    <div class="col-md-3"> <?= $tender->enquiryPeriod->getAttributeLabel('endDate') ?></div>
                    <div class="col-md-6">
                        <b>
                            <i tid="enquiryPeriod.endDate">  <?= $tender->enquiryPeriod->endDate ? Html::encode($tender->enquiryPeriod->endDate) : '' ?></i></b>
                    </div>
                </div>

                <div class="row one-row-style">
                    <div class="col-md-3">  <?= $tender->tenderPeriod->getAttributeLabel('startDate') ?></div>
                    <div class="col-md-6">
                        <b>
                            <i tid="tenderPeriod.startDate">  <?= $tender->tenderPeriod->startDate ? Html::encode($tender->tenderPeriod->startDate) : '' ?></i></b>
                    </div>
                </div>

                <div class="row one-row-style">
                    <div class="col-md-3">  <?= $tender->tenderPeriod->getAttributeLabel('endDate') ?></div>
                    <div class="col-md-6">
                        <b>
                            <i tid="tenderPeriod.endDate"><?= $tender->tenderPeriod->endDate ? Html::encode($tender->tenderPeriod->endDate) : '' ?></i></b>
                    </div>
                </div>

            </div>
        <? } ?>


        <div class="info-block">
            <h2><?= Yii::t('app', 'Контактна особа') ?></h2>

            <div class="contact_group_wrapper">

                <div class="row one-row-style">
                    <div class="col-md-3"><?= $tender->procuringEntity->contactPoint->getAttributeLabel('name') ?></div>
                    <div class="col-md-6">
                        <b> <i>   <?= Html::encode($tender->procuringEntity->contactPoint->name) ?></i></b><br/>
                        <b> <i>   <?= Html::encode($tender->procuringEntity->contactPoint->name_en) ?></i></b>
                    </div>
                </div>

                <div class="row one-row-style">
                    <div
                        class="col-md-3"><?= $tender->procuringEntity->contactPoint->getAttributeLabel('email') ?></div>
                    <div class="col-md-6">
                        <b> <i>  <?= Html::encode($tender->procuringEntity->contactPoint->email) ?></i></b>
                    </div>
                </div>

                <div class="row one-row-style">
                    <div
                        class="col-md-3"><?= $tender->procuringEntity->contactPoint->getAttributeLabel('telephone') ?></div>
                    <div class="col-md-6">
                        <b> <i> <?= Html::encode($tender->procuringEntity->contactPoint->telephone) ?></i></b>
                    </div>
                </div>
            </div>
        </div>

        <?php if(count($tender->procuringEntity->additionalContactPoints) > 0) { ?>


            <div class="info-block">
                <h2><?= Yii::t('app', 'Додатковi контактнi особи') ?></h2>
                <div class="contact_group_wrapper">
                <?php foreach ($tender->procuringEntity->additionalContactPoints as $additionalContactPoint) : ?>





                    <div class="row one-row-style">
                        <div class="col-md-3"><?= $additionalContactPoint->getAttributeLabel('name') ?></div>
                        <div class="col-md-6">
                            <b> <i>   <?= Html::encode($additionalContactPoint->name) ?></i></b><br/>
                            <b> <i>   <?= Html::encode($additionalContactPoint->name_en) ?></i></b>
                        </div>
                    </div>

<!--                    <div class="row one-row-style">-->
<!--                        <div-->
<!--                            class="col-md-3">--><?//= $additionalContactPoint->getAttributeLabel('email') ?><!--</div>-->
<!--                        <div class="col-md-6">-->
<!--                            <b> <i>  --><?//= Html::encode($additionalContactPoint->email) ?><!--</i></b>-->
<!--                        </div>-->
<!--                    </div>-->

                    <div class="row one-row-style">
                        <div
                            class="col-md-3"><?= $additionalContactPoint->getAttributeLabel('telephone') ?></div>
                        <div class="col-md-6">
                            <b> <i> <?= Html::encode($additionalContactPoint->telephone) ?></i></b>
                        </div>
                    </div>

                    <div class="row one-row-style">
                        <div
                            class="col-md-3"><?= $additionalContactPoint->getAttributeLabel('availableLanguage') ?></div>
                        <div class="col-md-6">
                            <b> <i> <?= Html::encode($additionalContactPoint->availableLanguage) ?></i></b>
                        </div>
                    </div>



                <?php endforeach; ?>
            </div>
            </div>

        <?php } ?>







    </div>
</div>
<!--</div>-->
<?php

echo $this->render('view/_nav_block', [
    'tender' => $tender,
    'tenders' => $tenders
]);


    if (\app\models\Companies::getCompanyBusinesType() == 'seller' && $tenders->status == 'active.tendering') {

        if (\app\models\Companies::checkAllowedCompanyStatusToWork(Yii::$app->user->identity->company_id)) {
            $bidIn1Stage = true;
            if (in_array($tenders->tender_method, Yii::$app->params['2stage.tender']) && !\app\models\Bids::checkFirstStageOnBidByCompany($tenders)) {
                $bidIn1Stage = false;
                echo '<h2>' . Yii::t('app', 'Ваша компанія не приймала участь у першому етапі') . '</h2>';
            }
            if($bid && $bidIn1Stage) {
                echo '<h1>' . Yii::t('app', 'Ставки') . '</h1>';
                echo $this->render('bids/_bid_all', [
                    'tender' => $tender,
                    'tenders' => $tenders,
                    'bid' => $bid,
                    'userBid' => $userBid
                ]);
            }
        }else{
            echo '<h1>' . Yii::t('app', 'Для подання пропозицiй Вам необхiдно пройти авторизацiю пiдприємства') . '</h1>';
        }

    }


    if ($tenders->status == 'active.auction') {

        if ($tenders->tender_type == 2 && isset($tender->lots)) {
            foreach ($tender->lots as $t => $lot) {
                if(in_array($lot->status, ['unsuccessful', 'cancelled'])) continue;
//                Yii::$app->VarDumper->dump($lot, 10, true);die;

                if (isset($lot->auctionPeriod->startDate)) {
                    echo '<h1 style="color: red">Старт аукцiону: ' . $lot->auctionPeriod->startDate . '</h1>';
                }

                if (isset($lot->auctionUrl)) {
                    echo '<h2>Для перегляду '.$lot->title.' перейдiть ' . Html::a('Аукцiон', $lot->auctionUrl,[
                        'target'=>'_blank'
                        ]) . '</h2>';
                }

                if($lot->title){
                    if (\app\models\Companies::checkCompanyIsSeller() && !is_null($bid->id) && in_array($bid->id, \app\models\Companies::getSellerCompanyBids($tenderId, 'array'))) {
                        echo '<h2>Для участi у ' . $lot->title . ' перейдiть ' . Html::a('Аукцiон', 'javascript:void(0)', [
                                'class' => 'auction_seller_url',
                                'type' => 'multi',
                                'lotId' => $lot->id
                            ]) . '</h2>';
                    }
                }
            }
        }elseif($tenders->tender_type == 1){

            if (isset($tender->auctionPeriod->startDate)) {
                echo '<h1 style="color: red">Старт аукцiону: ' . $tender->auctionPeriod->startDate . '</h1>';
            }

            if (isset($tender->auctionUrl)) {
                echo '<h2>Для перегляду перейдiть ' . Html::a('Аукцiон', $tender->auctionUrl,[
                        'target'=>'_blank'
                    ]) . '</h2>';
            }
            if (\app\models\Companies::checkCompanyIsSeller() && !is_null($bid->id) && in_array($bid->id, \app\models\Companies::getSellerCompanyBids($tenderId, 'array'))) {
                echo '<h2>Для участi у ' . $Tenders->title . ' перейдiть ' . Html::a('Аукцiон', 'javascript:void(0)', [
                        'class' => 'auction_seller_url'
                    ]) . '</h2>';
            }
        }


    }



if ($tenders->status == 'active.stage2.pending' && \app\models\Companies::checkCompanyIsTenderOwner($tenders->id, $tenders)) {
    $form = \yii\widgets\ActiveForm::begin();
    echo Html::hiddenInput('tid', $tenders->tender_id);
    echo Html::submitButton(Yii::t('app', 'Перейти на другий етап'), ['class' => 'btn btn-danger col-md-12 margin_t_20', 'name' => 'stage2_waiting']);
    \yii\widgets\ActiveForm::end();

}elseif($tenders->status == 'complete' && isset($tender->stage2TenderID)) {
    $newId = \app\models\Tenders::find()->where(['tender_id'=>$tender->stage2TenderID])->asArray()->one();
    if  (isset($newId)) {
        ?>
        <a href="<?= Yii::$app->urlManager->createAbsoluteUrl(['/' . \app\models\Companies::getCompanyBusinesType() . '/tender/view', 'id' => $newId['id']]) ?>"
           class="btn btn-success col-md-12" role="button"><?= Yii::t('app', 'go_to_two_stage') ?></a>
        <?php
    }
}elseif($tenders->status =='draft.stage2' && \app\models\Companies::checkCompanyIsTenderOwner($tenders->id, $tenders)){
    $form = \yii\widgets\ActiveForm::begin();
    echo Html::hiddenInput('tid', $tenders->tender_id);
    echo Html::submitButton(Yii::t('app', 'Активувати'), ['class' => 'btn btn-danger col-md-12 margin_t_20', 'name' => 'stage2_active_tendering']);
    \yii\widgets\ActiveForm::end();
}

    ?>



<style>
    .form-horizontal .control-label {
        margin-bottom: 0;
        padding-top: 0;
        text-align: left;
    }
</style>
<? $this->registerJsFile(Url::to('@web/js/bids.js'), ['position' => yii\web\View::POS_END, 'depends' => 'yii\web\JqueryAsset']); ?>
